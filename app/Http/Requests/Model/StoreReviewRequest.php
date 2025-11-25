<?php

namespace App\Http\Requests\Model;

use App\Http\Requests\Basic\BasicRequest;use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends BasicRequest
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
            'image' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'rate' => 'required|integer',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date|before_or_equal:today',
        ];
    }

}
