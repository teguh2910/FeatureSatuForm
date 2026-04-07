<?php

namespace Teguh\FeatureSatuForm\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $superAdminUsername = env('SUPER_ADMIN_USERNAME', 'admin');
        $currentUsername = (string) $request->session()->get('admin_username', '');

        if ($currentUsername !== $superAdminUsername) {
            abort(403, 'Hanya admin utama yang bisa mengakses User Management.');
        }

        return $next($request);
    }
}
