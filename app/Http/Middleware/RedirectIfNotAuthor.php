<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class RedirectIfNotAuthor
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
            // Check the gate
            $response = Gate::inspect('accessDashboard');
            if ($response->denied()) {
                Session::flash('error_gate', $response->message());
                return redirect()->route('home');
            }
        } else {
            // If user is not authenticated, redirect to login.
             Session::flash('error_gate', 'You must be logged in to access this page and be an author.');
            return redirect()->route('login')->with('error_gate', 'You must be logged in to access this page and be an author.');
        }

        return $next($request);
    }
}