<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function view(User|Client $user, Order $order): bool
    {
        // If it's a client, allow only if the order belongs to them
        if ($user instanceof Client) {
            return $order->client_id === $user->id;
        }

        // If it's a user (admin/staff), allow if they have permission
        if ($user instanceof User) {
            return $user->can('view-order'); // or return true if all staff can view
        }

        return false;
    }
}
