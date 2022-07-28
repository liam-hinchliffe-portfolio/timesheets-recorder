<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSoftDeletedUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()->deleted_at != null) {
            Auth::logout();
            Session::flush();
            Session::regenerate();
            return redirect()->route('login')->withErrors(['suspended' => 'Your account is deactivated']);
        } else{
            return $next($request);
        }
    }
}
