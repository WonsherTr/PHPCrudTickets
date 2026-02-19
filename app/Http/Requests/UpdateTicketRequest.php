<?php

namespace App\Http\Requests;

use App\Models\Ticket;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
        ];

        // Only admin can change status & priority
        if ($this->user()->isAdmin()) {
            $rules['status']   = ['required', Rule::in(Ticket::STATUSES)];
            $rules['priority'] = ['required', Rule::in(Ticket::PRIORITIES)];
        }

        // Optional new attachments
        $rules['attachments']   = ['nullable', 'array'];
        $rules['attachments.*'] = ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'];

        return $rules;
    }
}
