<?php

namespace App\Http\Middleware;

use Closure;
use Log;
use Illuminate\Support\Facades\DB;

class SourceOrgMiddleware extends BaseMiddleware {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $request = parent::handle($request, $next);
        $request->__source = 'org';
        return $next($request);
    }
}
