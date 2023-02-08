<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tightenco\Ziggy\Ziggy;
use Illuminate\Support\Facades\Auth;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    private function gates()
  {

    $gates = array();

    if (Auth::user()) {
      foreach (Auth::user()->features as $feature) {
        $gates[$feature->name] = true;
      }

      $isAdmin = Auth::user()->role->name == 'Administrator';
      $isRoot = Auth::user()->role->name == 'Root';

      $gates['see_stats'] = $isAdmin || $isRoot;
      $gates['edit_users'] = $isAdmin || $isRoot;

      $threadCount = Auth::user()->threads->count();
      $threadLimit = Auth::user()->tier->thread_limit;

      $gates['add_threads'] = $threadLimit == -1 || $threadCount < $threadLimit;
    }

    return $gates;
  }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user(),
            ],
            'auth.user' => fn () => $request->user()
        ? $request->user()->only('id', 'name', 'email')
        : null,
      'auth.can' => $this->gates(),
      'auth.thread_limit' => $request->user()
        ? $request->user()->tier->thread_limit
        : null,
      'auth.thread_count' => $request->user()
        ? $request->user()->threads->count()
        : null,
            'ziggy' => function () use ($request) {
                return array_merge((new Ziggy)->toArray(), [
                    'location' => $request->url(),
                ]);
            },
        ]);
    }
}
