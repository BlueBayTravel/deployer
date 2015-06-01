<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

/**
 * Middleware to prevent CSRF
 */
class VerifyCsrfToken extends BaseVerifier
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
        if ($this->excludedRoutes($request)) {
            return $this->addCookieToResponse($request, $next($request));
        }

        return parent::handle($request, $next);
    }

    /**
     * Determines whether or not the request should be excluded from CSRF protection
     *
     * @param \Illuminate\Http\Request $request
     * @return boolean
     */
    protected function excludedRoutes($request)
    {
        $routes = [
            'deploy/*',
            'api/*'
        ];

        foreach ($routes as $route) {
            if ($request->is($route)) {
                return true;
            }
        }

        return false;
    }
}
