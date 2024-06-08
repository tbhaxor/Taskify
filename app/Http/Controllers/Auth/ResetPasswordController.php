<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class ResetPasswordController extends Controller
{
    /**
     * Handle the incoming request.
     * @throws ValidationException
     */
    public function __invoke(ResetPasswordRequest $request): View|RedirectResponse
    {
        if ($request->isMethod('GET')) {
            return view('auth.reset-password', $request->only(['email', 'token']));
        }

        $status = Password::reset($request->only(['email', 'password', 'password_confirmation', 'token']), function (User $user, string $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])
                ->save();

            event(new PasswordReset($user));
        });


        if ($status == Password::PASSWORD_RESET) {
            return to_route('auth.login', ['message' => __($status)]);
        }

        throw ValidationException::withMessages([
            'error' => __($status),
        ]);
    }
}
