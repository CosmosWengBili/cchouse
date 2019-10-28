<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class InternalProtect
{
    private const USERNAME   = 'admin';
    private const PASSWORD   = 'password';
    private const BYPASS_IPS = ['127.0.0.1', '192.168.3.254', '192.168.10.1'];

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // 若需要驗證驗證未通過
        if ($this->needValidate($request) && ! $this->isValidUser($request)) {
            throw new UnauthorizedHttpException('Basic');
        }

        return $next($request);
    }

    private function needValidate(Request $request)
    {
        $ip = $request->ip();
        if (in_array($ip, self::BYPASS_IPS)) {
            return false;
        }

        return true;
    }

    private function isValidUser(Request $request)
    {
        $user     = $request->header('PHP_AUTH_USER');
        $password = $request->header('PHP_AUTH_PW');

        if ($user == self::USERNAME && $password == self::PASSWORD) {
            return true;
        }

        return false;
    }
}
