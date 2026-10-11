<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // otorisasi ditangani TicketPolicy di controller
    }

    public function rules(): array
    {
        return [
            'status'      => ['required', 'in:open,in_progress,resolved,closed'],
            'priority'    => ['required', 'in:low,medium,high,urgent'],
            'assigned_to' => ['nullable', 'exists:users,id'],
        ];
    }
}
