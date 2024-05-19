<?php

namespace App\Http\Requests\Groups;

use Illuminate\Foundation\Http\FormRequest;

class ShowGroupRequest extends FormRequest
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
        return [
            //
        ];
    }
}
