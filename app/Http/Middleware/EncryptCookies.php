<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class EncryptCookies
{
    /**
     * Encrypt the cookies on the response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        Cookie::queue(Cookie::forever('laravel_session', 'some_session_value'));

        return $next($request);
    }
}
