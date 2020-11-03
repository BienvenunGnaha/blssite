<?php

namespace App\Http\Controllers\Api;

use App\GeneralSetting;
use App\Lib\GoogleAuthenticator;
use App\Rules\FileTypeValidate;
use App\Trx;
use App\UserLogin;
use App\Withdrawal;
use Illuminate\Http\Request;
use App\Http\Traits\User as Usr;
use App\Http\Traits\Authorization;
use App\User;
use App\Http\Controllers\Controller;
use App\Plan;
use App\Twofa;
//use SimpleSoftwareIO\QrCode\Facades\QrCode;

class UserController extends Controller
{
    use Usr;
    use Authorization;

    public function home(Request $request)
    {
        $user = auth('api')->user();
        $data = $this->dashboard($user);

        return response()->json($data, 200);
    }

    public function userProfile(){
        $user = auth('api')->user();
        $data['user'] = $user;
        $data['amount'] = pricePayToCustomer($user->id);
        return response()->json($data, 200);
    }

    function profileIndex()
    {
        $data['user'] = auth('api')->user();
        return response()->json($data, 200);
    }

    public function referralIndex()
    {
        $user = auth('api')->user();
        $data['referals'] = User::where('ref_id', $user->id)->paginate(config('constants.table.default'));
        $data['plans'] = Plan::all();
        $data['link'] = 'https://businesslifesport.com/user/register/'.$user->username;
        $data['qrcode'] = "data:image/png;base64, ".base64_encode(\QrCode::format('png')->size(150)->generate('https://businesslifesport.com/user/register/'.auth()->user()->username));
        return response()->json($data, 200);
    }

    function passwordUpdate(Request $request)
    {
        $this->validate($request, [
            'current' => 'required|max:191',
            'password' => 'required|confirmed|max:191',
            'password_confirmation' => 'required|max:191'
        ]);
        $user = auth('api')->user();
        $data = $this->pwUpdate($request, $user);
        return response()->json($data['notify'], $data['code']);
    }

    public function profileUpdate(Request $request)
    {
        $request->validate([
            'firstname' => 'required|max:160',
            'lastname' => 'required|max:160',
            'address' => 'nullable|max:160',
            'city' => 'nullable|max:160',
            'state' => 'nullable|max:160',
            'zip' => 'nullable|max:160',
            'country' => 'nullable|max:160',
            'image' => ['nullable', 'image', new FileTypeValidate(['jpeg', 'jpg', 'png'])],
        ]);

        $user = auth('api')->user();
        $data = $this->prfUpdate($request, $user);
        return response()->json($data['notify'], $data['code']);
    }

    public function show2Fa(){
        $gnl = GeneralSetting::first();
        $ga = new GoogleAuthenticator();
        $user = auth('api')->user();
        $secret = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($user->username . '@' . $gnl->sitename, $secret);
        $prevcode = $user->tsc;
        $prevqr = $ga->getQRCodeGoogleUrl($user->username . '@' . $gnl->sitename, $prevcode);
        $page_title = 'Google 2FA Auth';

        return response()->json(compact('page_title', 'secret', 'qrCodeUrl', 'prevcode', 'prevqr', 'user'), 200);
    }

    public function create2fa(Request $request)
    {
        $user = auth('api')->user();
       // $twofa = null;
        /*if($user->ts === 1 && $user->tsc !== null){
            
        }*/
        $this->validate($request, [
            'key' => 'required',
            'code' => 'required',
        ]);


        $data = $this->create2faGoogle($request, $user);
        return response()->json($data['notify'], $data['code']);
    }

    public function disable2fa(Request $request)
    {
        $this->validate($request, [
            'code' => 'required',
        ]);

        $user = auth('api')->user();
        $data = $this->disable2faGoogle($request, $user);
        return response()->json($data['notify'], $data['code']);
    }

    public function verify2fa(Request $request)
    {
        $this->validate($request, [
            'code' => 'required',
        ]);

        $user = auth('api')->user();
        $valid = $this->g2faVf($request, $user);
        return response()->json(
            $valid ? ['ttfa' => Twofa::create(['token' => base64_encode(uniqid((string)time()))])] : [],
             $valid ? 200 : 500);
    }

    function loginHistory()
    {
        $user = auth('api')->user();
        $data['page_title'] = "Login History";
        $data['history'] = UserLogin::where('user_id', $user->id)->get();
        return response()->json($data, 200);
    }

    function transactions()
    {
        $user = auth('api')->user();
        $data['page_title'] = "Transaction Log";
        $data['table'] = Trx::where('user_id', $user->id)->orderBy('id', 'DESC')->get();
        return response()->json($data, 200);
    }

    function levelCommisionTrx()
    {
        $user = auth('api')->user();
        $data['page_title'] = "Transaction Log";
        $data['table'] = Trx::where('user_id', $user->id)->where('type', 11)->orderBy('id', 'DESC')->get();
        return response()->json($data, 200);
    }

    public function depositHistory()
    {

        $page_title = 'Deposit History';
        $empty_message = 'No history found.';
        $logs = auth()->user()->deposits()->where('status', '!=', 0)->latest()->paginate(config('constants.table.default'));
        return response()->json(compact('page_title', 'empty_message', 'logs'), 200);
    }

    public function withdrawHistory()
    {
        $user = auth('api')->user();
        $page_title = 'Withdraw History';
        $empty_message = 'No history found.';
        $logs = Withdrawal::where('status', '!=', 0)->where('user_id', $user->id)->latest()->paginate(config('constants.table.default'));
        return response()->json(compact('logs'), 200);
    }
}
