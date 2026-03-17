<?php

namespace App\Http\Requests\Model;

use App\Http\Requests\Basic\BasicRequest;use Illuminate\Foundation\Http\FormRequest;

class StoreMinistryRequest extends BasicRequest
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
            'description' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'lat' => 'nullable|numeric',
            'lon' => 'nullable|numeric',
            'contact_phone' => 'nullable|string|max:255',
            'contact_email' => 'nullable|string|max:255',
            'main_image' => 'nullable|string|max:255',
            'images' => 'nullable|array',
            'website' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'youtube' => 'nullable|string|max:255',
            'linkedin' => 'nullable|string|max:255',
            'manager' => 'nullable|string|max:255',

            'account_name' => 'nullable|string|max:255',
            'account_email' => 'nullable|email|max:255',
            'account_password' => 'nullable|string|min:6|max:255',
            'account_role' => 'nullable|string|max:50',
        ];
    }

}
