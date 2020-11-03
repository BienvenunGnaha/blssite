<?php

namespace App\Http\Controllers\Admin;

use App\Deposit;
use App\Epin;
use App\Trx;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\User;
use App\UserLogin;
use App\Withdrawal;
use App\GeneralSetting;
use App\Http\Traits\Matrix;

class AdminController extends Controller
{

    use Matrix;

    public function dashboard(Request $request)
    {

        $page_title = 'Dashboard';
        $users = User::all();
        $data['total_ref_get'] = 0;
        foreach($users as $us){
          $pr = pricePayToCustomer($us->id);
          $data['total_ref_get'] = $data['total_ref_get'] + $pr;
        }
        $user_login_data = UserLogin::whereYear('created_at', '>=', \Carbon\Carbon::now()->subYear())->get(['browser', 'os', 'country']);
        $chart['user_browser_counter'] = $user_login_data->groupBy('browser')->map(function ($item, $key) {
            return collect($item)->count();
        });

        $chart['user_os_counter'] = $user_login_data->groupBy('os')->map(function ($item, $key) {
            return collect($item)->count();
        });

        $chart['user_country_counter'] = $user_login_data->groupBy('country')->map(function ($item, $key) {
            return collect($item)->count();
        })->sort()->reverse()->take(5);

        $widget['total_users'] = User::all('sv', 'ev', 'status', 'balance');
        $widget['deposits'] = Deposit::where('status', 1)->selectRaw('COUNT(*) as total, SUM(rate * charge) as total_charge')->selectRaw('SUM(rate * amount) as total_amount')->first();

        $widget['withdrawals'] = Withdrawal::where('status', 1)->selectRaw('COUNT(*) as total, SUM(rate * charge) as total_charge')->selectRaw('SUM(rate * amount) as total_amount')->first();

        $widget['pending_withdrawal'] = Withdrawal::where('status', 0)->count();


        $data['total_pu_plan'] = Trx::whereType(7)->sum('amount');
        
        $data['total_e_pin_re'] = Trx::whereType(9)->sum('amount');
        $data['total_ref_bonus'] = Trx::whereType(4)->sum('amount');

         //dd($data['total_ref_bonus']);
        $deposit_chart = $this->dailyChart();
        


        return view('admin.dashboard', compact('page_title', 'chart', 'widget', 'deposit_chart' ), $data);
    }


    public function matrix(Request $request){
        $page_title = '';
        $users = User::All();
        $gs = GeneralSetting::first();

        return view('admin.users.matrix_user_manager', compact('gs', 'users', 'page_title'));
    }

    public function usersTarget(Request $request, $id, $type){
        $users = [];
        switch ((int)$type) {
            case 1:
                $user = User::find($id);
                $p_id = $user->position_id;
                if($p_id == '0'){
                    $users[] = [null, null];
                }else{
                    $users[] = [User::find($p_id)->toArray(), count(User::where('position_id', $p_id)->get())];
                }
                
                break;
            case 2:
                $uss = User::where('position_id', $id)->get();
                
                foreach($uss as $us){
                    $users[] = [$us->toArray(), count(User::where('position_id', $us->id)->get())];
                    
                }
                
                break;
            case 3:
                $uss = User::where('id', '!=', $id)->get();
                
                foreach($uss as $us){
                    $users[] = [$us->toArray()];
                    
                }
                
                break;
            default:
            return response()->json([], 500);
                break;
        }

        return response()->json($users, 200);
    }

    public function updateUserMatrix(Request $request){
        //die();
        $this->validate($request, [
            'matrix_update_type' => 'required',
            'user' => 'required',
        ]);
           
        switch ((int)$request->matrix_update_type) {
            case 1:
                $pos = isset($request->position) ? $request->position : '0';
                $this->updateMatrixUp($request->user, $pos);
                break;
            case 2:
                $pos = isset($request->position) ? $request->position : '0';
                $this->updateMatrixUp($request->user_target, $pos);
                break;
            case 3:
                $pos = isset($request->position) ? $request->position : '0';
                $this->updateMatrixReplace($request->user, $request->user_target);
                break;
            default:
                # code...
                break;
        }

        return redirect($request->headers->get('referer'));
    
    }

    public function dailyChart(){

        $deposit_chart = Deposit::whereYear('created_at', '=', date('Y'))->where('status', 1)->get()->groupBy(function($d) {
            return $d->created_at->format('F');
        });




        $withdraw_chart = Withdrawal::whereYear('created_at', '=', date('Y'))->where('status', 1)->get()->groupBy(function($d) {
            return $d->created_at->format('F');
        });




        $monthly_chart_day = collect([]);
        $monthly_chart_amount = collect([]);
        $withdraw_chart_amount = collect([]);


        foreach (month_arr() as $key => $value) {
            $monthly_chart_day->push(
                 Carbon::parse(date('Y') . '-' . $key)->format('Y-m')
            );
                $monthly_chart_amount->push(
                    $deposit_chart->has($value)?$deposit_chart[$value]->sum('amount') :0
                );

            $withdraw_chart_amount->push(
                $withdraw_chart->has($value)?$withdraw_chart[$value]->sum('amount') :0
                );

        }



        return ['day' => $monthly_chart_day, 'amount' => $monthly_chart_amount, 'w_amount' => $withdraw_chart_amount];
    }








    public function profile()
    {
        $page_title = 'Profile';
        $admin = Auth::guard('admin')->user();
        return view('admin.profile', compact('page_title', 'admin'));
    }

    public function profileUpdate(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'image' => 'nullable|image|mimes:jpg,jpeg,png'
        ]);

        $user = Auth::guard('admin')->user();

        if ($request->hasFile('image')) {
            try {
                $old = $user->image ?: null;
                $user->image = upload_image($request->image, config('constants.admin.profile.path'), config('contants.admin.profile.size'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Image could not be uploaded.'];
                return back()->withNotify($notify);
            }
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();
        $notify[] = ['success', 'Your profile has been updated.'];
        return redirect()->route('admin.profile')->withNotify($notify);
    }

    public function passwordUpdate(Request $request)
    {
        $this->validate($request, [
            'old_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = Auth::guard('admin')->user();
        if (!Hash::check($request->old_password, $user->password)) {
            $notify[] = ['error', 'Password Do not match !!'];
            return back()->withErrors(['Invalid old password.']);
        }
        $user->update([
            'password' => bcrypt($request->password)
        ]);

        $notify[] = ['success', 'Password Changed Successfully'];
        return redirect()->route('admin.profile')->withNotify($notify);

    }



    function managePin(){
        $page_title = 'Manage Pin';
        $trans = Epin::latest()->whereStatus(1)->paginate(15);
        return view('admin.epin.e_pin',compact('page_title', 'trans'));
    }

    function storePin(Request $request){
        $this->validate($request,[
            'amount' => 'required',
            'number' => 'required|numeric|min:1'
        ]);


        for ($a = 0; $a < intval($request->number); $a++ ){

            $pin = rand(10000000,99999999).'-'.rand(10000000,99999999).'-'.rand(10000000,99999999).'-'.rand(10000000,99999999);
            Epin::create([
                'user_id' => 0,
                'pin' => $pin,
                'amount' => $request->amount,
                'status' => 1,
            ]);
        }

        return back()->with('success','Successfully Generated');
    }

    function UsedPin(){
        $page_title = 'Used Pin';
        $trans = Epin::whereStatus(2)->latest()->paginate(15);
        return view('admin.epin.e_pin',compact('page_title', 'trans'));

    }
}
		