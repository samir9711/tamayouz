<?php
namespace App\Http\Requests\Model;

use Illuminate\Foundation\Http\FormRequest;

class ScanBadgeRequest extends FormRequest
{
    public function authorize(): bool
    {

        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required','string'],
        ];
    }
}
