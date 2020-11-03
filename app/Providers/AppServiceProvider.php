<?php

namespace App\Providers;

use App\Frontend;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Http\Traits\Matrix;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\User;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Gateway;
use App\Subscriber;
use App\Withdrawal;
use App\WithdrawMethod;
use App\Trx;

class AppServiceProvider extends ServiceProvider
{

    use Matrix;
    
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        $date_users = User::where('plan_id', '!=',0)->where('status', 1)->where('is_test', 0)->get();
        $userToDelete = User::where('plan_id', 0)->get();
        //dd(User::first());
        foreach ($date_users as  $user) {
            $price = $this->pricePayToCustomer($user->id);
            

            if($price){
                $lastday = (new \DateTime($user->last_pay))->getTimestamp(); 
                $today = (new \DateTime())->getTimestamp();
                if($lastday-$today >= 2592000 ){
                    if($user->stripe_user_id){
                        $gateway = Gateway::where('alias', '=', 'Stripe')->where('status', '=', 1)->first();
                        $parameter = json_decode($gateway->parameter_list);
                        try{
                            $stripe = new \Stripe\StripeClient($parameter->secret_key->value);
                            if($price > 100000 && $price <= 101000){
                                $price = 100000;
                            }
                          
                          $stripe->transfers->create([
                            'amount' => $price*100,
                            'currency' => 'eur',
                            'destination' => $user->stripe_user_id,
                          ]);
                          $user->last_pay = new \DateTime();
                          $user->save();
                          $trx = new Trx();
                          $trx->user_id = $user->id;
                          $trx->amount = $price;
                          $trx->main_amo = $price;
                          $trx->charge = 0;
                          $trx->type = 25;
                          $trx->title = 'Monthly Withdraw';
                          $trx->trx = uniqid((string)time(),true);
                          $trx->save();
                          $withdraw = new Withdrawal();
                          $withdraw->user_id = $user->id;
                          $withdraw->method_id = WithdrawMethod::first()->id;
                          $withdraw->trx = $trx->id;
                          $withdraw->status = 1;
                          $withdraw->amount = $price;
                          $withdraw->main_amo = $price;
                          $withdraw->charge = 0;
                          $withdraw->rate = 0;
                          $withdraw->save();

                          
                        }catch(\Stripe\Exception\InvalidRequestException $e){

                        }catch(\Exception $e){

                        }
                        

                        
                    }
                    
                }
            }
            


        }

        

        view()->share(['general' => \App\GeneralSetting::first()]);
        view()->share(['lang' => \App\Language::all()]);

        view()->composer('partials.seo', function ($view) {
            $seo = \App\Frontend::where('key', 'seo')->first();
            $view->with([
                'seo' => $seo ? $seo->value : $seo,
            ]);
        });


        view()->composer(activeTemplate().'layouts.master',  function ($view) {
            $footer = \App\Frontend::where('key', 'footer.title')->first();
            $social = \App\Frontend::where('key', 'social.item')->get();

            $view->with([
                'footer' => $footer->value ,
                'social' => $social,
            ]);

        });

        view()->composer('admin.partials.sidenav', function ($view) {
            $view->with([
                'banned_users_count'           => \App\User::banned()->count(),
                'email_unverified_users_count' => \App\User::emailUnverified()->count(),
                'sms_unverified_users_count'   => \App\User::smsUnverified()->count(),
                'pending_withdrawals_count'    => \App\Withdrawal::pending()->count(),
            ]);
        });

        
    }
}
			