<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LogoutRequest;

class LogoutController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(LogoutRequest $request)
    {
        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('auth.login');
    }
}
