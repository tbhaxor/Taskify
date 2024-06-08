<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     * @throws ValidationException
     */
    public function __invoke(LoginRequest $request): View|RedirectResponse
    {
        if ($request->isMethod('GET')) {
            return view('auth.login');
        }

        if (auth()->attempt($request->safe(['email', 'password']), $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended();
        }

        throw ValidationException::withMessages([
            'credentials' => 'Invalid login credentials.'
        ]);
    }
}
