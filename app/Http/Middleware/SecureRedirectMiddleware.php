<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecureRedirectMiddleware
{
  /**
   * Handle an incoming request.
   *
   * @param Request $request
   * @param Closure $next
   *
   * @return mixed
   */
  public function handle(Request $request, Closure $next)
  {
    if (!$request->secure() && config('app.https')) {

      return redirect()->secure($request->path(), 301);
    }
    return $next($request);
  }
}
