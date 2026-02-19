<?php

namespace App\Http\Requests;

use App\Models\Ticket;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'priority'    => ['required', Rule::in(Ticket::PRIORITIES)],
            'attachments'   => ['required', 'array', 'min:1'],
            'attachments.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'], // 5 MB
        ];
    }

    public function messages(): array
    {
        return [
            'attachments.required'  => 'Debes adjuntar al menos una imagen como evidencia.',
            'attachments.min'       => 'Se requiere al menos 1 imagen.',
            'attachments.*.image'   => 'Solo se permiten imágenes (jpg, png, webp).',
            'attachments.*.max'     => 'Cada imagen no debe superar 5 MB.',
        ];
    }
}
