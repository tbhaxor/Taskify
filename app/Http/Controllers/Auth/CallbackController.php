<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class CallbackController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        /** @var \SocialiteProviders\Manager\OAuth2\User $profile */
        $profile = Socialite::driver('zitadel')->enablePKCE()->user();

        /** @var User $user */
        $user = User::updateOrCreate(['email' => $profile->email], ['name' => $profile->name]);

        $request->session()->put('zitadel_id_token', $profile->accessTokenResponseBody['id_token']);

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->intended(route('group.index'));
    }
}
