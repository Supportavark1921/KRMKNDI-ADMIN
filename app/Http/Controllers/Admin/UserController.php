<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('users.view');

        $query = User::withTrashed()->with('roles');

        if ($s = $request->query('search')) {
            $query->where(fn ($q) => $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"));
        }
        if ($role = $request->query('role')) {
            $query->where('role', $role);
        }
        if ($status = $request->query('status')) {
            if ($status === 'trashed') {
                $query->onlyTrashed();
            } else {
                $query->where('status', $status)->withoutTrashed();
            }
        }

        return view('admin.users.index', [
            'users' => $query->latest()->paginate(25)->withQueryString(),
            'roles' => User::ROLES,
        ]);
    }

    public function create(): View
    {
        Gate::authorize('users.create');

        return view('admin.users.create', ['roles' => User::ROLES]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('users.create');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:'.implode(',', User::ROLES)],
            'status' => ['required', 'in:active,suspended'],
        ]);

        DB::transaction(function () use ($data, $request) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => $data['role'],
                'status' => $data['status'],
            ]);

            $user->syncRoles([$data['role']]);
            $this->maybeCreateProfile($user, $request);

            activity()->causedBy(auth()->user())
                ->performedOn($user)
                ->withProperties(['role' => $data['role']])
                ->log('user_created');
        });

        return redirect()->route('admin.users.index')->with('success', 'User created.');
    }

    public function show(User $user): View
    {
        Gate::authorize('users.view');
        $user->load('roles', 'permissions', 'vendor');

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        Gate::authorize('users.update');
        $user->load('roles', 'permissions', 'vendor');

        return view('admin.users.edit', [
            'user' => $user,
            'roles' => User::ROLES,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('users.update');
        $this->preventSelfRoleChange($user);

        $rules = [
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255', "unique:users,email,{$user->id}"],
            'role' => ['required', 'in:'.implode(',', User::ROLES)],
            'status' => ['required', 'in:active,suspended'],
        ];
        if ($request->filled('password')) {
            $rules['password'] = ['string', 'min:8', 'confirmed'];
        }

        $data = $request->validate($rules);

        DB::transaction(function () use ($user, $data, $request) {
            $payload = [
                'name' => $data['name'],
                'email' => $data['email'],
                'role' => $data['role'],
                'status' => $data['status'],
            ];
            if (isset($data['password'])) {
                $payload['password'] = Hash::make($data['password']);
            }

            $user->update($payload);
            $user->syncRoles([$data['role']]);
            $this->maybeCreateProfile($user, $request);
        });

        return redirect()->route('admin.users.show', $user)->with('success', 'User updated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        Gate::authorize('users.delete');
        $this->preventLastAdmin($user);
        $this->preventSelf($user);

        $user->delete(); // soft delete

        return redirect()->route('admin.users.index')->with('success', 'User archived.');
    }

    public function restore(int $id): RedirectResponse
    {
        Gate::authorize('users.restore');
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return back()->with('success', 'User restored.');
    }

    public function suspend(User $user): RedirectResponse
    {
        Gate::authorize('users.update');
        $this->preventSelf($user);
        $user->update(['status' => 'suspended']);

        activity()->causedBy(auth()->user())
            ->performedOn($user)
            ->log('user_suspended');

        return back()->with('success', "{$user->name} suspended.");
    }

    public function activate(User $user): RedirectResponse
    {
        Gate::authorize('users.update');
        $user->update(['status' => 'active']);

        activity()->causedBy(auth()->user())
            ->performedOn($user)
            ->log('user_activated');

        return back()->with('success', "{$user->name} activated.");
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function maybeCreateProfile(User $user, Request $request): void
    {
        if ($user->isVendor() && ! $user->vendor) {
            Vendor::create([
                'user_id' => $user->id,
                'business_name' => $request->input('business_name', $user->name),
                'status' => 'pending',
            ]);
        }
    }

    private function preventLastAdmin(User $user): void
    {
        if ($user->isAdmin() && User::where('role', 'admin')->whereNull('deleted_at')->count() <= 1) {
            abort(403, 'Cannot delete the last admin.');
        }
    }

    private function preventSelf(User $user): void
    {
        if ($user->id === auth()->id()) {
            abort(403, 'You cannot perform this action on your own account.');
        }
    }

    private function preventSelfRoleChange(User $user): void
    {
        // Allow editing own non-role fields; role change on self is blocked below at form level
    }
}
