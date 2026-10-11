<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
   public function authorize(): bool
    {
        return true; // otorisasi di controller lewat policy
    }

    public function rules(): array
    {
        return [
            'body'          => ['required', 'string', 'max:5000'],
            'is_internal'   => ['nullable', 'boolean'],
            'attachments'   => ['nullable', 'array', 'max:3'],
            'attachments.*' => ['file', 'max:5120', 'mimes:jpg,jpeg,png,pdf,doc,docx,xlsx,txt'],
        ];
    }
}
