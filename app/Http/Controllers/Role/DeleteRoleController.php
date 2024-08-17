<?php

namespace App\Http\Controllers\Role;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\DeleteRoleRequest;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DeleteRoleController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(DeleteRoleRequest $request, Role $role): View|RedirectResponse
    {
        if ($request->isMethod('GET')) {
            return view('roles.delete', [
                'role' => $role
            ]);
        }

        $role->delete();

        return to_route('role.index');
    }
}
