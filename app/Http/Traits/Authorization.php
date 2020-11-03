<?php

namespace App\Http\Traits;

use App\Lib\GoogleAuthenticator;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\User;

trait Authorization{

    public function checkValidCode($user, $code, $add_min = 10000)
    {
        if (!$code) return false;
        if (!$user->ver_code_send_at) return false;
        if ($user->ver_code_send_at->addMinutes($add_min) < Carbon::now()) return false;
        if ($user->ver_code !== $code) return false;
        return true;
    }

    public function sendVfCode(Request $request, User $user)
    {
        
        if ($this->checkValidCode($user, $user->ver_code, 2)) {
            $target_time = $user->ver_code_send_at->addMinutes(2)->timestamp;
            $delay = $target_time - time();
            return ['code' => 500, 'notify' => ['resend' => 'Please Try after ' . $delay . ' Seconds']];
        }
        if (!$this->checkValidCode($user, $user->ver_code)) {
            $user->ver_code = verification_code(6);
            $user->ver_code_send_at = Carbon::now();
            $user->save();
        } else {
            $user->ver_code = $user->ver_code;
            $user->ver_code_send_at = Carbon::now();
            $user->save();
        }

        if ($request->type === 'email') {
            send_email($user, 'EVER_CODE', [
                'code' => $user->ver_code
            ]);
            $notify[] = ['success', 'Email verification code sent successfully'];
            return ['code' => 200, 'notify' => $notify];
        } elseif ($request->type === 'phone') {
            send_sms($user, 'SVER_CODE', [
                'code' => $user->ver_code
            ]);
            $notify[] = ['success', 'SMS verification code sent successfully'];
            return ['code' => 200, 'notify' => $notify];
        } else {
            $notify = ['resend' => 'Sending Failed'];
            return ['code' => 500, 'notify' => $notify];
        }
    }

    public function emailVf(Request $request, User $user)
    {
        if ($this->checkValidCode($user, $request->email_verified_code)) {
            $user->ev = 1;
            $user->ver_code = null;
            $user->ver_code_send_at = null;
            $user->save();
            return true;
        }
        return false;
    }

    public function smsVf(Request $request, User $user)
    {
        if ($this->checkValidCode($user, $request->sms_verified_code)) {
            $user->sv = 1;
            $user->ver_code = null;
            $user->ver_code_send_at = null;
            $user->save();
            return true;
        }
        
        return false;
    }

    public function g2faVf(Request $request, User $user)
    {
        $ga = new GoogleAuthenticator();

        $secret = $user->tsc;
        $oneCode = $ga->getCode($secret);
        $userCode = $request->code;
        if ($oneCode == $userCode) {
            $user->tv = 1;
            $user->save();
            return true;
        } else {
            return false;
        }
    }
}