<?php

namespace App\Http\Controllers;

use App\Lib\GoogleAuthenticator;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Http\Traits\Authorization;

class AuthorizationController extends Controller
{
    use Authorization;

    public function authorizeForm()
    {
        $view = activeTemplate() . 'user.auth.authorize';
        if (auth()->check()) {
            $user = auth()->user();
            if (!$user->status) {
                $page_title = 'Your Account has been blocked';
                return view($view, compact('user', 'page_title'));
            } elseif (!$user->ev) {
                if (!$this->checkValidCode($user, $user->ver_code)) {
                    $user->ver_code = verification_code(6);
                    $user->ver_code_send_at = Carbon::now();
                    $user->save();
                    send_email($user, 'EVER_CODE', [
                        'code' => $user->ver_code
                    ]);
                }
                $page_title = 'Email verification form';
                return view($view, compact('user', 'page_title'));
            } elseif (!$user->sv) {
                if (!$this->checkValidCode($user, $user->ver_code)) {
                    $user->ver_code = verification_code(6);
                    $user->ver_code_send_at = Carbon::now();
                    $user->save();
                    send_sms($user, 'SVER_CODE', [
                        'code' => $user->ver_code
                    ]);
                }
                $page_title = 'SMS verification form';
                return view($view, compact('user', 'page_title'));
            } elseif (!$user->tv) {
                $page_title = 'Google Authenticator';
                return view($view, compact('user', 'page_title'));
            }
        }
        return redirect()->route('user.login');
    }

    public function sendVerifyCode(Request $request)
    {
        $user = Auth::user();
        $data = $this->sendVfCode($request, $user);

        if ($data['code'] === 200) {
            return back()->withNotify($data['notify']);
            
        }else{
            throw ValidationException::withMessages($data['notify']);
        }
    }

    public function emailVerification(Request $request)
    {

        $request->validate([
            'email_verified_code' => 'required',
        ], [
            'email_verified_code.required' => 'Email verification code is required',
        ]);

        $user = Auth::user();
        if ($this->emailVf($request, $user)) {
            return redirect()->intended(route('user.home'));
        }
        throw ValidationException::withMessages(['email_verified_code' => 'Verification code didn\'t match!']);
    }

    public function smsVerification(Request $request)
    {
        $request->validate([
            'sms_verified_code' => 'required',
        ], [
            'sms_verified_code.required' => 'SMS verification code is required',
        ]);
        $user = Auth::user();
        if ($this->smsVf($request, $user)) {
            return redirect()->intended(route('user.home'));
        }
        throw ValidationException::withMessages(['sms_verified_code' => 'Verification code didn\'t match!']);
    }

    public function g2faVerification(Request $request)
    {
        $user = auth()->user();

        $this->validate(
            $request,
            [
                'code' => 'required',
            ]
        );
        
        if ($this->g2faVf($request, $user)) {
            return redirect()->route('user.home');
        } else {
            throw ValidationException::withMessages(['code' => 'Wrong Verification Code']);
        }
    }
}
