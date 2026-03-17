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
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'mission' => 'nullable|string',
            'vision' => 'nullable|string',
            'values' => 'nullable|array',
            'contact_email' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'lat' => 'nullable|numeric',
            'lon' => 'nullable|numeric',
            'main_image' => 'nullable|string|max:255',
            'images' => 'nullable|array',
            'website' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'youtube' => 'nullable|string|max:255',
            'linkedin' => 'nullable|string|max:255',
        ];
    }

}
