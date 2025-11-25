<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use http\Client\Curl\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserVerifyEmailController extends Controller
{
    use GeneralTrait;
    public function resendVerificationOtp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
            ]);

            if ($validator->fails()) {
                return $this->requiredField($validator->errors()->first());
            }

            $user = \App\Models\User::where('email', $request->email)->first();

            if ($user->status) {
                return $this->apiResponse(null, false, 'Account is already verified.', 400);
            }

            // Generate a new random OTP.
            $otp = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);

            // Delete the old OTP.
            \App\Models\UserOtp::where('email', $user->email)
                ->where('type', 'register_email')
                ->where('isVerified', false)
                ->forceDelete();

            // Create a new OTP.
            \App\Models\UserOtp::create([
                'user_id'    => $user->id,
                'otp'        => $otp,
                'isVerified' => false,
                'type'       => 'register_email',
                'email'      => $user->email,
            ]);

            // Send the email with fallback.
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
                \Log::error('Both email methods failed for resend OTP: ' . $user->email);
                return $this->apiResponse(null, false, 'Failed to send verification email. Please check your email address or try again later.', 503);
            }

            return $this->apiResponse(['message' => 'OTP resent to your email successfully.']);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }


    public function verifyEmailOtp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'otp'   => 'required|string|size:6',
            ]);

            if ($validator->fails()) {
                return $this->requiredField($validator->errors()->first());
            }

            $userOtp = \App\Models\UserOtp::where('email', $request->email)
                ->where('otp', $request->otp)
                ->where('type', 'register_email')
                ->where('isVerified', false)
                ->latest('created_at')
                ->first();

            if (!$userOtp) {
                return $this->requiredField('Invalid or already used OTP.');
            }

            // Check if expired (valid for 15 minutes)
            $createdAt = $userOtp->created_at;
            $expiresAt = $createdAt->copy()->addMinutes(15);

            if (now()->greaterThan($expiresAt)) {
                return $this->requiredField('This OTP has expired. Please request a new one.');
            }

            $user = \App\Models\User::where('email', $request->email)->firstOrFail();

            $user->update([
                'status' => true,
                'email_verified_at' => now(),
            ]);

            $userOtp->update([
                'isVerified' => true,
                'verified_at' => now(),
            ]);

            $token = $user->createToken('MyApp')->plainTextToken;

            return $this->apiResponse([
                'message' => 'Email verified successfully.',
                'token' => $token,
                'user' => new \App\Http\Resources\Model\UserResource($user),
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

}
