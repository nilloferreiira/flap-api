<?php

namespace App\Observers\Role;

use App\Models\Role\Role;
use App\Models\User;

class RoleObserver
{
    public function deleted(Role $role)
    {
        User::where('role_id', $role->id)->update(['role_id' => 3]);
    }
}
