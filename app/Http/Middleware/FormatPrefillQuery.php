<?php

namespace App\Http\Middleware;

use Closure;

class FormatPrefillQuery
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
        $request->prefill = [];
        $prefills = preg_split('/;/', $request->query('prefill'), null, PREG_SPLIT_NO_EMPTY);
        foreach ($prefills as $pair) {
            [$relation, $id] = preg_split('/:/', $pair, null, PREG_SPLIT_NO_EMPTY);
            $request->prefill[$relation] = $id;
        }

        return $next($request);
    }
}
