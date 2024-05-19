<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SignupRequest;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class SignupController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(SignupRequest $request)
    {
        if ($request->isMethod('GET')) {
            return view('auth.signup');
        }

        User::create($request
            ->safe()
            ->merge([
                'email_verified_at' => Carbon::now(),
            ])
            ->except('confirm_password'));

        return to_route('auth.login');
    }
}
