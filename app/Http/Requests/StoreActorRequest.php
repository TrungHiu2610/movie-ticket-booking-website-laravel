<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreActorRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:actors,name',
            'photo_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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
            'name.required' => 'Tên diễn viên không được để trống',
            'name.max' => 'Tên diễn viên không được vượt quá 255 ký tự',
            'name.unique' => 'Tên diễn viên đã tồn tại',
            'photo_url.image' => 'File phải là ảnh',
            'photo_url.mimes' => 'Ảnh phải có định dạng jpeg, png, jpg hoặc gif',
            'photo_url.max' => 'Kích thước ảnh không được vượt quá 2MB',
        ];
    }
}
