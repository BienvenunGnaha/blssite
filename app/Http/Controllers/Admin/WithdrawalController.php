<?php

namespace App\Http\Controllers\Admin;

use App\GeneralSetting;
use App\Trx;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Withdrawal;

class WithdrawalController extends Controller
{
    public function pending()
    {
        $page_title = 'Pending Withdrawals';
        $withdrawals = Withdrawal::where('status', 2)->latest()->paginate(config('constants.table.default'));
        $empty_message = 'No withdrawal is pending';
        return view('admin.withdraw.withdrawals', compact('page_title', 'withdrawals', 'empty_message'));
    }

    public function approved()
    {
        $page_title = 'Approved Withdrawals';
        $withdrawals = Withdrawal::where('status', 1)->latest()->paginate(config('constants.table.default'));
        $empty_message = 'No withdrawal is approved';
        return view('admin.withdraw.withdrawals', compact('page_title', 'withdrawals', 'empty_message'));
    }

    public function rejected()
    {
        $page_title = 'Rejected Withdrawals';
        $withdrawals = Withdrawal::where('status', 3)->latest()->paginate(config('constants.table.default'));
        $empty_message = 'No withdrawal is rejected';
        return view('admin.withdraw.withdrawals', compact('page_title', 'withdrawals', 'empty_message'));
    }

    public function log()
    {
        $page_title = 'Withdrawal History';
        $withdrawals = Withdrawal::where('status', '!=', 0)->latest()->paginate(config('constants.table.default'));
        $empty_message = 'No withdrawal history';
        return view('admin.withdraw.withdrawals', compact('page_title', 'withdrawals', 'empty_message'));
    }

    public function approve(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        $withdraw = Withdrawal::findOrFail($request->id);

        $user = User::find($withdraw->user_id);

        $withdraw->update(['status' => 1]);

        $general = GeneralSetting::first(['cur_sym']);

        send_email($withdraw->user, 'WITHDRAW_APPROVE', [
            'trx' => $withdraw->trx,
            'amount' => $general->cur_sym . formatter_money($withdraw->amount),
            'receive_amount' => $general->cur_sym . formatter_money($withdraw->amount - $withdraw->charge),
            'charge' => $general->cur_sym . formatter_money($withdraw->charge),
            'method' => $withdraw->method->name,
        ]);

        send_sms($withdraw->user, 'WITHDRAW_APPROVE', [
            'trx' => $withdraw->trx,
            'amount' => $general->cur_sym . formatter_money($withdraw->amount),
            'receive_amount' => $general->cur_sym . formatter_money($withdraw->amount - $withdraw->charge),
            'charge' => $general->cur_sym . formatter_money($withdraw->charge),
            'method' => $withdraw->method->name,
        ]);


        $notify[] = ['success', 'Withdrawal has been approved.'];
        return redirect()->route('admin.withdraw.pending')->withNotify($notify);
    }

    public function reject(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        $withdraw = Withdrawal::findOrFail($request->id);


        $user = User::find($withdraw->user_id);
        $user->balance += $withdraw->amount;
        $user->save();

        $withdraw->update(['status' => 3]);

        $withdraw->user->transactions()->save(new Trx([
            'amount' => $withdraw->amount,
            'main_amo' => $withdraw->amount,
            'charge' => 0,
            'type' => 'withdraw',
            'title' => 'withdraw rejected Via  ' . $withdraw->method->name,
            'trx' => $withdraw->trx,
            'balance' => $user->balance,
        ]));


        $general = GeneralSetting::first(['cur_sym']);

        send_email($withdraw->user, 'WITHDRAW_REJECT', [
            'trx' => $withdraw->trx,
            'amount' => $general->cur_sym . formatter_money($withdraw->amount),
            'method' => $withdraw->method->name,
        ]);

        send_sms($withdraw->user, 'WITHDRAW_REJECT', [
            'trx' => $withdraw->trx,
            'amount' => $general->cur_sym . formatter_money($withdraw->amount),
            'method' => $withdraw->method->name,
        ]);


        $notify[] = ['success', 'Withdrawal has been rejected.'];
        return redirect()->route('admin.withdraw.pending')->withNotify($notify);
    }

    public function search(Request $request, $scope)
    {
        $search = $request->search;
        if (empty($search)) return back();
        $page_title = '';
        $empty_message = 'No search result found.';

        $withdrawals = Withdrawal::with(['user', 'method'])->where(function ($q) use ($search) {
            $q->where('trx', $search)->orWhereHas('user', function ($user) use ($search) {
                $user->where('username', $search);
            });
        });

        switch ($scope) {
            case 'pending':
                $page_title .= 'Pending Withdrawal Search';
                $withdrawals = $withdrawals->where('status', 2);
                break;
            case 'approved':
                $page_title .= 'Approved Withdrawal Search';
                $withdrawals = $withdrawals->where('status', 1);
                break;
            case 'rejected':
                $page_title .= 'Rejected Withdrawal Search';
                $withdrawals = $withdrawals->where('status', 3);
                break;
            case 'log':
                $page_title .= 'Withdrawal History Search';
                break;
        }

        $withdrawals = $withdrawals->paginate(config('constants.table.defult'));
        $page_title .= ' - ' . $search;


        return view('admin.withdraw.withdrawals', compact('page_title', 'empty_message', 'search', 'scope', 'withdrawals'));
    }
}
