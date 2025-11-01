<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255|min:3',
            'description' => 'nullable|string|max:1000',
            'status' => 'nullable|in:pending,in_progress,completed,cancelled',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'category_id' => 'nullable|exists:categories,id'
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
        $this->merge([
            'title' => trim($this->title),
            'description' => $this->description ? trim($this->description) : null,
        ]);
    }
}
