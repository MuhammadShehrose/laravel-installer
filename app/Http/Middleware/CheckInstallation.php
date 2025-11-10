<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckInstallation
{
    /**
     * Handle an incoming request.
     *
     * If the app is already installed (storage/installed exists) then
     * redirect all /install/* requests to the site root.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // allow access to installer when installed file does NOT exist
        if (! file_exists(storage_path('installed'))) {
            return $next($request);
        }

        // If installed, block install routes and redirect to homepage.
        return redirect()->to('/');
    }
}
