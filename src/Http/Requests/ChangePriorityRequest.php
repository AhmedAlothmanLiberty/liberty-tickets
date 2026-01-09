<?php

namespace Liberty\Tickets\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePriorityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // policy checked in service call
    }

    public function rules(): array
    {
        return [
            'priority' => ['required', 'integer', 'min:1', 'max:10'],
        ];
    }
}
