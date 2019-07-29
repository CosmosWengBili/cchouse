<?php

namespace App\Http\Middleware;

use Closure;

class FormatWithQuery
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
        // verify with query first!

        $request->withNested = preg_split('/;/', $request->query('with'), null, PREG_SPLIT_NO_EMPTY);
        return $next($request);
    }
}
