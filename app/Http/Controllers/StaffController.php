<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class StaffController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeStaff($request, 'staff.view_all');

        $staff = User::query()->with('roles')->latest()->paginate(20);

        return view('staff.index', compact('staff'));
    }

    public function create(Request $request): View
    {
        $this->authorizeStaff($request, 'staff.create');

        return view('staff.create', ['staff' => new User, 'roles' => $this->roles()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeStaff($request, 'staff.create');
        $data = $this->validatedData($request);

        $staff = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_active' => $request->boolean('is_active', true),
            'email_verified_at' => now(),
        ]);
        $staff->syncRoles($data['roles']);

        return to_route('staff.index')->with('status', 'Akun staff berhasil ditambahkan.');
    }

    public function edit(Request $request, User $staff): View
    {
        $this->authorizeStaff($request, 'staff.edit');
        $staff->load('roles');

        return view('staff.edit', ['staff' => $staff, 'roles' => $this->roles()]);
    }

    public function update(Request $request, User $staff): RedirectResponse
    {
        $this->authorizeStaff($request, 'staff.edit');
        $data = $this->validatedData($request, $staff);

        abort_if($staff->is($request->user()) && ! $request->boolean('is_active', true), 422, 'Anda tidak dapat menonaktifkan akun sendiri.');
        $staff->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'is_active' => $request->boolean('is_active', true),
        ]);
        if (filled($data['password'] ?? null)) {
            $staff->password = Hash::make($data['password']);
        }
        $staff->save();

        if (! ($staff->hasRole('Super Admin') && ! in_array('Super Admin', $data['roles'], true))) {
            $staff->syncRoles($data['roles']);
        }

        return to_route('staff.index')->with('status', 'Akun staff berhasil diperbarui.');
    }

    public function destroy(Request $request, User $staff): RedirectResponse
    {
        $this->authorizeStaff($request, 'staff.delete');
        abort_if($staff->is($request->user()), 422, 'Anda tidak dapat menonaktifkan akun sendiri.');
        abort_if($staff->hasRole('Super Admin'), 422, 'Akun Super Admin tidak dapat dinonaktifkan melalui tindakan ini.');

        $staff->update(['is_active' => false]);

        return to_route('staff.index')->with('status', 'Akun staff telah dinonaktifkan.');
    }

    private function authorizeStaff(Request $request, string $permission): void
    {
        abort_unless($request->user()?->can($permission), 403);
    }

    private function roles()
    {
        return Role::query()->where('guard_name', 'web')->orderBy('name')->get();
    }

    private function validatedData(Request $request, ?User $staff = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($staff)],
            'password' => [$staff ? 'nullable' : 'required', 'confirmed', 'min:12'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['string', Rule::exists('roles', 'name')->where('guard_name', 'web')],
        ]);
    }
}
