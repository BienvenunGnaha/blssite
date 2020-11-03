<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class DeleteUnsubscriber
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
        if (Auth::check()) {
            $user = Auth()->user();
           /*if($user->plan_id == '0' && (int)$user->temp == 1){
              dd((int)$user->temp);
           }*/
         
             if((int)$user->plan_id === 0 && (int)$user->temp === 1){
                 $name = \Route::currentRouteName();
                if(!in_array($name, ['user.first_login', 'user.register', 'user.login','user.plan.index', 'user.plan.purchase'])){
                  $user->delete();
                  return redirect()->route('user.login');
                }
             }

             
        }
        return $next($request);
    }
}
				