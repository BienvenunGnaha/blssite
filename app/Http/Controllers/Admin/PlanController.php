<?php

namespace App\Http\Controllers\Admin;

use App\Frontend;
use App\MatrixLevel;
use App\Http\Controllers\Controller;
use App\Plan;
use App\Gateway;
use Stripe\Stripe;
use Stripe\Plan as StripePlan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    function index()
    {
        $page_title = "Manage Plan";
        $plan = Plan::with('plan_level')->get();
        return view('admin.plan.index', compact('page_title', 'plan'));
    }

    function create()
    {
        $page_title = "Create Plan";
        return view('admin.plan.create', compact('page_title'));
    }

    function edit(Plan $plan)
    {
        $page_title = __($plan->name);
        return view('admin.plan.edit', compact('page_title', 'plan'));
    }

    function store(Request $request)
    {
        $this->validate($request, [
            'ref_bonus' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:1',
            'name' => 'required|string|max:191',
            'amount.*' => 'required|numeric|min:0',
        ]);

        $gateways = Gateway::where('status', '=', 1)->get();
        $gwStripe = Gateway::where('alias', '=', 'Stripe')->where('status', '=', 1)->first();
        $parameter = json_decode($gwStripe->parameter_list);
        Stripe::setApiKey($parameter->secret_key->value);
        $settings = [];
        $count = count($gateways);
        for($i = 0; $i < $count; $i++){
            $settings[$gateways[$i]->alias] = '';
        }

        $plStripe = StripePlan::create([
            'amount' => $request->price*100,
            'currency' => 'eur',
            'interval' => 'month',
            'product' => ['name' => $request->name],
        ]);

        $settings[$gwStripe->alias] = $plStripe->id;

        $plan = Plan::create([
            'name' => $request->name,
            'price' => $request->price,
            'ref_bonus' => $request->ref_bonus,
            'plan_settings' => json_encode($settings),
            'status' => 1,
        ]);
        $this->insertIntoMatrix($request->amount, $plan->id);

        $notify[] = ['success', 'Create Successfully'];
        return back()->withNotify($notify);

    }

    function update(Request $request, Plan $plan)
    {
        $this->validate($request, [
            'ref_bonus' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:1',
            'name' => 'required|string|max:191',
            'amount.*' => 'required|numeric|min:0',
        ]);
        $plan->plan_level->each->delete();
        $settings = [];
        $gwStripe = Gateway::where('alias', '=', 'Stripe')->where('status', '=', 1)->first();
        $parameter = json_decode($gwStripe->parameter_list);
        Stripe::setApiKey($parameter->secret_key->value);
        if(isset($plan->plan_settings)){
            $settings = json_decode($plan->plan_settings);
            
            $alias = $gwStripe->alias;
            $plan_id = $settings->$alias;
            
            //dd($subs->plan_stripe); 
            $plan_d = StripePlan::retrieve(
              $plan_id
            );
            $plan_d->delete();
            $pl = StripePlan::create([
              'id' => $plan_id,
              'amount' => $plan->price*100,
              'currency' => 'eur',
              'interval' => 'month',
              'product' => ['name' => $request->name],
          ]);

        }else{
            $gateways = Gateway::where('status', '=', 1)->get();
            $gwStripe = Gateway::where('alias', '=', 'Stripe')->where('status', '=', 1)->first();
            $settings = [];
            $count = count($gateways);
            for($i = 0; $i < $count; $i++){
                $settings[$gateways[$i]->alias] = '';
            }

            $plStripe = StripePlan::create([
                'amount' => $request->price*100,
                'currency' => 'eur',
                'interval' => 'month',
                'product' => ['name' => $request->name],
            ]);

            $settings[$gwStripe->alias] = $plStripe->id;
        }
        $plan->update([
            'name' => $request->name,
            'price' => $request->price,
            'ref_bonus' => $request->ref_bonus,
            'plan_settings' => json_encode($settings),
            'status' => $request->status,
        ]);
        $this->insertIntoMatrix($request->amount, $plan->id);

        $notify[] = ['success', 'Update Successfully'];

        return back()->withNotify($notify);


    }

    function insertIntoMatrix($amount, $id)
    {
        foreach ($amount as $key => $data) {
            MatrixLevel::create([
                'plan_id' => $id,
                'amount' => $data,
                'level' => $key + 1,
            ]);
        }
    }

}
