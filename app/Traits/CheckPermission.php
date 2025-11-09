<?php

namespace App\Traits;

trait CheckPermission
{
    public function checkPermission($user, $permission)
    {
        if (!$user->hasPermission($permission)) {
            return response()->json(['message' => 'Você não tem permissão para realizar esta ação'], 403);
        }
    }
}
