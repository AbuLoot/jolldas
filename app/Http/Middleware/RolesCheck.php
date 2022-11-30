<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;

class RolesCheck
{
    protected $auth;

    /**
     * Creates a new instance of the middleware.
     *
     * @param Guard $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $roles)
    {
        if (strpos($roles, '|')) {
            $roles = explode('|', $roles);
        } else {
            $roles = [$roles];
        }

        foreach($roles as $role) {

            if (in_array($role, $request->user()->roles()->pluck('name')->toArray())) {
                $result = true;
                break;
            }

            $result = false;
        }

        if ($this->auth->guest() || !$result) {
            abort(403);
        }

        return $next($request);
    }
}
