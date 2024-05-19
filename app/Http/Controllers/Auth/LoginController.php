<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(LoginRequest $request)
    {
        if ($request->isMethod('GET')) {
            return view('auth.login');
        }

        if (auth()->attempt($request->safe(['email', 'password'], $request->boolean('remember')))) {
            $request->session()->regenerate();
            return redirect()->intended();
        }

        throw ValidationException::withMessages([
            'credentials' => 'Invalid login credentials.'
        ]);
    }
}
