<?php

namespace App\Http\Middleware;

use App\Helper\Util;
use Closure;

class CheckClientAdminAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // IF Server Type== Master then proceed,
        // Else: return;
        if (config('constants.server_type') == Util::SERVER_TYPE_MASTER) {
            return $next($request);
        } else {
            return abort(404, "You are not allowed to access the admin panel on this server.");
        }
    }
}
