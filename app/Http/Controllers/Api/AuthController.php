<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use lluminate\Support\Facades\Auth;
use Validator;
use App\User;
use JWTAuth;
use App\Http\Traits\Authorization;
use App\Http\Traits\Matrix;
use App\Subscriber;
use App\Plan;
use App\Gateway;
use App\GeneralSetting;
use App\PasswordReset;
use App\Twofa;

use Stripe\Stripe;
use Stripe\Customer;
use Stripe\SetupIntent;
use Stripe\Subscription;
use Stripe\OAuth;
use Stripe\PaymentIntent;
use Slim\App;
use Slim\Http\Request as SlimRequest;
use Slim\Http\Response;

class AuthController extends Controller
{

    use Authorization;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        //$this->middleware('auth:api', ['except' => ['login', 'register']]);
        $this->guard = "api";
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request){
    	$validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required|string',
        ]);

        /*if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }*/
        //die();
        //return response()->json(['error' => 'Unauthorized'], 200);
        $data = $request->request->all();
        $username = $data['username'];
        $password = $data['password'];
        $credentials = ['username' => $username, 'password' => $password];
        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }


        //$user = User::where('username')->first();

        return $this->createNewToken($token);
    }

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
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

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = null;
        $data = $request->all();

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
                return response()->json($notify);
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
                    'city' => $data['contry'],
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
                            
                    if ($user) {

                        $charge = 0;

                        if($cpStripe !== null){
                            if($cpStripe->percent_off !== null){
                                $charge = $plan->price - ($plan->price * ($cpStripe->percent_off / 100));
                            }
                            
                            if($cpStripe->amount_off !== null){
                                $charge = $plan->price - $cpStripe->amount_off;
                            }
                        }
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

                    
                    }
                }
            } catch (\Exception $e) {
                //throw $th;
            }

            
        }

        if($user){
            return response()->json([
                'message' => 'User successfully registered',
                'user' => $user
            ], 201);
        }else {
            return response()->json([
                'message' => "Your account hasn't been registered"
            ], 500);
        }
    }


    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        auth('api')->logout();

        return response()->json(['message' => 'User successfully signed out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        return $this->createNewToken(auth('api')->refresh());
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile() {
        return response()->json(auth('api')->user());
    }

    public function sendVerifyCode(Request $request)
    {
        $user = auth('api')->user();
        $data = $this->sendVfCode($request, $user);

        if ($data['code'] === 200) {
            return response()->json($data['notify'], 200);
        }else{
            return response()->json($data['notify'], 500);
        }
    }

    public function emailVerification(Request $request)
    {
        $request->validate([
            'email_verified_code' => 'required',
        ], [
            'email_verified_code.required' => 'Email verification code is required',
        ]);

        $user = auth('api')->user();
        
        if ($this->emailVf($request, $user)) {
            return response()->json(['message' => 'Verification code match!'], 200);
        }

        return response()->json(['message' => 'Verification code didn\'t match!'], 500);
    }

    public function smsVerification(Request $request)
    {
        $request->validate([
            'sms_verified_code' => 'required',
        ], [
            'sms_verified_code.required' => 'SMS verification code is required',
        ]);
        $user = auth('api')->user();

        if ($this->smsVf($request, $user)) {
            return response()->json(['message' => 'Verification code match!'], 200);
        }

        return response()->json(['message' => 'Verification code didn\'t match!'], 500);
    }

    public function g2faVerification(Request $request)
    {
        $user = auth('api')->user();

        $this->validate(
            $request,
            [
                'code' => 'required',
            ]
        );
        
        if ($this->g2faVf($request, $user)) {
            return response()->json(['message' => 'Verification code is correct'], 200);
        } else {
            return response()->json(['message' => 'Wrong Verification code'], 500);
        }
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => auth('api')->user(),
        ]);
    }

    public function sendResetLinkEmail(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|string',
        ]);
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            $notify[] = ['error', 'User not found.'];
            return response()->json($notify, 500);
        }

        PasswordReset::where('email', $user->email)->delete();

        $code = verification_code(6);

        PasswordReset::create([
            'email' => $user->email,
            'token' => $code,
            'created_at' => \Carbon\Carbon::now(),
        ]);

        send_email($user, 'ACCOUNT_RECOVERY_CODE', ['code' => $code]);

        $page_title = 'Account Recovery';
        $email = $user->email;
        $data = ['email' => $email, 'messeage' => 'Password reset email sent successfully.'];
        return response()->json($data);
    }

    public function verifyCode(Request $request)
    {
        $request->validate(['code' => 'required', 'email' => 'required']);
        if (PasswordReset::where('token', $request->code)->where('email', $request->email)->count() != 1) {
            $notify[] = ['error', 'Invalid token'];
            return response()->json($notify, 500);
        }
        $notify[] = ['success', 'You can change your password.'];
        return response()->json($notify);
    }

    public function resetpw(Request $request)
    {
        
        $request->validate($this->rules());
        $reset = PasswordReset::where('token', $request->token)->orderBy('created_at', 'desc')->first();
        if (!$reset) {
            $notify[] = ['error', 'Invalid code'];
            return response()->json($notify);
        }

        $user = User::where('email', $reset->email)->first();
        $user->password = bcrypt($request->password);
        $user->save();

        $general = GeneralSetting::first(['en', 'sn']);

        if ($general->en) {
            $msg =  'Password has been updated.';
            send_email($user, 'PASS_RESET');
        } else if ($general->sn) {
            $sms =  'Password has been updated.';
            send_sms($user, $sms);
        }

        $notify[] = ['success', 'Password Changed'];
        $reset->delete();
        return response()->json($notify);
    }

    public function verify2faToken(Request $request){

    }


    /**
     * Get the password reset validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ];
    }
}
