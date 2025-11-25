<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\Model\UserResource;
use App\Http\Traits\GeneralTrait;
use App\Models\User;
use App\Models\UserOtp;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class ResetPasswordController extends Controller
{

    use GeneralTrait;


    public function forgot_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return $this->requiredField($validator->errors()->first());
        }

        try {
            $user = User::where('email', $request->email)
                ->where('status', 1)
                ->first();

            if (!$user) {
                return $this->notFoundResponse('User not found or account not verified');
            }

            UserOtp::where('email', $user->email)
                ->where('type', 'forget_password')
                ->where('isVerified', false)
                ->forceDelete();

            $randomToken = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);

            $userOtp = UserOtp::create([
                'user_id'  => $user->id,
                'otp' => $randomToken,
                'isVerified'  => false,
                'type'     => 'forget_password',
                'email'    => $user->email,
            ]);

            $templateName = "emails.forgot-password";
            $email = $user->email;
            $data = [
                'user'  => $user,
                'token' => $randomToken,
            ];

            $result = $this->send_email($templateName, $email, 'Password Reset - Shiro Properties', $data);

            // Secondary email sending method if the first one fails.
            if (!$result) {
                $simpleMessage = "Hello {$user->name},\n\nYour password reset code is: {$randomToken}\n\nThis code is valid for 30 minutes.\n\nIf you didn't request this, please ignore this email.\n\nThank you,\nShiro Properties Team";
                
                $result = $this->send_simple_email(
                    $user->email, 
                    'Password Reset - Shiro Properties', 
                    $simpleMessage
                );
            }

            if ($result === true) {
                $dataResponse['user'] = new UserResource($user);
                return $this->apiResponse($dataResponse, true, 'Password reset code sent to your email successfully.');
            } else {
                \Log::error('Both email methods failed for password reset: ' . $user->email);
                return $this->apiResponse(null, false, 'Failed to send password reset email. Please check your email address and try again later.', 503);
            }
        } catch (\Exception $ex) {
            return $this->apiResponse(null, false, $ex->getMessage(), 500);
        }
    }

    public function checkToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'token' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return $this->requiredField($validator->errors()->first());
        }

        try {
            $userOtp = UserOtp::where('email', $request->email)
                ->where('otp', $request->token)
                ->where('type', 'forget_password')
                ->where('isVerified', false)
                ->latest('created_at')
                ->first();

            if (!$userOtp) {
                return $this->apiResponse(null, false, 'Invalid or expired reset token.', 400);
            }

            $createdAt = $userOtp->created_at;
            $expiresAt = $createdAt->copy()->addMinutes(30);

            if (now()->greaterThan($expiresAt)) {
                return $this->apiResponse(null, false, 'Reset token has expired. Please request a new one.', 400);
            }

            return $this->apiResponse(['message' => 'Token is valid.'], true, null, 200);
        } catch (\Exception $ex) {
            return $this->apiResponse(null, false, $ex->getMessage(), 500);
        }
    }


    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'token' => 'required|string|size:6',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
            ],
        ]);

        if ($validator->fails()) {
            return $this->requiredField($validator->errors()->first());
        }

        try {
            $userOtp = UserOtp::where('email', $request->email)
                ->where('otp', $request->token)
                ->where('type', 'forget_password')
                ->where('isVerified', false)
                ->latest('created_at')
                ->first();

            if (!$userOtp) {
                return $this->apiResponse(null, false, 'Invalid or expired reset token.', 400);
            }

            $createdAt = $userOtp->created_at;
            $expiresAt = $createdAt->copy()->addMinutes(30);

            if (now()->greaterThan($expiresAt)) {
                return $this->apiResponse(null, false, 'Reset token has expired. Please request a new one.', 400);
            }

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return $this->notFoundResponse('User not found');
            }

            $user->update([
                'password' => Hash::make($request->password),
            ]);

            $userOtp->update([
                'isVerified' => true,
                'verified_at' => now(),
            ]);

            // Delete all other tokens for the same user.
            UserOtp::where('user_id', $user->id)
                ->where('type', 'forget_password')
                ->where('isVerified', false)
                ->forceDelete();

            $dataResponse['user'] = new UserResource($user);
            return $this->apiResponse($dataResponse, true, 'Password reset successfully.');
        } catch (\Exception $ex) {
            return $this->apiResponse(null, false, $ex->getMessage(), 500);
        }
    }
}
