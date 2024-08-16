<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CallbackController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        if (!$request->session()->has('zitadel_pkce')) {
            return to_route('auth.login');
        }

        $payload = [
            'client_id' => config('services.zitadel.client_id'),
            'code' => $request->query('code'),
            'redirect_uri' => route('auth.callback'),
            'grant_type' => 'authorization_code',
            'code_verifier' => $request->session()->get('zitadel_pkce')['code_verifier'],
        ];

        Log::info('Retrieved information', $payload);

        $response = Http::asForm()->post(config('services.zitadel.base_url') . '/oauth/v2/token?' . http_build_query($payload));
        $tokens = Arr::only($response->json(), ['id_token', 'access_token']);

        $response = Http::withHeader('Authorization', 'Bearer ' . $tokens['access_token'])
            ->post(config('services.zitadel.base_url') . '/oidc/v1/userinfo');

        /** @var User $user */
        $user = User::updateOrCreate(['email' => $response->json('email')], Arr::only($response->json(), ['name', 'email']));

        $request->session()->put('zitadel_id_token', $tokens['id_token']);
        $request->session()->remove('zitadel_pkce');

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->intended(route('group.index'));
    }
}
