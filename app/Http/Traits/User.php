<?php
namespace App\Http\Traits;


use App\GeneralSetting;
use App\Plan;
use App\User as Usr;
use App\MatrixLevel;
use App\Lib\GoogleAuthenticator;
use App\Rules\FileTypeValidate;
use App\SupportTicket;
use App\Trx;
use App\UserLogin;
use App\Withdrawal;
use App\WithdrawMethod;
use App\Deposit;
use Illuminate\Support\Facades\Hash;

use \Illuminate\Http\Request;

trait User{

    function dashboard(Usr $user)
    {
        $data['page_title'] = "Dashboard";
        $data['balance'] = pricePayToCustomer($user->id);
        $data['total_deposit'] = Deposit::whereUserId($user->id)->whereStatus(1)->sum('amount');
        $data['total_withdraw'] = Withdrawal::whereUserId($user->id)->whereStatus(1)->sum('amount');


        $data['ref_com'] = Trx::whereUserId($user->id)->whereType(11)->sum('amount');
        $data['level_com'] = Trx::whereUserId($user->id)->whereType(4)->sum('amount');
        $data['total_epin_recharge'] = Trx::whereUserId($user->id)->whereType(9)->sum('amount');
        $data['total_epin_generate'] = Trx::whereUserId($user->id)->whereType(10)->sum('amount');
        $data['total_bal_transfer'] = Trx::whereUserId($user->id)->whereType(8)->sum('amount');

        $data['total_direct_ref'] = Usr::where('ref_id', $user->id)->count();

        $data['total_paid_width'] = Usr::where('position_id', $user->id)->count();
        
        //$this->pricePayToCustomer($user->id);

        if ($user->ref_id != 0) {
            $data['ref_user'] = Usr::find($user->ref_id);
        }

        $gs = GeneralSetting::first();

        $data['plan'] = null;
        $data['matrix_level'] = null;
        $data['matrix_width'] = $gs ? $gs->matrix_width : 0;
        if($user->plan_id != 0){
            $plan = Plan::find($user->plan_id);
            $data['plan'] = $plan;
            if($plan instanceof Plan){
                $data['matrix_level'] = MatrixLevel::where(['plan_id' => $plan->id])->orderBy('level', 'ASC')->get(); 
            }
        }

        return $data;
    }

    function pwUpdate(Request $request, Usr $user){

        
        if ($request->current == $request->password) {
            $notify[] = ['error', 'Current password and new password should not same'];
            return ['code' => 500, 'notify' => $notify];

        }
        if (!Hash::check($request->current, $user->password)) {
            $notify[] = ['error', 'Current password does not match'];
            return ['code' => 500, 'notify' => $notify];

        }else{
            $user->password = Hash::make($request->password);
            $user->save();
            $notify[] = ['success', 'Password update successful'];
            return ['code' => 200, 'notify' => $notify];
        }
        
    }

    function prfUpdate(Request $request, Usr $user){
        $filename = $user->image;
        if ($request->hasFile('image')) {
            try {
                $path = config('constants.user.profile.path');
                $size = config('constants.user.profile.size');
                $filename = upload_image($request->image, $path, $size, $filename);
            } catch (\Exception $exp) {
                $notify[] = ['success', 'Image could not be uploaded'];
                return ['code' => 500, 'notify' => $notify];
            }
        }

        $user->update([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'image' => $filename,
            'address' => [
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'zip' => $request->zip,
                'country' => $request->country,
            ]
        ]);
        $notify[] = ['success', 'Your profile has been updated'];
        return ['code' => 200, 'notify' => $notify];;
    }

    function create2faGoogle(Request $request, Usr $user){
        $ga = new GoogleAuthenticator();
        $secret = $request->key;
        $oneCode = $ga->getCode($secret);
        if ($oneCode === $request->code) {

            $user->tsc = $request->key;
            $user->ts = 1;
            $user->tv = 1;
            $user->save();

            if ($user->ev) {
                send_email($user, '2FA_ENABLE', [
                    'code' => $user->ver_code
                ]);
            } else {
                send_sms($user, '2FA_ENABLE', [
                    'code' => $user->ver_code
                ]);
            }

            $notify[] = ['success', 'Google Authenticator Enabled Successfully'];
            return ['code' => 200, 'notify' => $notify];
        } else {
            $notify[] = ['danger', 'Wrong Verification Code'];
            return ['code' => 500, 'notify' => $notify];
        }
    }

    function disable2faGoogle(Request $request, Usr $user){
        $ga = new GoogleAuthenticator();

        $secret = $user->tsc;
        $oneCode = $ga->getCode($secret);
        $userCode = $request->code;

        if ($oneCode == $userCode) {

            $user->tsc = null;
            $user->ts = 0;
            $user->tv = 1;
            $user->save();

            if ($user->ev) {
                send_email($user, '2FA_DISABLE');
            } else {
                send_sms($user, '2FA_DISABLE');
            }

            $notify[] = ['success', 'Two Factor Authenticator Disable Successfully'];
            return ['code' => 200, 'notify' => $notify];
        } else {
            $notify[] = ['error', 'Wrong Verification Code'];
            return ['code' => 500, 'notify' => $notify];
        }
        
    }

    
}