<?php

namespace App\Http\Controllers\Admin;

use App\CouponStripe;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Gateway;
use App\User;

use Stripe\Stripe;
use Stripe\StripeClient;
use Stripe\Customer;
use Stripe\SetupIntent;
use Stripe\Subscription;
use Stripe\OAuth;
use Stripe\PaymentIntent;
use Slim\App;
use Slim\Http\Request as SlimRequest;
use Slim\Http\Response;

class CouponStripeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = 'Coupons';
        $coupons = CouponStripe::all();
        return view('admin.coupon.index', compact('page_title', 'coupons'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $page_title = 'Create Coupon';
        $users = User::where('is_test', 0)->get();
        return view('admin.coupon.create', compact('page_title', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'duration' => 'required',
        ]);
        $data = $request->all();
        $data['valid'] = true;
        foreach ($data as $key => $value) {
            # code...
            if($value == ''){
                $data[$key] = null;
            }
        }
        //dd($data); 
        if($data['redeem_by'] !== null){
            $d = new \DateTime($data['redeem_by']);
            $data['redeem_by'] = $d->getTimestamp();
            //dd($data); 
        }
        $coupon = CouponStripe::create($data);
        $gwStripe = Gateway::where('alias', '=', 'Stripe')->where('status', '=', 1)->first();
        $parameter = json_decode($gwStripe->parameter_list);
        //Stripe::setApiKey($parameter->secret_key->value);
        try {
            $stripe = new StripeClient($parameter->secret_key->value);
            $cp_data = [
                'duration' => $coupon->duration,
                "name" => $coupon->name,
                "currency" => 'eur'
            ];
            if($coupon->redeem_by !== null && time() < (int)$coupon->redeem_by){
                $cp_data["redeem_by"] = $coupon->redeem_by;
            }
            if($coupon->amount_off !== null){
                $cp_data["amount_off"] = floatval($coupon->amount_off);
            }
            if($coupon->duration_in_months !== null){
                $cp_data["duration_in_months"] = (int)$coupon->duration_in_months;
            }
            if($coupon->max_redemptions !== null){
                $cp_data["max_redemptions"] = (int)$coupon->max_redemptions;
            }
            if($coupon->percent_off !== null){
                $cp_data["percent_off"] = floatval($coupon->percent_off);
            }
            //dd($cp_data);
            $stripe_coupon = $stripe->coupons->create($cp_data);

            

        } catch (\Exception $e) {
            dd($e);
            $notify = ['danger', 'Coupon creating Failed'];
            return back()->withNotify($notify);
        }

        $coupon->id_coupon = $stripe_coupon->id;
        $coupon->save();
        $notify = ['success', 'Create Successfully'];
        return back()->withNotify($notify);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CouponStripe  $couponStripe
     * @return \Illuminate\Http\Response
     */
    public function show(CouponStripe $couponStripe)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CouponStripe  $couponStripe
     * @return \Illuminate\Http\Response
     */
    public function edit(CouponStripe $couponStripe)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CouponStripe  $couponStripe
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CouponStripe $couponStripe)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CouponStripe  $couponStripe
     * @return \Illuminate\Http\Response
     */
    public function destroy(CouponStripe $couponStripe, $id)
    {
        $coupon = CouponStripe::find($id);
        if ($coupon) {
            $id_coupon = $coupon->id_coupon;
            $coupon->delete();
            $gwStripe = Gateway::where('alias', '=', 'Stripe')->where('status', '=', 1)->first();
            $parameter = json_decode($gwStripe->parameter_list);
            $stripe = new StripeClient($parameter->secret_key->value);
            if($id_coupon !== null){
                $delete = $stripe->coupons->delete(
                    $id_coupon,
                    []
                  );
            }
           
            $notify[] = ['success', 'Delete Successfully'];
                return back()->withNotify($notify);
        }
        
        $notify[] = ['danger', 'Coupon deleting Failed'];
        return back()->withNotify($notify);
    }
}
