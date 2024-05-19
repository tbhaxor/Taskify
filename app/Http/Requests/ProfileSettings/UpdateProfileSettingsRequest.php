<?php

namespace App\Http\Requests\ProfileSettings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return !auth()->guest();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if ($this->isMethod('GET')) {
            return [];
        }

        return [
            'name' => 'required|max:256',
            'email' => 'required|email',
            'old_password' => 'nullable|current_password',
            'new_password' => 'required_if:old_password,not_empty',
            'confirm_new_password' => 'required_if:old_password,not_empty|same:new_password'
        ];
    }
}
