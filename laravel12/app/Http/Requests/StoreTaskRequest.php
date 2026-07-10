<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:todo,in_progress,done',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Task title is required',
            'title.max' => 'Title must not exceed 255 characters',
            'status.required' => 'Task status is required',
            'status.in' => 'Status must be one of: todo, in_progress, done',
            'assigned_to.exists' => 'The selected user does not exist',
            'due_date.date' => 'Due date must be a valid date',
        ];
    }
}
