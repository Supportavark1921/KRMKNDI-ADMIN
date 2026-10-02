<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleController extends Controller
{
    public function index(): View
    {
        Gate::authorize('roles.view');

        return view('admin.roles.index', [
            'roles' => Role::withCount('permissions', 'users')->get(),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('roles.create');

        return view('admin.roles.create', [
            'permissionMap' => config('permissions'),
            'allPerms' => Permission::orderBy('name')->get()->keyBy('name'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('roles.create');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:80', 'unique:roles,name', 'regex:/^[a-z0-9\-_]+$/'],
        ]);

        $role = Role::create(['name' => $data['name'], 'guard_name' => 'web']);
        $perms = array_keys(array_filter($request->input('permissions', [])));
        $role->syncPermissions($perms);

        activity()->causedBy(auth()->user())
            ->performedOn($role)
            ->withProperties(['permissions' => $perms])
            ->log('role_created');

        return redirect()->route('admin.roles.index')->with('success', 'Role "'.$role->name.'" created.');
    }

    public function edit(Role $role): View
    {
        Gate::authorize('roles.update');
        $role->load('permissions');

        return view('admin.roles.edit', [
            'role' => $role,
            'permissionMap' => config('permissions'),
            'allPerms' => Permission::orderBy('name')->get()->keyBy('name'),
            'rolePerms' => $role->permissions->pluck('name')->flip(),
        ]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        Gate::authorize('roles.update');

        $perms = array_keys(array_filter($request->input('permissions', [])));

        $old = $role->permissions->pluck('name')->sort()->values()->toArray();
        $role->syncPermissions($perms);
        $new = $role->fresh()->permissions->pluck('name')->sort()->values()->toArray();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        activity()->causedBy(auth()->user())
            ->performedOn($role)
            ->withProperties(['old' => $old, 'new' => $new])
            ->log('role_permissions_updated');

        return redirect()->route('admin.roles.index')->with('success', 'Role "'.$role->name.'" updated.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        Gate::authorize('roles.delete');

        if (in_array($role->name, ['admin', 'user'], true)) {
            return back()->with('error', 'The admin and user roles cannot be deleted.');
        }
        if ($role->users()->exists()) {
            return back()->with('error', 'Cannot delete a role that has users assigned. Reassign them first.');
        }

        activity()->causedBy(auth()->user())
            ->performedOn($role)
            ->log('role_deleted');

        $role->delete();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')->with('success', 'Role "'.$role->name.'" deleted.');
    }
}
