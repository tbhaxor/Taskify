<?php

namespace App\Http\Controllers\Role;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\CreateRoleRequest;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CreateRoleController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(CreateRoleRequest $request): View|RedirectResponse
    {
        if ($request->isMethod('GET')) {
            return view('roles.create');
        }

        /** @var Role $role */
        $role = $request->user()->roles()->create($request->safe()->only('name'));
        Permission::whereIn('value', $request->safe()->input('permissions'))
            ->get('id')
            ->each(fn(Permission $permission) => $role->permissions()->attach($permission->id));
        
        return to_route('role.index');
    }
}
