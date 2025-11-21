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
        // Get the movie being updated from route parameter
        $movie = $this->route('movie');

        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            // Poster: nullable khi update (nếu không có file mới = giữ nguyên poster cũ)
            // Nhưng nếu movie chưa có poster và user không upload → báo lỗi
            'poster_url' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:5120',
                function ($attribute, $value, $fail) use ($movie) {
                    // Nếu không upload file mới VÀ movie chưa có poster cũ
                    if (!$value && !$movie->poster_url) {
                        $fail('Poster là bắt buộc. Vui lòng upload ảnh poster.');
                    }
                },
            ],
            // Trailer: nullable khi update (nếu không có file/URL mới = giữ nguyên trailer cũ)
            // Nhưng nếu movie chưa có trailer và user không nhập → báo lỗi
            'trailer_url' => [
                'nullable',
                function ($attribute, $value, $fail) use ($movie) {
                    // Nếu không có value mới VÀ movie chưa có trailer cũ
                    if (!$value && !$this->hasFile('trailer_url') && !$movie->trailer_url) {
                        $fail('Trailer là bắt buộc. Vui lòng nhập URL hoặc upload video.');
                    }
                },
            ],
            'duration_minutes' => 'required|integer|min:1',
            'release_date' => 'required|date',
            'age_rating' => 'required|string|in:P,C13,C16,C18',
            'status' => 'required|string|in:coming_soon,now_showing,ended',

            'genres' => 'nullable|array',
            'genres.*' => 'exists:genres,id',

            'actors' => 'nullable|array',
            'actors.*' => 'exists:actors,id',

            'directors' => 'nullable|array',
            'directors.*' => 'exists:directors,id',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Tiêu đề không được để trống',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự',
            'description.required' => 'Mô tả không được để trống',
            'poster_url.image' => 'File phải là ảnh',
            'poster_url.mimes' => 'Poster phải có định dạng jpeg, png, jpg, gif hoặc webp',
            'poster_url.max' => 'Kích thước poster không được vượt quá 5MB',
            'duration_minutes.required' => 'Thời lượng không được để trống',
            'duration_minutes.integer' => 'Thời lượng phải là số',
            'duration_minutes.min' => 'Thời lượng phải lớn hơn 0',
            'release_date.required' => 'Ngày phát hành không được để trống',
            'release_date.date' => 'Ngày phát hành không đúng định dạng',
            'age_rating.required' => 'Độ tuổi không được để trống',
            'age_rating.in' => 'Độ tuổi không hợp lệ',
            'status.required' => 'Trạng thái không được để trống',
            'status.in' => 'Trạng thái không hợp lệ',
            'genres.*.exists' => 'Thể loại không tồn tại',
            'actors.*.exists' => 'Diễn viên không tồn tại',
            'directors.*.exists' => 'Đạo diễn không tồn tại',
        ];
    }
}
