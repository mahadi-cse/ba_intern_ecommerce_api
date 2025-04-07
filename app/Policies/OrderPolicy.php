<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAll(User $user)
    {
        return $user->type === 'admin';
    }

    public function update(User $user)
    {
        return $user->type === 'admin';
    }
}