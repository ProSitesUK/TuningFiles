<?php

namespace App\Http\Middleware;

use App\Models\ResellerProfile;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenantDomain
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();

        // Skip for the main domain
        $mainDomain = parse_url(config('app.url'), PHP_URL_HOST);
        if ($host === $mainDomain) {
            return $next($request);
        }

        // Look up tenant by custom domain
        $tenant = ResellerProfile::where('custom_domain', $host)
            ->where('domain_verified', true)
            ->where('is_active', true)
            ->first();

        if ($tenant) {
            // Store tenant in request for controllers to use
            $request->attributes->set('tenant', $tenant);
            // Could redirect to /t/{slug} or serve directly
        }

        return $next($request);
    }
}
