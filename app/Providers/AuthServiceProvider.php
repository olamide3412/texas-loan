<?php

namespace App\Providers;

use App\Enums\RoleEnums;
use App\Models\Client;
use App\Models\Order;
use App\Models\Staff;
use App\Policies\ClientPolicy;
use App\Policies\OrderPolicy;
use App\Policies\StaffPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ProvidersAuthServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ProvidersAuthServiceProvider
{
    protected $policies = [
        Staff::class => StaffPolicy::class,
        Client::class => ClientPolicy::class,
        Order::class => OrderPolicy::class

        // Add other policies here as needed
    ];
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('admin', function () {
            $user = Auth::user();

            if (is_null($user)) {
                return false; // Deny access if no user is authenticated
            }

            return $user->role === RoleEnums::SuperAdministrator->value ||
                   $user->role === RoleEnums::Administrator->value; // Assuming you have a role for students
        });

        Gate::define('super_admin', function () {
            $user = Auth::user();

            if (is_null($user)) {
                return false; // Deny access if no user is authenticated
            }

            return $user->role === RoleEnums::SuperAdministrator->value; // Assuming you have a role for students
        });
    }
}
