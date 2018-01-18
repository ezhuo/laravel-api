<?php

namespace App\Http\Middleware;

use Closure;
use Log;

class BaseMiddleware {

    public function handle($request, Closure $next) {
        return $request;
    }
}
