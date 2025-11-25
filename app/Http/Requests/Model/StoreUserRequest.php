<?php

namespace App\Http\Requests\Model;

use App\Http\Requests\Basic\BasicRequest;use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends BasicRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|max:255|unique:users,email',
            'password' => 'nullable|string|max:255',
            'register_id' => 'nullable|integer',
            'address' => 'nullable|string|max:255',
            'birthday' => 'nullable|date',
            'phone' => 'nullable|string|max:255',
            'gender' => 'nullable|string|max:255',
            'image_profile' => 'nullable|string|max:255',
            'status' => 'required|boolean',
            'email_verified_at' => 'nullable|date_format:Y-m-d H:i:s',
            'remember_token' => 'nullable|string|max:100',
            'custom_fields' => 'nullable|array',
            'avatar_url' => 'nullable|string|max:255',
        ];
    }

}
