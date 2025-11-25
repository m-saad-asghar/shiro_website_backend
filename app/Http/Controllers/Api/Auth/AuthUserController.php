<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\Model\UserResource;
use App\Http\Traits\GeneralTrait;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Stripe\Customer;
use Stripe\Stripe;

class AuthUserController extends Controller
{
    use GeneralTrait;

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'nullable|required_without:register_id',
            'register_id' => 'nullable|required_without:password|string'
        ]);

        if ($validator->fails()) {
            return $this->requiredField($validator->errors()->first());
        }

        try {
            $user = User::where('email', $request->input('email'))->first();

            if (!$user) {
                return $this->apiResponse(null, false, 'Invalid email or password.', 401);
            }

            if ($request->filled('password')) {
                if (!Hash::check($request->input('password'), $user->password)) {
                    return $this->apiResponse(null, false, 'Invalid email or password.', 401);
                }
            }

            if ($request->filled('register_id')) {
                if ($user->register_id !== $request->input('register_id')) {
                    return $this->apiResponse(null, false, 'Invalid registration method.', 401);
                }
            }


            if ($user->status === false) {
                // Delete any old unverified OTPs.
                \App\Models\UserOtp::where('email', $user->email)
                    ->where('type', 'register_email')
                    ->where('isVerified', false)
                    ->forceDelete();

                $otp = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
                
                \App\Models\UserOtp::create([
                    'user_id' => $user->id,
                    'otp' => $otp,
                    'isVerified' => false,
                    'type' => 'register_email',
                    'email' => $user->email,
                ]);

                $emailSent = $this->send_email('emails.verify-email', $user->email, 'Verify Your Email - Shiro Properties', [
                    'user' => $user,
                    'token' => $otp,
                ]);

                // Secondary email sending method if the first one fails.
                if (!$emailSent) {
                    $simpleMessage = "Hello {$user->name},\n\nYour verification code is: {$otp}\n\nPlease enter this code to verify your email.\n\nThank you,\nShiro Properties Team";
                    
                    $emailSent = $this->send_simple_email(
                        $user->email, 
                        'Email Verification - Shiro Properties', 
                        $simpleMessage
                    );
                }

                if (!$emailSent) {
                    \Log::error('Both email methods failed for user: ' . $user->email);
                    return $this->apiResponse(null, false, 'Failed to send verification email. Please check your email address or try again later.', 503);
                }

                return $this->apiResponse(null, false, 'Account not verified. A new verification code has been sent to your email.', 406);
            }

            $data['user'] = new UserResource($user);
            $data['token'] = $user->createToken('MyApp')->plainTextToken;

            return $this->apiResponse($data, true, null, 200);
        } catch (\Exception $ex) {
            return $this->apiResponse(null, false, $ex->getMessage(), 500);
        }
    }


    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name'        => 'required|string|max:255',
                'email'       => 'required|email|max:255',
                'register_id' => 'nullable|string',
                'register_method' => 'nullable|string|in:manual,google,facebook,apple',
                'password'    => [
                    'nullable',
                    'required_without:register_id',
                    'confirmed',
                    Password::min(8)
                        ->mixedCase()
                        ->numbers()
                        ->symbols()
                        ->uncompromised()
                ],
                'address'     => 'nullable|string|max:500',
                'birthday'    => 'required|date|before:today',
                'phone'       => 'required|string|regex:/^[+]?[0-9\s\-\(\)]+$/|min:10|max:15',
                'gender'      => 'nullable|in:male,female,other',
            ]);

            if ($validator->fails()) {
                return $this->requiredField($validator->errors()->first());
            }

            $registerMethod = $request->register_method ?? 'manual';

            $user = User::where('email', $request->email)->first();


            if ($user && !$user->status) {
                $user->update([
                    'name'            => $request->name,
                    'register_id'     => $request->register_id,
                    'register_method' => $registerMethod,
                    'password'        => $request->password ? Hash::make($request->password) : $user->password,
                    'address'         => $request->address,
                    'birthday'        => $request->birthday,
                    'phone'           => $request->phone,
                    'gender'          => $request->gender,

                ]);

                //payment

                if ($registerMethod === 'manual') {
                    $otp = str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT);

                    \App\Models\UserOtp::create([
                        'user_id'    => $user->id,
                        'otp'        => $otp,
                        'isVerified' => false,
                        'type'       => 'register_email',
                        'email'      => $user->email,

                    ]);

                    $emailSent = $this->send_email('emails.verify-email', $user->email, 'Verify Your Email - Shiro Properties', [
                        'user'  => $user,
                        'token' => $otp,
                    ]);

                    // Secondary email sending method if the first one fails.
                    if (!$emailSent) {
                        $simpleMessage = "Hello {$user->name},\n\nYour verification code is: {$otp}\n\nPlease enter this code to verify your email.\n\nThank you,\nShiro Properties Team";
                        
                        $emailSent = $this->send_simple_email(
                            $user->email, 
                            'Email Verification - Shiro Properties', 
                            $simpleMessage
                        );
                    }

                    if (!$emailSent) {
                        \Log::error('Both email methods failed for existing user: ' . $user->email);
                        return $this->apiResponse(null, false, 'Failed to send verification email. Please check your email address or try again later.', 503);
                    }
                }

                return $this->apiResponse([
                    'user' => new UserResource($user),
                    'message' => 'OTP sent to your email for verification (updated)',
                ]);
            }


            $user = User::create([
                'name'            => $request->name,
                'email'           => $request->email,
                'register_id'     => $request->register_id,
                'register_method' => $registerMethod,
                'password'        => $request->password ? Hash::make($request->password) : null,
                'address'         => $request->address,
                'birthday'        => $request->birthday,
                'phone'           => $request->phone,
                'gender'          => $request->gender,
                'status'          => $registerMethod === 'manual' ? false : true,

            ]);
//            Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
//
//            $full_name = $user->name;
//            $phone = $user->phone_number;
//
//
//            $customer = Customer::create([
//                'name' => $full_name,
//                // 'email' => $email,
//                'phone' => $phone,
//                // 'payment_method' => $paymentMethodId,
//            ]);
//
//            $stripe_customer_id = $customer->id;
//
//            $user->update(['customer_id' => $stripe_customer_id]);

            if ($registerMethod !== 'manual') {
                $token = $user->createToken('MyApp')->plainTextToken;

                return $this->apiResponse([
                    'user' => new UserResource($user),
                    'token' => $token,
                    'message' => 'Registered via social successfully.',
                ]);
            }


            // Send OTP for verification.
            $otp = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);

            \App\Models\UserOtp::create([
                'user_id'    => $user->id,
                'otp'        => $otp,
                'isVerified' => false,
                'type'       => 'register_email',
                'email'      => $user->email,
            ]);

            $emailSent = $this->send_email('emails.verify-email', $user->email, 'Verify Your Email - Shiro Properties', [
                'user'  => $user,
                'token' => $otp,
            ]);

                // Secondary email sending method if the first one fails.
            if (!$emailSent) {
                $simpleMessage = "Hello {$user->name},\n\nYour verification code is: {$otp}\n\nPlease enter this code to verify your email.\n\nThank you,\nShiro Properties Team";
                
                $emailSent = $this->send_simple_email(
                    $user->email, 
                    'Email Verification - Shiro Properties', 
                    $simpleMessage
                );
            }

            if (!$emailSent) {
                \Log::error('Both email methods failed for new user: ' . $user->email);
                return $this->apiResponse(null, false, 'Failed to send verification email. Please check your email address or try again later.', 503);
            }

            return $this->apiResponse([
                'user' => new UserResource($user),
                'message' => 'OTP sent to your email for verification',
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }



    public function checkEmail(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|unique:users,email',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse(['message' => $validator->errors()->first()], false, 'Invalid email', 400);
            }

            return $this->apiResponse(['message' => 'Email is valid', 'email' => $request->email], true, null, 200);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function checkEmailExists(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse(null, false, $validator->errors()->first(), 400);
            }

            $emailExists = User::where('email', $request->input('email'))->exists();

            if ($emailExists) {
                return $this->apiResponse(['exists' => true], false, 'Email already exists.', 409);
            } else {
                return $this->apiResponse(['exists' => false], true, 'Email does not exist.', 200);
            }
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}
