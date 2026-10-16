<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/** Ion Auth group (role): superadmin, admin, contentmanager, ... */
class Group extends Model
{
    protected $table = 'groups';

    public $timestamps = false;

    protected $fillable = ['name', 'description'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'users_groups', 'group_id', 'user_id');
    }
}
