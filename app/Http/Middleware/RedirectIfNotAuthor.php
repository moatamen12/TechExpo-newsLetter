<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class RedirectifNotAuthor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): SymfonyResponse
    {
        if (Auth::check()) {
            // Check the gate. Auth::user() is automatically passed to the gate.
            if (Gate::denies('accessDashboard')) {
                return redirect()->route('Home')->with('error', 'You need a writer profile to access the dashboard.');
            }
        } else {
            // If user is not authenticated, redirect to login.
            // This middleware should typically run after 'auth' middleware.
            return redirect()->route('login');
        }

        return $next($request);
    }
}