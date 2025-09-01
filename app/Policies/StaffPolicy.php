<?php

namespace App\Policies;

use App\Enums\Admin\RoleEnums;
use App\Enums\RoleEnums as EnumsRoleEnums;
use App\Models\Staff;
use App\Models\User;

class StaffPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function view(User $user, Staff $model) : bool
    {
        return $user->role === EnumsRoleEnums::SuperAdministrator->value ||
            $user->role === EnumsRoleEnums::Administrator->value
            || $user->id === $model->user_id;
    }
}
