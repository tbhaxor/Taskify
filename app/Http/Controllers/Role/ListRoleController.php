<?php

namespace App\Http\Controllers\Role;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\ListRoleRequest;
use Illuminate\View\View;

class ListRoleController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(ListRoleRequest $request): View
    {
        return view('roles.index', [
            'roles' => $request->user()->roles()->withCount('permissions')->get()
        ]);
    }
}
