<?php

namespace App\Http\Requests;

use App\Jobs\ImportTasksJob;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file_path' => ['required', 'string'],
            'original_file_name' => ['required', 'string'],
            'mapping' => ['required', 'array'],
            'mapping.title' => ['required', 'string'],
            'mapping.description' => ['nullable', 'string'],
            'mapping.status' => ['nullable', 'string'],
            'mapping.priority' => ['nullable', 'string'],
            'mapping.due_date' => ['nullable', 'string'],
            'mapping.assignee_email' => ['nullable', 'string'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $keys = array_keys($this->input('mapping', []));
            $invalid = array_diff($keys, array_keys(ImportTasksJob::SYSTEM_FIELDS));

            if (! empty($invalid)) {
                $validator->errors()->add('mapping', 'Field mapping tidak valid: '.implode(', ', $invalid));
            }
        });
    }
}
