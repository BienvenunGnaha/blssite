<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Gateway;
use App\Plan;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\SetupIntent;
use Stripe\Subscription;
use Stripe\OAuth;
use Stripe\PaymentIntent;
use Slim\App;
use Slim\Http\Request as SlimRequest;
use Slim\Http\Response;

class StripeController extends Controller
{
    public function setupIntent(Request $request){
        $validator = Validator::make($request->all(), [
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'required|string|email',
        ]);

        $request = $request->request->all();
        $gateway = Gateway::where('alias', '=', 'Stripe')->where('status', '=', 1)->first();
        $parameter = json_decode($gateway->parameter_list);//sk_test_51H5XB7LXos1KNuRfFCkqi6R1KTIVyOeX11iBigvbst596jugCgjo2Rq9BGBAXGIGIMQDG8EF1pI4252YXLPcBndo00yOGZIkfJ
        //Stripe::setApiKey('sk_test_51H5XB7LXos1KNuRfFCkqi6R1KTIVyOeX11iBigvbst596jugCgjo2Rq9BGBAXGIGIMQDG8EF1pI4252YXLPcBndo00yOGZIkfJ');
        Stripe::setApiKey($parameter->secret_key->value);

        $cus = Customer::create([
            'name' => $request['firstname'].' '.$request['lastname'],
            'email' => $request['email']
        ]);

        $setupIntent = SetupIntent::create([ 'customer' => $cus->id]);
        $data['clientSecret'] = $setupIntent->client_secret;
        $data['publishable_key'] = $parameter->publishable_key->value;
        //$data['publishable_key'] = 'pk_test_51H5XB7LXos1KNuRfl8tb3BngnpJkN8uqBue1XAuLBCmLqPzwKYDWuHlkURkFW10LMDVMZ8cDQxZJt4pJE3eBWFu100wANAtqcY';
        $data['stripe_cus'] = $cus->id;
        $data['plan'] = Plan::first();

        return response()->json($data, 200);
    }

    public function userSetupIntent(Request $request){
        $validator = Validator::make($request->all(), [
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'required|string|email',
        ]);

        $user = auth('api')->user();

        $request = $request->request->all();
        $gateway = Gateway::where('alias', '=', 'Stripe')->where('status', '=', 1)->first();
        $parameter = json_decode($gateway->parameter_list);//sk_test_51H5XB7LXos1KNuRfFCkqi6R1KTIVyOeX11iBigvbst596jugCgjo2Rq9BGBAXGIGIMQDG8EF1pI4252YXLPcBndo00yOGZIkfJ
        // Stripe::setApiKey('sk_test_51H5XB7LXos1KNuRfFCkqi6R1KTIVyOeX11iBigvbst596jugCgjo2Rq9BGBAXGIGIMQDG8EF1pI4252YXLPcBndo00yOGZIkfJ');
        Stripe::setApiKey($parameter->secret_key->value);
        
        $cus = $user->stripe_cus;

        if($cus === null){
            $customer = Customer::create([
                'name' => $request['firstname'].' '.$request['lastname'],
                'email' => $request['email']
            ]);

            $cus = $customer->id;
        }
        

        $setupIntent = SetupIntent::create(['customer' => $cus]);
        $data['clientSecret'] = $setupIntent->client_secret;
        $data['publishable_key'] = $parameter->publishable_key->value;
        //$data['publishable_key'] = 'pk_test_51H5XB7LXos1KNuRfl8tb3BngnpJkN8uqBue1XAuLBCmLqPzwKYDWuHlkURkFW10LMDVMZ8cDQxZJt4pJE3eBWFu100wANAtqcY';
        $data['stripe_cus'] = $cus;

        return response()->json($data, 200);
    }
}
