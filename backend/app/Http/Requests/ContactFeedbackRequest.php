<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validación centralizada y mensajes en español para el formulario público de contacto.
 */
class ContactFeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'subject' => ['nullable', 'string', 'max:160'],
            'branch' => ['nullable', 'string', 'max:160'],
            'rating' => ['nullable', 'string', 'max:20'],
            'type' => ['nullable', 'string', 'max:50'],
            'source' => ['nullable', 'string', 'max:80'],
            'notes' => ['nullable', 'string', 'max:500'],
            'message' => ['required', 'string', 'max:2500'],
            // Campos trampa antispam (Honeypot) validados como opcionales
            'website' => ['nullable', 'string', 'max:255'],
            '_gotcha' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Por favor escribe tu nombre completo.',
            'email.required' => 'Por favor proporciona un correo electrónico válido.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'message.required' => 'Por favor escribe tu mensaje o consulta.',
            'message.max' => 'El mensaje no puede superar los 2,500 caracteres.',
        ];
    }
}
