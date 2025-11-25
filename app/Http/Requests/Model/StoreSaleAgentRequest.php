<?php

namespace App\Http\Requests\Model;

use App\Http\Requests\Basic\BasicRequest;use Illuminate\Foundation\Http\FormRequest;

class StoreSaleAgentRequest extends BasicRequest
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
            'agent_id' => 'required|integer|exists:agents,id',
            'property_id' => 'required|integer|exists:properties,id',
            'price' => 'nullable|numeric',
            'date' => 'nullable|date',
        ];
    }

}
