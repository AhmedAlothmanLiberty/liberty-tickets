<?php

namespace Liberty\Tickets\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        $ticket = $this->route('ticket');
        return $this->user()->can('update', $ticket);
    }

    public function rules(): array
    {
        return [
            'title'       => ['sometimes', 'string', 'max:200'],
            'description' => ['sometimes', 'string'],
        ];
    }
}
