<?php

namespace Liberty\Tickets\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // policy checked in service call
    }

    public function rules(): array
    {
        return [
            'assigned_to' => ['required', 'integer', 'min:1'],
        ];
    }
}
