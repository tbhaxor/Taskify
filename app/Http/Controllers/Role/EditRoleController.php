<?php

namespace App\Http\Controllers\Role;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\EditRoleRequest;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EditRoleController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(EditRoleRequest $request, Role $role): View|RedirectResponse
    {
        if ($request->isMethod('GET')) {
            return view('roles.edit', [
                'role' => $role->load('permissions')
            ]);
        }

        $role->update($request->safe()->only('name'));
        $role->permissions()->detach();
    
        Permission::whereIn('value', $request->safe()->input('permissions'))
            ->get('id')
            ->each(fn(Permission $permission) => $role->permissions()->attach($permission->id));

        return to_route('role.index');
    }
}
