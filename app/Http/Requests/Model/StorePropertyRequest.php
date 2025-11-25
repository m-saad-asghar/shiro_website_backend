<?php

namespace App\Http\Requests\Model;

use App\Http\Requests\Basic\BasicRequest;use Illuminate\Foundation\Http\FormRequest;

class StorePropertyRequest extends BasicRequest
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
        $isSale = $this->input('is_sale', false);
        
        return [
            'title' => 'required|string|max:255',
            'type_id' => 'nullable|integer|exists:types,id',
            'purpose' => 'nullable|string|max:255',
            'is_finish' => 'required|boolean',
            'completion' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'rental_period' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'images' => $isSale ? 'required|array|min:3' : 'nullable|array',
            'images.*' => $isSale ? 'required|string' : 'nullable|string',
            'area' => 'nullable|numeric',
            'region_id' => 'nullable|integer|exists:regions,id',
            'num_bathroom' => 'required|integer',
            'num_bedroom' => 'required|integer',
            'agent_id' => 'nullable|integer|exists:agents,id',
            'developer_id' => 'nullable|integer|exists:developers,id',
            'profile' => 'nullable|string',
            'contact' => 'nullable|array',
            'service_id' => 'nullable|integer|exists:services,id',
            'is_sale' => 'required|boolean',
            'date_sale' => 'nullable|date',
            'is_home' => 'required|boolean',
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'images.required' => 'يجب إضافة الصور.',
            'images.min' => 'يجب إضافة 3 صور على الأقل للعقار.',
            'images.*.required' => 'يجب أن تكون كل صورة صالحة.',
        ];
    }

}
