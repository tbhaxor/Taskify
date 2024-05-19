<?php

namespace App\Http\Controllers\ProfileSettings;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileSettings\UpdateProfileSettingsRequest;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UpdateProfileSettingsController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(UpdateProfileSettingsRequest $request)
    {
        if ($request->isMethod('POST')) {
            if ($request->user()->email != $request->string('email') && User::whereEmail($request->string('email'))->first() != null) {
                throw  ValidationException::withMessages([
                    'email' => 'The email has already been taken.'
                ]);
            }

            if ($request->safe()->input('old_password') && $request->safe()->input('new_password') == null) {
                throw ValidationException::withMessages([
                    'new_password' => 'The new password field is required.',
                ]);
            }

            if ($request->safe()->input('new_password') && $request->safe()->input('confirm_new_password') == null) {
                throw ValidationException::withMessages([
                    'confirm_new_password' => 'The confirm new password field is required.',
                ]);
            }

            /** @var Collection<string, string> */
            $payload = collect($request->safe(['name', 'email', 'new_password']));
            if ($payload->get('new_password') != null) {
                $payload->put('password', Hash::make($payload->get('new_password')));
            }

            $request->user()->update($payload->toArray());
        }

        return view('profile.edit');
    }
}
