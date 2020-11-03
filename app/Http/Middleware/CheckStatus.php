<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckStatus
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
            
            if($user){
                if(!$user->stripe_connect_state){
                    $user->stripe_connect_state = uniqId(time());
                }

                if(!$user->last_pay){
                    $user->last_pay = new \DateTime();
                }

                $user->save();
            }

            if ($user->status  && $user->ev  && $user->sv  && $user->tv) {
                return $next($request);
            } else {
                return redirect()->route('user.authorization');
            }
        }
    }
}
