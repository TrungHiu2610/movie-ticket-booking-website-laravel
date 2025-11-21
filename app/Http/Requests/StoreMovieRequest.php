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
            'poster_url' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB
            // Trailer có thể là URL string hoặc file upload
            'trailer_url' => [
                'required',
                function ($attribute, $value, $fail) {
                    // Nếu là file upload
                    if ($this->hasFile('trailer_url')) {
                        $file = $this->file('trailer_url');

                        // Validate file type
                        $allowedMimes = ['video/mp4', 'video/quicktime', 'video/x-msvideo'];
                        if (!in_array($file->getMimeType(), $allowedMimes)) {
                            $fail('Trailer phải là video (MP4, MOV, AVI)');
                        }

                        // Validate file size (100MB)
                        if ($file->getSize() > 102400 * 1024) {
                            $fail('Kích thước video không được vượt quá 100MB');
                        }
                    }
                    // Nếu là URL string
                    elseif (!filter_var($value, FILTER_VALIDATE_URL)) {
                        $fail('Trailer phải là URL hợp lệ hoặc file video');
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
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự',
            'description.required' => 'Mô tả không được để trống',
            'poster_url.required' => 'Vui lòng chọn ảnh poster',
            'poster_url.image' => 'File phải là ảnh',
            'poster_url.mimes' => 'Poster phải có định dạng jpeg, png, jpg, gif hoặc webp',
            'poster_url.max' => 'Kích thước poster không được vượt quá 5MB',
            'trailer_url.mimetypes' => 'Trailer phải là video (MP4, MOV, AVI)',
            'trailer_url.max' => 'Kích thước video không được vượt quá 100MB',
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
