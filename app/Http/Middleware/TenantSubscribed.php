<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantSubscribed
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->isReseller()) {
            return redirect('/login');
        }

        $profile = $user->resellerProfile;

        if (!$profile || $profile->hasExpired()) {
            return redirect()->route('reseller.plans')
                ->with('status', 'Please subscribe to access the portal.');
        }

        return $next($request);
    }
}
