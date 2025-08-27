<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthGate
{
    public function handle($request, Closure $next)
    {
        if (!session()->has('admin_employee_id') || !session()->has('event_id')) {
            return redirect()->route('login');
        }
        
        return $next($request);
    }
}
