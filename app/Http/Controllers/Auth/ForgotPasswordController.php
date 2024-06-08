<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(ForgotPasswordRequest $request): View
    {
        if ($request->isMethod('GET')) {
            return view('auth.forgot-password');
        }

        $status = Password::sendResetLink($request->only('email'));

        return view('auth.forgot-password', [
            'success' => $status === Password::RESET_LINK_SENT,
            'message' => __($status)
        ]);
    }
}
