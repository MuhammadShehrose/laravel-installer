<?php

namespace Installer\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectIfNotInstalled
{
    public function handle(Request $request, Closure $next)
    {
        $envExists = file_exists(base_path('.env'));
        $installedExists = file_exists(storage_path('installed'));

        if (! $envExists || ! $installedExists) {
            // Avoid redirect loop for installer routes
            if (! $request->is('install*') && ! $request->is('api/*')) {
                return redirect()->route('install.welcome');
            }
        }

        return $next($request);
    }
}
