<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminSecret
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Check if the 'secret' is in the session OR provided in the URL
        $key = 'Zeelot2024'; // Change this to your actual secret password

        if ($request->query('key') === $key) {
            session(['admin_auth' => true]);
        }

        if (!session('admin_auth')) {
            abort(403, 'Unauthorized. Please provide the secret key.');
        }

        return $next($request);
    }
}
