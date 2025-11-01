<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Make sure user can only update their own tasks
        $user = $this->user();
        if (!$user) {
            return false;
        }
        
        $task = $this->route('id') ? $user->tasks()->find($this->route('id')) : null;
        return $task !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255|min:3',
            'description' => 'sometimes|nullable|string|max:1000',
            'completed' => 'sometimes|boolean',
            'status' => 'sometimes|nullable|in:pending,in_progress,completed,cancelled',
            'priority' => 'sometimes|nullable|in:low,medium,high,urgent',
            'category_id' => 'sometimes|nullable|exists:categories,id'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul tugas harus diisi.',
            'title.min' => 'Judul tugas minimal 3 karakter.',
            'title.max' => 'Judul tugas maksimal 255 karakter.',
            'description.max' => 'Deskripsi maksimal 1000 karakter.',
            'completed.boolean' => 'Status penyelesaian harus berupa true atau false.',
            'status.in' => 'Status yang dipilih tidak valid.',
            'priority.in' => 'Prioritas yang dipilih tidak valid.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('title')) {
            $this->merge(['title' => trim($this->title)]);
        }
        
        if ($this->has('description')) {
            $this->merge(['description' => $this->description ? trim($this->description) : null]);
        }
    }
}
