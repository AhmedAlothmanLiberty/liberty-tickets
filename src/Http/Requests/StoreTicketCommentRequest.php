<?php

namespace Liberty\Tickets\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $ticket = $this->route('ticket');
        return $this->user()->can('view', $ticket);
    }

    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:5000'],
        ];
    }
}
