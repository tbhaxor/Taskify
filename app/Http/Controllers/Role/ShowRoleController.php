<?php

namespace App\Http\Controllers\Role;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\ShowRoleRequest;
use App\Models\Role;
use Illuminate\View\View;

class ShowRoleController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(ShowRoleRequest $request, Role $role): View
    {
        return view('roles.show', [
            'role' => $role->load('permissions'),
        ]);
    }
}
