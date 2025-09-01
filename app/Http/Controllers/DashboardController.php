<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnums;
use App\Models\Log;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $users = User::latest()->limit(25)->get();
        $usersCount = User::count();
        $adminsCount = User::where('role', RoleEnums::Administrator->value)->count();
        $superAdminsCount = User::where('role', RoleEnums::SuperAdministrator->value)->count();
        $activeResponseCount = 0; // WhatsAppResponse::where('is_active', true)->count();
        $inactiveResponseCount = 0; //WhatsAppResponse::where('is_active', false)->count();
        $exchangeRateCount = 0; // ExchangeRate::count();

        $logsCount = Log::count();

        //dd($users->toArray());
        return inertia('Auth/Dashboard', [
            'counts' => [
                'users' => $usersCount,
                'admins' => $adminsCount,
                'superAdmins' => $superAdminsCount,
                'activeResponseCount' => $activeResponseCount,
                'inactiveResponseCount' => $inactiveResponseCount,
                'exchangeRateCount'  =>  $exchangeRateCount,
                'logs' => $logsCount,
            ],
        ]);
    }
}
