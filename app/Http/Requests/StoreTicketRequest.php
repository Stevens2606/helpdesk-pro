<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
        public function authorize(): bool
    {
        return true;
    }

 public function bodyParameters(): array
{
    return [
        'subject'     => ['description' => 'Judul singkat tiket.', 'example' => 'Printer tidak bisa mencetak'],
        'description' => ['description' => 'Penjelasan masalah.', 'example' => 'Printer lantai 2 error.'],
        'category_id' => ['description' => 'ID kategori.', 'example' => 1],
        'priority'    => ['description' => 'low, medium, high, atau urgent.', 'example' => 'high'],
    ];
}
public function rules(): array
{
    return [
        'subject'       => ['required', 'string', 'max:255'],
        'description'   => ['required', 'string'],
        'category_id'   => ['required', 'exists:categories,id'],
        'priority'      => ['required', 'in:low,medium,high,urgent'],
        'attachments'   => ['nullable', 'array', 'max:3'],
        'attachments.*' => ['file', 'max:5120', 'mimes:jpg,jpeg,png,pdf,doc,docx,xlsx,txt'],
    ];
}
}
