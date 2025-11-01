<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization handled by CategoryPolicy
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:categories,name',
                'regex:/^[a-zA-Z0-9\s\-\_]+$/', // Alphanumeric, spaces, hyphens, underscores only
            ],
            'color' => [
                'required',
                'string',
                'regex:/^#[a-fA-F0-9]{6}$/', // Valid hex color
            ],
            'description' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Nama kategori sudah digunakan.',
            'name.regex' => 'Nama kategori hanya boleh berisi huruf, angka, spasi, tanda hubung, dan garis bawah.',
            'name.max' => 'Nama kategori maksimal 255 karakter.',
            'color.required' => 'Warna kategori wajib dipilih.',
            'color.regex' => 'Format warna tidak valid. Gunakan format hex (#RRGGBB).',
            'description.max' => 'Deskripsi maksimal 500 karakter.',
        ];
    }

    /**
     * Prepare data for validation
     */
    protected function prepareForValidation(): void
    {
        // Trim whitespace from name
        if ($this->has('name')) {
            $this->merge([
                'name' => trim($this->name),
            ]);
        }

        // Ensure color has # prefix
        if ($this->has('color') && !str_starts_with($this->color, '#')) {
            $this->merge([
                'color' => '#' . $this->color,
            ]);
        }
    }
}
