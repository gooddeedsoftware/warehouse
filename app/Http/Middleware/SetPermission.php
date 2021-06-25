<?php
/**
 * Added by David on 3.9.2018
 * Setting session for each request
 */
namespace App\Http\Middleware;

use App;
use Auth;
use Closure;
use Config;
use Session;
use App\Models\User;


class SetPermission
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
        if(@Auth::user() && (Session::get('usertypeId') != Auth::user()->usertype_id)) {
            $user = User::getUser(Auth::user()->id);
            if ($user) {
                Session::put('usertype', $user->usertype->type);            
                Session::put('usertypeId', $user->usertype->id);
                Session::put('currentUserID', $user->id);  
                Session::put('paginate_size', $user->pagination_size);            
            }
        }
        return $next($request);
    }

}