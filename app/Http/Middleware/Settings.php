<?php

namespace App\Http\Middleware;

use Closure;

class Settings
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if($request->is('api/settings/database/fetch')) {
            if (\Auth::user()->hasPermissionTo("View Database")) {
                return $next($request);
            } else {
                abort('403');
            }
        } 
        if($request->is('api/settings/database/create')) {
            if (\Auth::user()->hasPermissionTo("Create Database")) {
                return $next($request);
            } else {
                abort('403');
            }
        } 
        if($request->is('api/settings/database/update')) {
            if (\Auth::user()->hasPermissionTo("Update Database")) {
                return $next($request);
            } else {
                abort('403');
            }
        } 
        if($request->is('api/settings/database/delete')) {
            if (\Auth::user()->hasPermissionTo("Delete Database")) {
                return $next($request);
            } else {
                abort('403');
            }
        } 
        if($request->is('api/settings/database/testdb')) {
            if (\Auth::user()->hasPermissionTo("Test Database")) {
                return $next($request);
            } else {
                abort('403');
            }
        } 

        return $next($request);
    }
}
