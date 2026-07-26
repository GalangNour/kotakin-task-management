<?php

namespace App\Http\Requests;

use App\Exports\TasksExport;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskExportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'columns' => ['required', 'array', 'min:1'],
            'columns.*' => [Rule::in(array_keys(TasksExport::AVAILABLE_COLUMNS))],
        ];
    }
}
