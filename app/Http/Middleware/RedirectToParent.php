<?php

namespace App\Http\Middleware;

use Closure;

class RedirectToParent
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
        [$controller, $action] = explode('@', \Route::currentRouteAction());
        $controller = class_basename($controller);
        $uri = \Request::getRequestUri();

        switch ($action) {
            case 'update':
            case 'store':
                // a form was sent and needs the page to be redirected.
                // in order to avoid messing up the controller functions, an additional parameter is passed through request
                // rather than make an after middleware and do auto redirect.

                // if the previous page was a parent page, the request will contain a _redirect session.
                // otherwise, go to that resources' index page by default 
                $request->_redirect = session()->get('_redirect', action($controller . '@index'));
                break;

            case 'index':
            case 'show':
                // remember which page to redirect to
                session(['_redirect' => $uri]);
                break;

            default:
                break;
        }
        
        return $next($request);
    }
}
