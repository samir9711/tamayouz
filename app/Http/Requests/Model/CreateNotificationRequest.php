<?php
namespace App\Http\Requests\Model;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {

        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['nullable','string','max:255'],
            'body'  => ['nullable','string'],
            'kind'  => ['required', Rule::in(['news','notification','alert','other'])],

            // target object: type = user | users | ministry
            'target.type' => ['required','string', Rule::in(['user','users','ministry'])],
            'target.id'   => ['nullable','integer'],      // for 'user' or 'ministry'
            'target.ids'  => ['nullable','array'],        // for 'users'
            'target.ids.*'=> ['integer'],

            // optional scheduling (now or later) — for simplicity we use sent_at optional
            'sent_at' => ['nullable','date'],
        ];
    }
}
