<?php

namespace App\Http\Controllers;

use App\Deposit;
use App\GeneralSetting;
use App\Lib\GoogleAuthenticator;
use App\Rules\FileTypeValidate;
use App\SupportTicket;
use App\Trx;
use App\UserLogin;
use App\Withdrawal;
use App\WithdrawMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;
use Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\Matrix;
use App\Http\Traits\User as Usr;

class UserController extends Controller
{
    use Matrix;
    use Usr;

    public function home()
    {
        $page_title = 'Dashboard';
        $user = Auth::user();
        $data = $this->dashboard($user);

        return view(activeTemplate() . 'user.dashboard', compact('page_title'), $data);
    }


    function profileIndex()
    {
        $data['page_title'] = "Account Settings";
        return view(activeTemplate() . '.user.profile', $data);
    }

    function passwordUpdate(Request $request)
    {
        $this->validate($request, [
            'current' => 'required|max:191',
            'password' => 'required|confirmed|max:191',
            'password_confirmation' => 'required|max:191'
        ]);
        $user = User::find(Auth::id());

        return back()->withNotify($this->pwUpdate($request, $user)['notify']);
    }


    public function profile()
    {
        $page_title = 'Profile';
        return view(activeTemplate() . 'user.profile', compact('page_title'));
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

        $user = auth()->user();
        return back()->withNotify($this->prfUpdate($request, $user)['notify']);
    }

    public function passwordChange()
    {
        $page_title = 'Password Change';
        return view(activeTemplate() . 'user.password', compact('page_title'));
    }


    public function show2faForm()
    {
        $gnl = GeneralSetting::first();
        $ga = new GoogleAuthenticator();
        $user = auth()->user();
        $secret = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($user->username . '@' . $gnl->sitename, $secret);
        $prevcode = $user->tsc;
        $prevqr = $ga->getQRCodeGoogleUrl($user->username . '@' . $gnl->sitename, $prevcode);
        $page_title = 'Google 2FA Auth';

        return view(activeTemplate() . 'user.go2fa', compact('page_title', 'secret', 'qrCodeUrl', 'prevcode', 'prevqr'));
    }

    public function create2fa(Request $request)
    {
        $user = auth()->user();
        $this->validate($request, [
            'key' => 'required',
            'code' => 'required',
        ]);

        
        return back()->withNotify($this->create2faGoogle($request, $user)['notify']);
    }


    public function disable2fa(Request $request)
    {
        $this->validate($request, [
            'code' => 'required',
        ]);

        $user = auth()->user();
        return back()->withNotify($this->disable2faGoogle($request, $user)['notify']);
    }

    public function depositHistory()
    {

        $page_title = 'Deposit History';
        $empty_message = 'No history found.';
        $logs = auth()->user()->deposits()->where('status', '!=', 0)->latest()->paginate(config('constants.table.default'));
        return view(activeTemplate() . 'user.deposit_history', compact('page_title', 'empty_message', 'logs'));
    }

    public function withdrawHistory()
    {
        $page_title = 'Withdraw History';
        $empty_message = 'No history found.';
        $logs = Withdrawal::where('status', '!=', 0)->latest()->paginate(config('constants.table.default'));
        return view(activeTemplate() . 'user.withdraw_history', compact('page_title', 'empty_message', 'logs'));
    }

    public function withdraw()
    {


        $page_title = 'Withdraw';
        $methods = WithdrawMethod::where('status', 1)->get();
        $empty_message = 'No history found.';
        $logs = auth()->user()->withdrawals()->with(['method'])->latest()->paginate(config('constants.table.default'));
        return view(activeTemplate() . 'user.withdraw', compact('page_title', 'methods', 'empty_message', 'logs'));
    }




    function transactions()
    {
        $data['page_title'] = "Transaction Log";
        $data['table'] = Trx::where('user_id', auth()->id())->orderBy('id', 'DESC')->paginate(config('constants.table.default'));
        return view(activeTemplate() . '.user.trans_history', $data);

    }

    public function balance_tf_log()
    {
        $data['page_title'] = 'Transferred Balance';
        $data['table'] = Trx::where('user_id', auth()->id())->where('type', 8)->latest()->paginate(config('constants.table.default'));
        return view(activeTemplate() . '.user.trans_history', $data);
    }

    public function ref_com_Log()
    {
        $data['page_title'] = 'Referral Commission';
        $data['table'] = Trx::where('user_id', auth()->id())->where('type', 4)->latest()->paginate(config('constants.table.default'));
        return view(activeTemplate() . '.user.trans_history', $data);
    }

    public function level_com_log()
    {
        $data['page_title'] = 'Level Commission';
        $data['table'] = Trx::where('user_id', auth()->id())->where('type', 11)->latest()->paginate(config('constants.table.default'));
        return view(activeTemplate() . '.user.trans_history', $data);
    }


    public function withdrawInsert(Request $request)
    {


        $request->validate([
            'id' => 'required|integer',
            'amount' => 'required|numeric|gt:0',
        ]);
        $withdraw = WithdrawMethod::findOrFail($request->id);

        $multiInput = [];
        if ($withdraw->user_data != null) {
            foreach ($withdraw->user_data as $k => $val) {
                $multiInput[str_replace(' ', '_', $val)] = null;
            }
        }

        if ($request->amount < $withdraw->min_limit || $request->amount > $withdraw->max_limit) {
            $notify[] = ['error', 'Please follow the limit'];
            return back()->withNotify($notify);
        }

        if ($request->amount > auth()->user()->balance) {
            $notify[] = ['error', 'You do not have sufficient balance'];
            return back()->withNotify($notify);
        }

        $charge = $withdraw->fixed_charge + (($request->amount * $withdraw->percent_charge) / 100);
        $withoutCharge = $request->amount - $charge;
        $final_amo = formatter_money($withoutCharge * $withdraw->rate);

        $data = new Withdrawal();
        $data->method_id = $request->id;
        $data->user_id = auth()->id();
        $data->amount = formatter_money($request->amount);
        $data->charge = formatter_money($charge);
        $data->rate = $withdraw->rate;
        $data->currency = $withdraw->currency;
        $data->delay = $withdraw->delay;
        $data->final_amo = $final_amo;
        $data->status = 0;
        $data->trx = getTrx();
        $data->save();
        Session::put('Track', $data->trx);
        return redirect()->route('user.withdraw.preview');

    }


    public function withdrawPreview()
    {
        $track = Session::get('Track');
        $data = Withdrawal::where('user_id', auth()->id())->where('trx', $track)->where('status', 0)->first();
        if (!$data) {
            return redirect()->route('user.withdraw');
        }
        $page_title = "Withdraw Preview";

        return view(activeTemplate() . 'user.withdraw_preview', compact('data', 'page_title'));
    }




    public function withdrawStore(Request $request)
    {


        $track = Session::get('Track');

       $withdraw = Withdrawal::where('user_id', auth()->id())->where('trx', $track)->orderBy('id', 'DESC')->first();



        $withdraw_method = WithdrawMethod::where('status', 1)->findOrFail($withdraw->method_id);


        if (!empty($withdraw_method->user_data)) {
            foreach ($withdraw_method->user_data as $data) {
                $validation_rule['ud.' . \Str::slug($data)] = 'required';
            }
            $request->validate($validation_rule, ['ud.*' => 'Please provide all information.']);
        }



        $balance = auth()->user()->balance - $withdraw->amount;
        auth()->user()->update([
            'balance' => formatter_money($balance),
        ]);

        $withdraw->detail = $request->ud;
        $withdraw->status = 2;
        $withdraw->save();


        $trx = new Trx();
        $trx->user_id = auth()->id();
        $trx->amount = $withdraw->amount;
        $trx->charge = formatter_money($withdraw->charge);
        $trx->main_amo = formatter_money($withdraw->final_amo);
        $trx->balance = formatter_money(auth()->user()->balance);
        $trx->type = 'withdraw';
        $trx->trx = $withdraw->trx;
        $trx->title = 'withdraw Via ' . $withdraw->method->name;
        $trx->save();


        $general = GeneralSetting::first();

        send_email(auth()->user(), 'WITHDRAW_PENDING', [
            'trx' => $withdraw->trx,
            'amount' => $general->cur_sym . ' ' . formatter_money($withdraw->amount),
            'method' => $withdraw->method->name,
            'charge' => $general->cur_sym . ' ' . $withdraw->charge,
        ]);

        send_sms(auth()->user(), 'WITHDRAW_PENDING', [
            'trx' => $withdraw->trx,
            'amount' => $general->cur_sym . ' ' . formatter_money($withdraw->amount),
            'method' => $withdraw->method->name,
            'charge' => $general->cur_sym . ' ' . $withdraw->charge,
        ]);


        $notify[] = ['success', 'You withdraw request has been taken.'];


        return redirect()->route('user.home')->withNotify($notify);
    }


    function loginHistory()
    {
        $data['page_title'] = "Login History";
        $data['history'] = UserLogin::where('user_id', Auth::id())->latest()->paginate(config('constants.table.default'));
        return view(activeTemplate() . '.user.login_history', $data);
    }
}
