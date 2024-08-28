<?php

namespace App\Http\Requests\GroupSharing;

use App\Models\Group;
use Illuminate\Foundation\Http\FormRequest;

class DeleteGroupSharingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var Group $group */
        $group = $this->route('group');
        return $this->user()->id === $group->user_id && (int)$this->query('user_id') !== $group->user_id;
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
