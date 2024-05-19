<?php

namespace App\Http\Requests\Groups;

use App\Models\Group;
use Illuminate\Foundation\Http\FormRequest;

class EditGroupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->id() == $this->route()->parameter('group')->user_id;
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
