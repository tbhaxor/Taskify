<?php

namespace App\Http\Controllers\Auth;

use AdrienGras\PKCE\PKCEUtils;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     * @throws ValidationException
     */
    public function __invoke(Request $request): View|RedirectResponse
    {

        $pkce = PKCEUtils::generateCodePair();
        $request->session()->put('zitadel_pkce', $pkce);

        $scopes = [
            'openid',
            'profile',
            'email',
            'urn:zitadel:iam:org:project:id:zitadel:aud',
            'urn:zitadel:iam:org:id:' . config('services.zitadel.org_id')
        ];

        $query = [
            'response_type' => 'code',
            'client_id' => config('services.zitadel.client_id'),
            'scope' => Arr::join($scopes, ' '),
            'redirect_uri' => route('auth.callback'),
            'code_challenge_method' => 'S256',
            'code_challenge' => $pkce['code_challenge']
        ];

        Log::info('Crafted query params', $query);

        return redirect(config('services.zitadel.base_url') . '/oauth/v2/authorize?' . http_build_query($query));
    }
}
