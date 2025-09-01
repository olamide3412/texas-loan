<?php

namespace App\Providers;

use App\Enums\GenderEnums;
use App\Enums\OccupationEnums;
use App\Enums\RoleEnums;
use App\Enums\StatesEnums;
use App\Helpers\EnumHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading();
        //
        Model::unguard();

        Paginator::useTailwind();
         // Set the timezone for Carbon (and all date/time functions)
        config(['app.timezone' => 'Africa/Lagos']);
        date_default_timezone_set(config('app.timezone'));
        Carbon::setLocale(config('app.locale'));


        Inertia::share([
            'enums' => fn () => EnumHelper::options([
                'roles' => RoleEnums::class,
                'genders' => GenderEnums::class,
                'states' => StatesEnums::class,
                'occupations' => OccupationEnums::class,
                // Add more enums here as needed
            ])
        ]);

    }
}
