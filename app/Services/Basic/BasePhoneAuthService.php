<?php

namespace App\Services\Basic;

use App\Facades\Services\Auth\OtpFacade;
use App\Http\Traits\GeneralTrait;
use App\Models\DeviceToken;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

abstract class BasePhoneAuthService
{
    use GeneralTrait;
    protected $model;
    protected $key;
    protected $resource;

    abstract protected function setVariables(): void;

    public function __construct()
    {
        $this->setVariables();
    }

    public function sendOtp(Request $request): void
    {
        $user = $this->model::firstOrCreate([
            'prefix_phone' => $request->prefix_phone,
            'phone' => $request->phone,
        ]);


        if ($user->status) {
            throw new HttpResponseException(
                $this->requiredField(__('messages.phone_already_verified'))
            );
        }

        OtpFacade::sendOtp($user, 'register');
    }


    public function verifyOtp(Request $request): array
    {
        $user = $this->model::where('prefix_phone', $request->prefix_phone)
            ->where('phone', $request->phone)
            ->firstOrFail();

        if (!OtpFacade::verifyOtp($user, $request->otp, 'register')) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                $this->requiredField('messages.invalid_otp'),

            );
        }
        $user->update([
            'phone_status' => true,
            'phone_verified_at' => now(),
        ]);

        $token = $user->createToken('pre-register-token')->plainTextToken;

        return [
            'token' => $token,
            $this->key => $this->resource::make($user),
        ];
    }


    public function login(Request $request): array
    {
        $user = $this->model::where('prefix_phone', $request->prefix_phone)
            ->where('phone', $request->phone)
            ->first();

        if (
            !$user ||
            !$user->status ||
            !Hash::check($request->password, $user->password)
        ) {
            throw new HttpResponseException(
                $this->requiredField(__('messages.invalid_credentials'))
            );
        }

        $accessToken = $user->createToken('access-token');
        $token = $accessToken->plainTextToken;
        $tokenId = $accessToken->accessToken->id;

        if ($request->filled('token_device')) {
            DeviceToken::updateOrCreate(
                [

                    'token_device' => $request->token_device,
                ],
                [
                    'device_able_type' => get_class($user),
                    'device_able_id' => $user->id,
                    'personal_access_token_id' => $tokenId,
                ]
            );
        }

        return [
            'token' => $token,
            $this->key => $this->resource::make($user),
        ];
    }



    public function sendResetOtp(Request $request): void
    {
        $user = $this->model::where('prefix_phone', $request->prefix_phone)
            ->where('phone', $request->phone)
            ->firstOrFail();

        OtpFacade::sendOtp($user, 'reset_password');
    }


    public function verifyResetOtp(Request $request): array
    {
        $user = $this->model::where('prefix_phone', $request->prefix_phone)
            ->where('phone', $request->phone)
            ->firstOrFail();

        if (!OtpFacade::verifyOtp($user, $request->otp, 'reset_password')) {
            throw new HttpResponseException(
                $this->requiredField(__('messages.invalid_otp'))
            );
        }

        $token = $user->createToken('reset-password-token')->plainTextToken;

        return [
            'token' => $token,
            $this->key => $this->resource::make($user),
        ];
    }

    public function resetPassword(Request $request): void
    {
        $user = auth()->user();
        $user->update([
            'password' => bcrypt($request->password),
        ]);
    }



}


