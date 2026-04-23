<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
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
                'user' => $request->user(),
            ],
            'appConfig' => [
                'demo_mode' => config('app.demo_mode'),
                'demo_bypass_face_verification' => config('app.demo_bypass_face_verification'),
                'demo_bypass_geofence' => config('app.demo_bypass_geofence'),
            ],
            'notifications' => fn () => $request->user()
                ? [
                    'unread_count' => $request->user()->unreadNotifications()->count(),
                    'items' => $request->user()
                        ->notifications()
                        ->latest()
                        ->limit(8)
                        ->get()
                        ->map(fn ($notification) => [
                            'id' => $notification->id,
                            'title' => data_get($notification->data, 'title', 'Notifikasi'),
                            'message' => data_get($notification->data, 'message', ''),
                            'type' => data_get($notification->data, 'type', 'general'),
                            'action' => data_get($notification->data, 'action'),
                            'read_at' => $notification->read_at,
                            'created_at' => optional($notification->created_at)->toIso8601String(),
                        ])
                        ->values(),
                ]
                : [
                    'unread_count' => 0,
                    'items' => [],
                ],
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ];
    }
}
