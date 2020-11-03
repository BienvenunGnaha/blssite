<?php

namespace App\Http\Controllers\Auth;

use App\GeneralSetting;
use App\User;
use App\Http\Controllers\Controller;
use App\WithdrawMethod;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Subscriber;
use App\Plan;
use App\Gateway;
use App\CouponStripe;

use Stripe\Stripe;
use Stripe\Customer;
use Stripe\SetupIntent;
use Stripe\Subscription;
use Stripe\OAuth;
use Stripe\PaymentIntent;
use Slim\App;
use Slim\Http\Request as SlimRequest;
use Slim\Http\Response;

use App\Http\Traits\Matrix;


class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    use Matrix;

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
        $this->middleware(['guest']);
        $this->middleware('regStatus')->except('registrationNotAllowed');
    }

    public function showRegistrationForm($ref = null)
    {
        $page_title = "Sign Up";
        $plan = Plan::whereStatus(1)->first();
        $discount = null;
        return view(activeTemplate() . 'user.auth.register', compact('page_title', 'plan', 'discount'));
    }

    public function showRegistrationFormRef($username){


  $ref_user = User::where('username', $username)->first();
        if (isset($ref_user)){
            $page_title = "Sign Up";
             $plan = Plan::whereStatus(1)->first();
            if ($ref_user->plan_id == 0){
                
                $notify[] = ['error', $ref_user->username.' did not in subscribed in any plan'];
                return redirect()->route('user.register')->withNotify($notify);
            }
            $discount = null;
            if($ref_user instanceof User){
                $cpStripe = CouponStripe::where('user_id', $ref_user->id)->first();
                if($cpStripe instanceof CouponStripe){
                    if($cpStripe->percent_off !== null){
                        $discount = (string)$cpStripe->percent_off.'%';
                    }
                    
                    if($cpStripe->amount_off !== null){
                        $discount = (string)$cpStripe->amount_off.'€';
                    }
                }
            }
            return view(activeTemplate().'.user.auth.register',compact('page_title', 'ref_user', 'plan', 'discount'));
        }else{
            return redirect()->route('user.register');
        }
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'firstname' => 'required|string|max:60',
            'lastname' => 'required|string|max:60',
            'country' => 'required|string|max:80',
            'email' => 'required|string|email|max:160|unique:users',
            'mobile' => 'required|string|max:30|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'username' => 'required|string|unique:users|min:6',
            'accept_term' => 'required',
            'plan_id' => 'required',
            'stripe_cus' => 'required',
            'pm' => 'required'
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $gnl = GeneralSetting::first();
        $subscriber = Subscriber::where('email', $data['email'])->first();
        if(!$subscriber){
            $subs = new Subscriber();
            $subs->email = $data['email'];
            $subs->save();
        }

        //dd($data);
        if($data['ref_id'] == '0' && $data['ref_bls'] !== null){
            $refU = User::where('username', $data['ref_bls'])->first();
            if($refU instanceof User){
                $data['ref_id'] = (string)$refU->id;
            }
        }
        //dd($data['ref_id']);
        $plan = Plan::find($data['plan_id']);
        if($plan){
            $settings = json_decode($plan->plan_settings);
            $gwStripe = Gateway::where('alias', '=', 'Stripe')->where('status', '=', 1)->first();
            $parameter = json_decode($gwStripe->parameter_list);
            Stripe::setApiKey($parameter->secret_key->value);
            $alias = $gwStripe->alias;
            
            try {
                Customer::update(
                  $data['stripe_cus'],
                  ['invoice_settings' => ['default_payment_method' => $data['pm']]]
                );
            } catch (CardException $e) {
                $notify[] = ['danger', 'Could not create subscription with this payment method.'];
                return back()->withNotify($notify);
            }

            $stripeSubsData = [
                'customer' => $data['stripe_cus'],
                'items' => [['plan' => $settings->$alias]],
                'off_session' => TRUE
            ];

            $cpStripe = null;
            
            if($data['ref_id'] !== '' || $data['ref_id'] !== null){
                $userReferer = User::find((int)$data['ref_id']);
                if($userReferer instanceof User){
                    $cpStripe = CouponStripe::where('user_id', $userReferer->id)->first();
                    if($cpStripe instanceof CouponStripe){
                        $stripeSubsData['coupon'] = $cpStripe->id_coupon;
                    }
                }
            }
            try {
                $subscription = Subscription::create($stripeSubsData);
            
            if($subscription->status == 'active' || $subscription->status == 'succeeded'){
                $user = User::create([
                    'ref_id' => $data['ref_id'],
                    'firstname' => $data['firstname'],
                    'lastname' => $data['lastname'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                    'username' => $data['username'],
                    'mobile' => $data['mobile'],
                    'accept_term' => $data['accept_term'],
                    'accept_news' => $data['accept_news'],
                    'address' => [
                        'address' => '',
                        'state' => '',
                        'zip' => '',
                        'country' => '',
                        'city' => '',
                    ],
                    'status' => 1,
                    'ev' =>  $gnl->ev ? 0 : 1,
                    'sv' =>  $gnl->sv ? 0 : 1,
                    'ts' => 0,
                    'tv' => 1,
                    'temp' => 0,
                    'balance' => 0.00,
                    'plan_id' => $data['plan_id'],
                    'stripe_cus' => $data['stripe_cus'],
                    'stripe_subs' => $subscription->id,
                ]);

                $charge = 0;

                if($cpStripe !== null){
                    if($cpStripe->percent_off !== null){
                        $charge = $plan->price - ($plan->price * ($cpStripe->percent_off / 100));
                    }
                    
                    if($cpStripe->amount_off !== null){
                        $charge = $plan->price - $cpStripe->amount_off;
                    }
                }
                
                if ($user) {
                    $user->transactions()->create([
                        'trx' => 'Subscription '.$plan->name,
                        'user_id' => $user->id,
                        'amount' => $charge == 0 ? $plan->price : $charge,
                        'main_amo' => $plan->price,
                        'balance' => 0.00,
                        'title' => 'Purchased ' . $plan->name,
                        'charge' => $charge,
                        'type' => 7,
                    ]);
                    //hit position start
                    $this->get_position($user->id);
                    //hit position end



                    //hit position start
                    //$this->give_referral_commission($user->id, $plan->id);
                    //hit position end

                    /// //hit ref level commission start
                    $this->give_level_commission($user->id, $plan->id);
                    //hit ref level commission end



                    send_email($user, 'pan_purchased', [

                        'name' => $user->firstname.' '.$user->lastname,
                        'price' => $plan->price . ' ' . $gnl->cur_text,
                        'balance_now' => 0.00,

                    ]);

                    /*send_sms($user, 'pan_purchased', [

                        'name' => $plan->name,
                        'price' => $plan->price . ' ' . $gnl->cur_text,
                        'balance_now' => 0.00,
                    ]);*/

                    
                    return $user;
                }
             }
            } catch (\Exception $e) {
                //throw $th;
            }

            
         }

        return User::create([
            'ref_id' => $data['ref_id'],
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'username' => $data['username'],
            'mobile' => $data['mobile'],
            'accept_term' => $data['accept_term'],
            'accept_news' => $data['accept_news'],
            'address' => [
                'address' => '',
                'state' => '',
                'zip' => '',
                'country' => '',
                'city' => '',
            ],
            'status' => 1,
            'ev' =>  $gnl->ev ? 0 : 1,
            'sv' =>  $gnl->sv ? 0 : 1,
            'ts' => 0,
            'tv' => 1,
            'temp' => 1,
            'plan_id' => 0,
        ]);
    }

    public function registered()
    {
        
        return redirect()->route('user.home');
    }
}
