<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $payload = [
            'id_token_hint' => $request->session()->get('zitadel_id_token'),
            'client_id' => config('services.zitadel.client_id'),
            'post_logout_redirect_uri' => config('app.url')
        ];
        return redirect()->away(config('services.zitadel.base_url') . '/oidc/v1/end_session?' . http_build_query($payload));
    }
}
