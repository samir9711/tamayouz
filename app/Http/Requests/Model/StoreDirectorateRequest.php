<?php

namespace App\Http\Requests\Model;

use App\Http\Requests\Basic\BasicRequest;use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; 

class StoreDirectorateRequest extends BasicRequest
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
            'ministry_id' => [
                Rule::requiredIf(fn () => auth('admin')->check()),
                'nullable',
                'integer',
                'exists:ministries,id'
            ],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'contact_phone' => 'nullable|string|max:255',
            'contact_email' => 'nullable|string|max:255',
        ];
    }

}
