<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskAttachmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // size in KB, sesuai rancangan: 100-500 KB
            'file' => ['required', 'file', 'min:100', 'max:500'],
        ];
    }
}
