<?php

namespace App\Http\Requests\Model;

use App\Http\Requests\Basic\BasicRequest;use Illuminate\Foundation\Http\FormRequest;

class StoreAboutUsRequest extends BasicRequest
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
            'manager_name' => 'required|string|max:255',
            'manager_position' => 'nullable|string|max:255',
            'manager_image' => 'required|string|max:255',
            'manager_description' => 'required|string',
            'video_url' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'sub_description' => 'nullable|string',
            'description' => 'required|string',
            'content' => 'nullable|string',
            'vision' => 'nullable|string',
            'mission' => 'nullable|string',
            'apart' => 'nullable|string',
            'Our_value' => 'nullable|array',
            'approach' => 'nullable|string|max:255',
            'target' => 'nullable|array',
            'philosophy' => 'nullable|string',
            'text_partner' => 'nullable|string',
            'text_services' => 'nullable|string',
        ];
    }

}
