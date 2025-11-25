<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Requests\User\StoreContactUsRequest;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Requests\User\UploadProfilePictureRequest;
use App\Http\Resources\Model\UserResource;
use App\Http\Traits\GeneralTrait;
use App\Models\Agent;
use App\Models\ContactAgentForm;
use App\Models\ContactForm;
use App\Models\ContactUsForm;
use App\Models\Property;
use App\Models\Subscribe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use GeneralTrait;
    public function profile(Request $request)
    {
        try {
            $user = $request->user();
            $data['user'] = new UserResource($user);
            return $this->apiResponse($data);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            $user = $request->user();

            // Update the validation based on the fields that exist.
            $validator = Validator::make($request->all(), [
                'name'      => 'sometimes|required|string',
                'phone'     => 'sometimes|required|string',
                'gender'    => 'sometimes|nullable|in:male,female,other',
                'birthday'  => 'sometimes|nullable|date',
                'address'   => 'sometimes|nullable|string',
                'image_profile' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            ]);

            if ($validator->fails()) {
                return $this->requiredField($validator->errors()->first());
            }

            $dataToUpdate = $request->only([
                'name', 'phone', 'gender', 'birthday', 'address'
            ]);

            if ($request->hasFile('image_profile')) {
                $imagePath = $request->file('image_profile')->store('image_profile', 'public');
                $dataToUpdate['image_profile'] = $imagePath;
            }

            $user->update($dataToUpdate);

            $data['user'] = new UserResource($user);
            return $this->apiResponse($data);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function uploadProfilePicture(Request $request)
    {
        try {
            $user = $request->user();

            $validator = Validator::make($request->all(), [
                'image_profile' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
            ]);

            if ($validator->fails()) {
                return $this->requiredField($validator->errors()->first());
            }

            $imagePath = $request->file('image_profile')->store('image_profile', 'public');
            $user->image_profile = $imagePath;
            $user->save();

            $data['user'] = new UserResource($user);
            return $this->apiResponse($data);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function changePassword(Request $request)
    {
        try {
            $user = $request->user();

            $validator = Validator::make($request->all(), [
                'old_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return $this->requiredField($validator->errors()->first());
            }

            if (!Hash::check($request->old_password, $user->password)) {
                return $this->requiredField('The old password is incorrect.');
            }

            $user->password = Hash::make($request->new_password);
            $user->save();

            $data['message'] = 'Password updated successfully';
            return $this->apiResponse($data);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }



    public function submitContactForm(Request $request)
    {
        try {
            $data = $request->validate([
                'name'     => 'required|string|max:255',
                'email'    => 'required|email|max:255',
                'phone'    => 'nullable|string|max:20',
                'message'  => 'required|string',
                'language' => 'nullable|in:en,ar',
            ]);

            $subject = $data['language'] === 'ar'
                ? 'New message from the contact form'
                : 'New Contact Form Submission';

            ContactForm::create($data);

            $this->send_email('emails.contact', 'admin@example.com', $subject, $data);

            return $this->apiResponse([
                'message' => 'Your message was submitted successfully.',
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name'  => 'nullable|string|max:255',
                'email' => 'required|email|max:255|unique:subscribes,email',
            ]);

            Subscribe::create($data);

            return $this->apiResponse([
                'message' => 'Thank you for subscribing to our newsletter.',
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }



    public function submitContactAgentForm(Request $request)
    {
        try {
            $data = $request->validate([
                'first_name'  => 'required|string|max:255',
                'second_name' => 'nullable|string|max:255',
                'phone_one'   => 'required|string|max:20',
                'phone_two'   => 'nullable|string|max:20',
                'message'     => 'required|string',
                'agent_id'    => 'required|exists:agents,id',
                'property_id' => 'nullable|exists:properties,id',
            ]);


            $contact = ContactAgentForm::create($data);


            $agent = Agent::find($data['agent_id']);
            $property = null;

            if (!empty($data['property_id'])) {
                $property = Property::find($data['property_id']);
            }

            // Send the email to the agent.
            if ($agent && $agent->email) {
                $this->send_email(
                    'emails.contact_agent',
                    $agent->email,
                    'New Message from Contact Agent Form',
                    array_merge($data, ['property' => $property])
                );
            }

            return $this->apiResponse([
                'message' => 'Your message was sent to the agent successfully.',
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }


}
