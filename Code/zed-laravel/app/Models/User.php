<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * CMS user, stored in the existing Ion Auth "users" table (shared with the old
 * CodeIgniter CMS during the switch-over). Passwords are bcrypt ($2y$), which
 * Laravel's hasher verifies as they are.
 *
 * Roles are the Ion Auth groups (groups / users_groups):
 * superadmin, admin, contentmanager.
 */
class User extends Authenticatable
{
    use Notifiable;

    public const ROLES = ['superadmin', 'admin', 'contentmanager'];

    protected $table = 'users';

    // Ion Auth keeps unix timestamps in created_on / last_login
    public $timestamps = false;

    protected $fillable = [
        'username', 'email', 'password', 'first_name', 'last_name', 'company', 'phone', 'active', 'created_on', 'ip_address',
    ];

    protected $hidden = [
        'password', 'salt', 'remember_token', 'remember_code', 'activation_code', 'forgotten_password_code',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'active' => 'boolean',
        ];
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'users_groups', 'user_id', 'group_id');
    }

    /** @param string|array<int, string> $names */
    public function inGroup(string|array $names): bool
    {
        $names = (array) $names;

        return $this->groups->contains(fn (Group $group) => in_array($group->name, $names, true));
    }

    public function isSuperAdmin(): bool
    {
        return $this->inGroup('superadmin');
    }

    public function isAdmin(): bool
    {
        return $this->inGroup(['superadmin', 'admin']);
    }

    /** May this user sign in to the CMS? */
    public function canUseCms(): bool
    {
        return $this->active && $this->inGroup(self::ROLES);
    }

    /** The highest role, used for menus: superadmin > admin > contentmanager. */
    public function role(): ?string
    {
        foreach (self::ROLES as $role) {
            if ($this->inGroup($role)) {
                return $role;
            }
        }

        return null;
    }

    public function displayName(): string
    {
        $name = trim($this->first_name.' '.$this->last_name);

        return $name !== '' ? $name : (string) $this->email;
    }
}
