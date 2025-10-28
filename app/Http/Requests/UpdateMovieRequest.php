<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMovieRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'poster_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Không bắt buộc khi update
            'trailer_url' => 'required|url',
            'duration_minutes' => 'required|integer|min:1',
            'release_date' => 'required|date',
            'age_rating' => 'required|string',
            'status' => 'required|string',

            'genres' => 'present|array',
            'genres.*' => 'exists:genres,id',

            'actors' => 'present|array',
            'actors.*' => 'exists:actors,id',

            'directors' => 'present|array',
            'directors.*' => 'exists:directors,id',
        ];
    }
}
