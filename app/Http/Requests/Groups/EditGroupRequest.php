<?php

namespace App\Http\Requests\Groups;

use App\Enums\UserPermission;
use Illuminate\Foundation\Http\FormRequest;

class EditGroupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->route('group')->user_id == $this->user()->id || $this->user()->can(UserPermission::EDIT_GROUPS->value, $this->route('group'));
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
            'title' => 'required|max:64',
            'description' => 'nullable'
        ];
    }
}
