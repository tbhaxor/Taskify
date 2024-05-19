<?php

namespace App\Http\Controllers\ProfileSettings;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileSettings\DeleteProfileSettingsRequest;

class DeleteProfileSettingsController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(DeleteProfileSettingsRequest $request)
    {
        $request->user()->delete();
        return to_route('auth.logout');
    }
}
