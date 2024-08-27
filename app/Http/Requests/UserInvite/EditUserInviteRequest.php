<?php

namespace App\Http\Requests\UserInvite;

use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditUserInviteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->id === $this->route('group')->user_id;
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
            'role_id' => [
                'required',
                'integer',
                Rule::exists('roles', 'id')->where(function (Builder $query) {
                    return $query->where('user_id', $this->user()->id)->orWhereNull('user_id');
                }),
            ]
        ];
    }
}
