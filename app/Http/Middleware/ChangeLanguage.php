<?php

namespace App\Http\Middleware;

use App\Language;
use Closure;
use Illuminate\Http\Request;

class ChangeLanguage
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
        removeSessions();
        //dd($request->server('HTTP_ACCEPT_LANGUAGE'));
        $lang = explode('-', explode(',', $request->server('HTTP_ACCEPT_LANGUAGE'))[0])[0];
        if($lang == 'fr'){
        //dd($lang);
            $language = Language::where('code', $lang)->first();
            if (!$language) $lang = 'en';
            app()->setLocale(session('lang', strtolower($lang)));
        }else{
            app()->setLocale(session('lang', $this->get_code()));
        }
        
        
        return $next($request);
    }
    public function get_code()
    {
        if (session()->has('user_lang')) {
            return session()->get('user_lang');
        }
        $language = Language::where('is_default', 1)->first();
        return $language ? $language->code : 'en';
    }
}
