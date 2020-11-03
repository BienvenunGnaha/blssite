<?php

namespace App\Http\Controllers;

use App\Epin;
use App\GeneralSetting;
use App\Http\Traits\Matrix;
use App\PaymentProveImage;
use App\Plan;
use App\Trx;
use App\User;
use App\UserLogin;
use App\Gateway;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use Stripe\Stripe;
use Stripe\Customer;
use Stripe\SetupIntent;
use Stripe\Subscription;
use Stripe\OAuth;
use Stripe\PaymentIntent;
use Slim\App;
use Slim\Http\Request as SlimRequest;
use Slim\Http\Response;

class HomeController extends Controller
{
    use Matrix;


    function planIndex()
    {


        $data['page_title'] = "Plans";

        $data['plans'] = Plan::whereStatus(1)->get();


        if (auth()->user()->plan_id != 0) {

            $notify[] = ['error', 'Purchase not possible twice.'];
            return back()->withNotify($notify);

        }
        $user = User::find(Auth::id());
        $gateway = Gateway::where('alias', '=', 'Stripe')->where('status', '=', 1)->first();
        $parameter = json_decode($gateway->parameter_list);
        Stripe::setApiKey($parameter->secret_key->value);

        if(!$user->stripe_cus){
            $cus = Customer::create([
                'name' => $user->firstname.' '.$user->lastname,
                'email' => $user->email
            ]);
            $user->stripe_cus = $cus->id;
            $user->save();
        }

        $setupIntent = SetupIntent::create([ 'customer' => $user->stripe_cus]);

        $data['clientSecret'] = $setupIntent->client_secret;
        $data['publishable_key'] = $parameter->publishable_key->value;

        return view(activeTemplate() . '.user.plan', $data);
    }

    function payPop(Request $request)
    {
        $request = $request->request->all();
        $gateway = Gateway::where('alias', '=', 'Stripe')->where('status', '=', 1)->first();
        $parameter = json_decode($gateway->parameter_list);
        Stripe::setApiKey($parameter->secret_key->value);

            $cus = Customer::create([
                'name' => $request['firstname'].' '.$request['lastname'],
                'email' => $request['email']
            ]);
        $setupIntent = SetupIntent::create([ 'customer' => $cus->id]);

        $data['clientSecret'] = $setupIntent->client_secret;
        $data['publishable_key'] = $parameter->publishable_key->value;
        $html = view(activeTemplate() . '.user.auth.subscription', $data)->render();

        return response()->json(['cus' => $cus->id, 'html' => $html], 200);
    }

    function planStart()
    {


        $data['page_title'] = "Plans";

        $data['plans'] = Plan::whereStatus(1)->get();


        if (auth()->user()->plan_id != 0) {

            $notify[] = ['error', 'Purchase not possible twice.'];
            return back()->withNotify($notify);

        }
        $user = User::find(Auth::id());
        $gateway = Gateway::where('alias', '=', 'Stripe')->where('status', '=', 1)->first();
        $parameter = json_decode($gateway->parameter_list);
        Stripe::setApiKey($parameter->secret_key->value);

        if(!$user->stripe_cus){
            $cus = Customer::create([
                'name' => $user->firstname.' '.$user->lastname,
                'email' => $user->email
            ]);
            $user->stripe_cus = $cus->id;
            $user->save();
        }

        $setupIntent = SetupIntent::create([ 'customer' => $user->stripe_cus]);

        $data['clientSecret'] = $setupIntent->client_secret;
        $data['publishable_key'] = $parameter->publishable_key->value;

        return view(activeTemplate() . '.user.auth.subscription', $data);
    }

    function planStore(Request $request)
    {


        $this->validate($request, ['plan_id' => 'required|integer', 'pm' => 'required|string']);
        $plan = Plan::find($request->plan_id);
        $gnl = GeneralSetting::first();
        if ($plan) {
            $user = User::find(Auth::id());
            if ($user->plan_id != 0) {

                $notify[] = ['error', 'You have already subscribed. Please unsubscribe you and choose another plan if you want change plan.'];
                return back()->withNotify($notify);
            }
            $settings = json_decode($plan->plan_settings);
            $gwStripe = Gateway::where('alias', '=', 'Stripe')->where('status', '=', 1)->first();
            $parameter = json_decode($gwStripe->parameter_list);
            Stripe::setApiKey($parameter->secret_key->value);
            $alias = $gwStripe->alias;
            
            try {
                Customer::update(
                  $user->stripe_cus,
                  ['invoice_settings' => ['default_payment_method' => $request->pm]]
                );
            } catch (CardException $e) {
                $notify[] = ['error', 'Could not create subscription with this payment method.'];
                return back()->withNotify($notify);
            }

            $subscription = Subscription::create([
                'customer' => $user->stripe_cus,
                'items' => [['plan' => $settings->$alias]],
                'off_session' => TRUE
            ]);
            //dd($subscription->status);
            if($subscription->status == 'active' || $subscription->status == 'succeeded'){
                $user->update(['plan_id' => $plan->id, 'stripe_subs' => $subscription->id, 'temp' => 0]);
                
                if ($user) {
                    $user->transactions()->create([
                        'trx' => 'Subscription '.$plan->name,
                        'user_id' => $user->id,
                        'amount' => $plan->price,
                        'main_amo' => $plan->price,
                        'balance' => $user->balance,
                        'title' => 'Purchased ' . $plan->name,
                        'charge' => 0,
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

                        'name' => $plan->name,
                        'price' => $plan->price . ' ' . $gnl->cur_text,
                        'balance_now' => $user->balance,

                    ]);

                    /*send_sms($user, 'pan_purchased', [

                        'name' => $plan->name,
                        'price' => $plan->price . ' ' . $gnl->cur_text,
                        'balance_now' => $user->balance,
                    ]);*/

                    $notify[] = ['success', 'Subscribed ' . $plan->name . ' Successfully'];
                    return redirect()->route('user.home')->withNotify($notify);
                }
            }

                $notify[] = ['error', 'Something Went Wrong'];
                return back()->withNotify($notify);

        }
        $notify[] = ['error', 'Something Went Wrong'];
        return back()->withNotify($notify);
    }


    public function redirectConnectStripe(Request $request){
        
        $user = User::find(Auth::id());
        $gwStripe = Gateway::where('alias', '=', 'Stripe')->where('status', '=', 1)->first();
        $parameter = json_decode($gwStripe->parameter_list);
        Stripe::setApiKey($parameter->secret_key->value);
        $state = $request->query->get('state');
        $code = $request->query->get('code');
        //dd($state.' '.$user->stripe_connect_state);
        if ($state != $user->stripe_connect_state){
            $notify[] = ['danger', 'Incorrect state parameter'];
            return redirect()->route('user.home')->withNotify($notify);
        }
        
        // Send the authorization code to Stripe's API.
        try {
            $stripeResponse = OAuth::token([
            'grant_type' => 'authorization_code',
            'code' => $code,
            ]);
        } catch (\Stripe\Error\OAuth\InvalidGrant $e) {
            return $response->withStatus(400)->withJson(array('error' => 'Invalid authorization code: '));
        } catch (Exception $e) {
            return $response->withStatus(500)->withJson(array('error' => 'An unknown error occurred.'));
        }

        $user->stripe_user_id = $stripeResponse->stripe_user_id;
        $user->save();
        $notify[] = ['success', 'Conected account successly'];
        return redirect()->route('user.home')->withNotify($notify);

    }


    public function renew(Request $request)
    {
        # code...

        $payload = @file_get_contents('php://input');
        $event = null;
        $donne = json_decode($payload, true);
        try {
                $event = \Stripe\Event::constructFrom($donne);
        } catch(\UnexpectedValueException $e) {
                    // Invalid payload
                    http_response_code(400);
                    exit();
        }
    // Handle the event
        switch ($event->type) {
            
            case 'invoice.payment_succeeded':
                $success = $event->data->object;
                $user = User::where('stripe_cus', '=', $success->customer)->first();
                $plan = Plan::first(); 
                if($user instanceof User){
                    if($success->status == 'paid' || $success->status == 'active'){
                        if($user->plan_id == 0){
                            $user->plan_id = $plan->id;
                            $user->save();
                        }
                    }
                    else{
                        $user->plan_id = 0;
                        $user->save();
                    }
                }
            break;
            case 'invoice.payment_failed':
                $failure = $event->data->object; // contains a \Stripe\PaymentMethod
                $user = User::where('stripe_cus', '=', $failure->customer)->first();
                
                if($user instanceof User){
                    $user->plan_id = 0;
                    $user->save();
                }
            break;
            // ... handle other event types
        default:
            // Unexpected event type
            http_response_code(400);
            exit();
        }

    
        return response()->json(array('success' => true), 200);

    }

    function matrixIndex($lv_no)
    {


        $gnl = GeneralSetting::first();
        if ($lv_no > $gnl->matrix_height) {

            $notify[] = ['error', 'No Level Found.'];

            return redirect()->route('home')->withNotify($notify);
        }
        $data['page_title'] = "My Level " . $lv_no . " Referrer";
        $data['lv_no'] = $lv_no;
        $data['referral'] = User::where('position_id', auth()->id())->get();
        return view(activeTemplate() . '.user.matrix', $data);
    }

    public function referralIndex()
    {
        $data['page_title'] = 'My Referrer';
        $data['referrals'] = User::where('ref_id', auth()->id())->paginate(config('constants.table.default'));
        return view(activeTemplate() . '.user.referrer', $data);
    }



    function indexTransfer()
    {
        $page_title = 'Balance Transfer';
        return view(activeTemplate() . '.user.balance_transfer', compact('page_title'));
    }

    function balTransfer(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'amount' => 'required|numeric|min:0',
        ]);
        $gnl = GeneralSetting::first();
        $user = User::find(Auth::id());
        $trans_user = User::where('username', $request->username)->orwhere('email', $request->username)->first();
        if ($trans_user == '') {

            $notify[] = ['error', 'Username Not Found'];

            return back()->withNotify($notify);


        }
        if ($trans_user->username == $user->username) {

            $notify[] = ['error', 'Balance Transfer Not Possible In Your Own Account'];

            return back()->withNotify($notify);

        }
        $charge = $gnl->bal_trans_fixed_charge + ($request->amount * $gnl->bal_trans_per_charge) / 100;
        $amount = $request->amount + $charge;
        if ($user->balance >= $amount) {

            $new_balance = $user->balance - $amount;
            $user->balance = $new_balance;
            $user->save();

            $trx = getTrx();

            Trx::create([
                'trx' => $trx,
                'user_id' => $user->id,
                'type' => 8,
                'title' => 'Balance Transferred To ' . $trans_user->fullname,
                'amount' => $request->amount,
                'main_amo' => $amount,
                'balance' => $user->balance,
                'charge' => $charge
            ]);


            send_email($user, 'BAL_SEND', [

                'amount' => $request->amount . '' . $gnl->cur_text,
                'name' => $trans_user->username,
                'charge' => $charge . ' ' . $gnl->cur_text,
                'balance_now' => $new_balance . ' ' . $gnl->cur_text,

            ]);

            send_sms($user, 'BAL_SEND', [
                'amount' => $request->amount . '' . $gnl->cur_text,
                'name' => $trans_user->username,
                'charge' => $charge . ' ' . $gnl->cur_text,
                'balance_now' => $new_balance . ' ' . $gnl->cur_text,
            ]);


            $trans_new_bal = $trans_user->balance + $request->amount;
            $trans_user->balance = $trans_new_bal;
            $trans_user->save();

            Trx::create([
                'trx' => $trx,
                'user_id' => $trans_user->id,
                'type' => 8,
                'title' => 'Balance Transferred From ' . $user->fullname,
                'amount' => $request->amount,
                'main_amo' => $request->amount,
                'balance' => $trans_new_bal,
                'charge' => 0
            ]);


            send_email($trans_user, 'bal_receive', [

                'amount' => $request->amount . '' . $gnl->cur_text,
                'name' => $user->username,
                'charge' => 0 . ' ' . $gnl->cur_text,
                'balance_now' => $trans_new_bal . ' ' . $gnl->cur_text,

            ]);

            send_sms($trans_user, 'bal_receive', [
                'amount' => $request->amount . '' . $gnl->cur_text,
                'name' => $user->username,
                'charge' => 0 . ' ' . $gnl->cur_text,
                'balance_now' => $trans_new_bal . ' ' . $gnl->cur_text,
            ]);


            $notify[] = ['success', 'Balance Transferred Successfully.'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'Insufficient Balance.'];
            return back()->withNotify($notify);

        }
    }

    function searchUser(Request $request)
    {
        $trans_user = User::where('id', '!=', Auth::id())->where('username', $request->username)
            ->orwhere('email', $request->username)->count();
        if ($trans_user == 1) {
            return response()->json(['success' => true, 'message' => 'Correct User']);
        } else {
            return response()->json(['success' => false, 'message' => 'User Not Found']);
        }

    }

    function pinRecharge()
    {


        $page_title = 'Recharge Wallet With E-PIN ';
        $epin = Epin::where('created_user_id', auth()->id())->latest()->paginate(config('constants.table.default'));
        return view(activeTemplate() . '.user.pin_recharge', compact('page_title', 'epin'));
    }


    function EPinRecharge()
    {


        $page_title = 'My E-Pin Recharged';
        $epin = Epin::where('created_user_id', auth()->id())->where('status', 2)->latest()->paginate(config('constants.table.default'));
        return view(activeTemplate() . '.user.my_pin', compact('page_title', 'epin'));
    }

    function EPinGenerated()
    {


        $page_title = 'My E-Pin Generated';
        $epin = Epin::where('created_user_id', auth()->id())->latest()->paginate(config('constants.table.default'));
        return view(activeTemplate() . '.user.my_pin', compact('page_title', 'epin'));
    }


    function pinRechargePost(Request $request)
    {
        $this->validate($request, [
            'pin' => 'required'
        ]);

        $pin = Epin::where('pin', $request->pin)->first();

        if ($pin == '') {
            $notify[] = ['error', 'Wrong Pin.'];
            return back()->withNotify($notify);
        }
        if ($pin->status == 2) {
            $notify[] = ['error', 'Already Used.'];
            return back()->withNotify($notify);
        }
        if ($pin->status == 1) {
            $pin->status = 2;
            $pin->user_id = Auth::id();
            $pin->save();

            $user = User::find(Auth::id());
            $new_balance = $user->balance + $pin->amount;
            $user->balance = $new_balance;
            $user->save();

            $tlog['type'] = 9;
            $tlog['user_id'] = $user->id;
            $tlog['amount'] = $pin->amount;
            $tlog['main_amo'] = $pin->amount;
            $tlog['balance'] = $user->balance;
            $tlog['charge'] = 0;
            $tlog['title'] = 'E-Pin Recharge';
            $tlog['trx'] = getTrx();
            Trx::create($tlog);

            $notify[] = ['success', 'Balance Added Successfully.'];
            return redirect()->back()->withNotify($notify);

        }

    }

    function pinGenerate(Request $request)
    {
        $this->validate($request, [
            'amount' => 'required|numeric|min:0|max:191'
        ]);

        $user = User::find(auth()->id());
        if ($user->balance < $request->amount) {

            $notify[] = ['error', 'Insufficient balance for generate pin'];
            return redirect()->back()->withNotify($notify);

        }

        $new_balance = $user->balance - $request->amount;
        $user->balance = $new_balance;
        $user->save();

        $tlog['type'] = 10;
        $tlog['user_id'] = $user->id;
        $tlog['amount'] = $request->amount;
        $tlog['main_amo'] = $request->amount;
        $tlog['balance'] = $user->balance;
        $tlog['title'] = 'E-Pin Generate';
        $tlog['trx'] = getTrx();
        $tlog['charge'] = 0;
        Trx::create($tlog);

        $pin = rand(10000000, 99999999) . '-' . rand(10000000, 99999999) . '-' . rand(10000000, 99999999) . '-' . rand(10000000, 99999999);
        Epin::create([
            'created_user_id' => $user->id,
            'user_id' => 0,
            'pin' => $pin,
            'amount' => $request->amount,
            'status' => 1,
        ]);

        $notify[] = ['success', 'Pin generate Successfully'];
        return redirect()->back()->withNotify($notify);

    }
}
