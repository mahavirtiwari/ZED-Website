<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** CMS user accounts (super admins only). Uses the Ion Auth users / groups tables. */
class UserController extends Controller
{
    public const ROLE_LABELS = ['superadmin' => 'Super admin', 'admin' => 'Admin', 'contentmanager' => 'Content manager'];

    public function index(): View
    {
        return view('cms.users.index', ['users' => User::query()->with('groups')->orderBy('first_name')->get()]);
    }

    public function create(): View
    {
        return view('cms.users.form', ['user' => new User(['active' => true]), 'role' => 'contentmanager']);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, new User);
        $user = new User;
        $this->fill($user, $data);
        $user->forceFill(['created_on' => time(), 'ip_address' => (string) $request->ip(), 'username' => $data['email']])->save();
        $this->syncRole($user, $data['role']);

        return redirect()->route('cms.users.index')->with('status', 'User added.');
    }

    public function edit(User $user): View
    {
        return view('cms.users.form', ['user' => $user, 'role' => $user->role() ?? 'contentmanager']);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $this->validated($request, $user);
        if ($user->is($request->user()) && ($data['role'] !== 'superadmin' || ! $data['active'])) {
            return back()->withErrors(['role' => 'You cannot remove your own super admin role or deactivate yourself.']);
        }
        $this->fill($user, $data);
        $user->save();
        $this->syncRole($user, $data['role']);

        return redirect()->route('cms.users.index')->with('status', 'User updated.');
    }

    private function validated(Request $request, User $user): array
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'company' => ['nullable', 'string', 'max:100'],
            'role' => ['required', Rule::in(array_keys(self::ROLE_LABELS))],
            'password' => [$user->exists ? 'nullable' : 'required', 'confirmed', AuthController::passwordRule()],
        ]);
        $data['active'] = $request->boolean('active');

        return $data;
    }

    private function fill(User $user, array $data): void
    {
        $user->forceFill([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'] ?? '',
            'email' => $data['email'],
            'phone' => $data['phone'] ?? '',
            'company' => $data['company'] ?? '',
            'active' => $data['active'],
        ]);
        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
    }

    private function syncRole(User $user, string $role): void
    {
        $group = Group::query()->firstOrCreate(['name' => $role], ['description' => self::ROLE_LABELS[$role]]);
        $keep = $user->groups()->whereNotIn('name', User::ROLES)->pluck('groups.id')->all();
        $user->groups()->sync(array_merge($keep, [$group->id]));
    }
}
