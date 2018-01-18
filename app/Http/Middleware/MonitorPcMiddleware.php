<?php

namespace App\Http\Middleware;

use Closure;
use Log;
use Illuminate\Support\Facades\DB;

class MonitorPcMiddleware extends BaseMiddleware {

    public function handle($request, Closure $next) {
        $request = parent::handle($request, $next);
        $request->__monitor = 'pc';
        return $next($request);
    }
}
