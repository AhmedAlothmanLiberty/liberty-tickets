<?php

namespace Liberty\Tickets\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \Liberty\Tickets\Models\Ticket::class);
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:200'],
            'description' => ['required', 'string'],
            'type'        => ['required', 'in:bug,feature'],
            'priority'    => ['required', 'integer', 'min:1', 'max:10'],
        ];
    }
}
