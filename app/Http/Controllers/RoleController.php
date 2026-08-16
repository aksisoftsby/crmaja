<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeRoles($request);

        return view('roles.index', ['roles' => Role::query()->withCount('users')->where('guard_name', 'web')->orderBy('name')->get()]);
    }

    public function create(Request $request): View
    {
        $this->authorizeRoles($request);

        return view('roles.create', ['role' => new Role, 'permissions' => $this->permissions()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeRoles($request);
        $data = $this->validatedData($request);
        $role = Role::create(['name' => $data['name'], 'guard_name' => 'web']);
        $role->syncPermissions($data['permissions'] ?? []);

        return to_route('roles.index')->with('status', 'Role berhasil ditambahkan.');
    }

    public function edit(Request $request, Role $role): View
    {
        $this->authorizeRoles($request);

        return view('roles.edit', ['role' => $role->load('permissions'), 'permissions' => $this->permissions()]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $this->authorizeRoles($request);
        abort_if($role->name === 'Super Admin', 422, 'Permission Super Admin dikelola melalui seeder dan tidak dapat diubah dari UI.');
        $data = $this->validatedData($request, $role);
        $role->update(['name' => $data['name']]);
        $role->syncPermissions($data['permissions'] ?? []);

        return to_route('roles.index')->with('status', 'Role berhasil diperbarui.');
    }

    public function destroy(Request $request, Role $role): RedirectResponse
    {
        $this->authorizeRoles($request);
        abort_if($role->name === 'Super Admin', 422, 'Role Super Admin tidak dapat dihapus.');
        abort_if($role->users()->exists(), 422, 'Pindahkan seluruh staff dari role ini sebelum menghapusnya.');
        $role->delete();

        return to_route('roles.index')->with('status', 'Role berhasil dihapus.');
    }

    private function authorizeRoles(Request $request): void
    {
        abort_unless($request->user()?->can('roles.manage'), 403);
    }

    private function permissions()
    {
        return Permission::query()->where('guard_name', 'web')->orderBy('name')->get()->groupBy(fn (Permission $permission) => str($permission->name)->before('.')->headline()->toString());
    }

    private function validatedData(Request $request, ?Role $role = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('roles', 'name')->ignore($role)],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::exists('permissions', 'name')->where('guard_name', 'web')],
        ]);
    }
}
