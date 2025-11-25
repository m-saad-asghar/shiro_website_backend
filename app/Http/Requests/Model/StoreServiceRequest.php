<?php

namespace App\Http\Requests\Model;

use App\Http\Requests\Basic\BasicRequest;use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends BasicRequest
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
            'title_main' => 'required|string|max:255',
            'image_main' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'sub_image' => 'nullable|string|max:255',
            'sub_title' => 'nullable|string|max:255',
            'description_header' => 'nullable|string',
        ];
    }

}
