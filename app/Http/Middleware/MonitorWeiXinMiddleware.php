<?php

namespace App\Http\Middleware;

use Closure;
use Log;
use Illuminate\Support\Facades\DB;

class MonitorWeiXinMiddleware extends BaseMiddleware {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $request = parent::handle($request, $next);
        $request->__monitor = 'weixin';
        return $next($request);
    }
}
