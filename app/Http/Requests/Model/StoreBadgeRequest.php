<?php

namespace App\Http\Requests\Model;

use App\Http\Requests\Basic\BasicRequest;use Illuminate\Foundation\Http\FormRequest;

class StoreBadgeRequest extends BasicRequest
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
            'user_id' => ['required','integer','exists:users,id'],
            'establishment_id' => ['required','integer','exists:establishments,id'],
            'discount_percent' => ['required','numeric','min:0','max:100'],
            'valid_from' => ['nullable','date'],
            'valid_until' => ['nullable','date','after_or_equal:valid_from'],

            // badge fields
            'title' => ['nullable','string'],
            'description' => ['nullable','string'],

            // discount fields (أضفتهم هنا)
            'discount_title' => ['nullable','string'],
            'discount_description' => ['nullable','string'],
            'note' => ['nullable','string'],

            'categories' => ['nullable','array'],
            'categories.*' => ['string'],

            // optional badge fields
            'code' => ['nullable','string'],
            'ministry_id' => ['nullable','integer','exists:ministries,id'],
        ];
    }

}
