<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OpenLibraryBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string'],
            'author_name' => ['nullable', 'array'],
            'author_name.*' => ['string'],
            'cover_i' => ['nullable', 'integer'],
            'isbn' => ['nullable', 'array'],
            'isbn.*' => ['string'],
            'editions_description' => ['nullable', 'string'],
        ];
    }
}
