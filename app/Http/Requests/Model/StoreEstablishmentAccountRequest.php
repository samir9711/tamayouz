<?php

namespace App\Http\Requests\Model;

use App\Http\Requests\Basic\BasicRequest;use Illuminate\Foundation\Http\FormRequest;

class StoreEstablishmentAccountRequest extends BasicRequest
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
            'establishment_id' => 'required|integer|exists:establishments,id',
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:establishment_accounts,email',
            'password' => 'required|string|max:255',
            'role' => 'required|in:min_admin,min_subadmin',
        ];
    }

}
