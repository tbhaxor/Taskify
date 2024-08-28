<?php

namespace App\Http\Requests\Groups;

use App\Enums\UserPermission;
use Illuminate\Foundation\Http\FormRequest;

class DeleteGroupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->route('group')->user_id == $this->user()->id || $this->user()->can(UserPermission::DELETE_GROUPS->value, $this->route('group'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }
}
