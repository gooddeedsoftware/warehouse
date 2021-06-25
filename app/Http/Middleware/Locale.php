<?php
namespace App\Http\Middleware;

use App;
use Closure;
use Config;
use Session;

class Locale
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
        $language = Session::get('language', Config::get('app.locale'));
        App::setLocale($language);
        $user = \Auth::user();
        if ($user != null && ($user->email == 'vitali@avalia.no' || $user->email == 'erlend@gantic.no' )) {
            \Debugbar::enable();
        } else {
            \Debugbar::disable();
        }
        return $next($request);
    }
}
