<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Validation\Validator;

class StoreMovieRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'poster_url' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Yêu cầu là ảnh, max 2MB
            'trailer_url' => 'required|string', // Tạm thời bỏ validation url
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

    protected function failedValidation(Validator $validator)
    {
        Log::error('StoreMovieRequest validation failed:', [
            'errors' => $validator->errors()->toArray(),
            'input' => $this->except(['poster_url'])
        ]);

        parent::failedValidation($validator);
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Tiêu đề không được để trống',
            'description.required' => 'Mô tả không được để trống',
            'poster_url.required' => 'Vui lòng chọn ảnh poster',
            'poster_url.image' => 'File phải là ảnh',
            'trailer_url.required' => 'URL trailer không được để trống',
            'duration_minutes.required' => 'Thời lượng không được để trống',
            'release_date.required' => 'Ngày phát hành không được để trống',
        ];
    }
}
