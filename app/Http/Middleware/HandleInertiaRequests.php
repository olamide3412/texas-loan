<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $this->getAuthUser('web'),
                'client' => $this->getAuthUser('client'),
                'check' => $this->isAuthenticated(),
                'type' => $this->getAuthType(),
            ],
             'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'message' => fn () => $request->session()->get('message')
            ],
            'support' => [
                'phone' => '+2348066950049',
                'phone_whatsapp' => '2348066950049', //'2348151702840',
                'phone_formatted' => '+234 806695 0049',
                'email' => 'support@texasloan.ng',
                'location' => 'Kano State, Nigeria'
            ],
            'csrf_token' => csrf_token(),
            'turnstileSiteKey' => config('services.turnstile.site_key'),
        ];
    }

    protected function getAuthUser($guard)
    {
        return fn () => Auth::guard($guard)->check() ? Auth::guard($guard)->user() : null;
    }

    protected function isAuthenticated()
    {
        return fn () => Auth::guard('web')->check() || Auth::guard('client')->check();
    }

    protected function getAuthType()
    {
        if (Auth::guard('web')->check()) return 'staff';
        if (Auth::guard('client')->check()) return 'client';
        return null;
    }
}
