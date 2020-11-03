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
use App\Pronostic;
use App\Sport;

class PronosticController extends Controller
{

    

    public function index(Request $request)
    {

        $page_title = 'Pronostics';
        $pronos = Pronostic::orderBy('created_at', 'DESC')->paginate(10);
        
        return view('admin.pronostic.pronostic', compact('page_title', 'pronos'));
    }

    public function create(Request $request)
    {
        //dd(base_path());
        $page_title = 'Ajouter Pronostics';
        $sports = Sport::all()->toJson();

        return view('admin.pronostic.create', compact('page_title', 'sports'));
    }

    public function post(Request $request)
    {

        $this->validate($request, [
            'details' => 'required',
            'button' => 'required',
            'button_fr' => 'required'
        ]);
        $prono = new Pronostic();
        $www = str_replace('core', '', base_path());
        $details = json_decode($request->request->get('details'), true);
        
        $count = count($details);
        for($i = 0; $i < $count; $i++){
            $detail = $details[$i];
            $icon1 = $detail['team1_icon'];
            $icon2 = $detail['team2_icon'];

            if($icon1){
                $decode1 = explode(',', $icon1);
                $mime = explode(':', explode(';', $decode1[0])[0])[1];
                $filename = config('constants.pronostic.path').'/'.uniqid((string)time()).'.'.explode('/', $mime)[1];
                file_put_contents($www.$filename, base64_decode($decode1[1]));
                $detail['team1_icon'] = $filename;
            }

            if($icon2){
                $decode2 = explode(',', $icon2);
                $mime2 = explode(':', explode(';', $decode2[0])[0])[1];
                $filename2 = config('constants.pronostic.path').'/'.uniqid((string)time()).'.'.explode('/', $mime2)[1];
                file_put_contents($www.$filename2, base64_decode($decode2[1]));
                $detail['team2_icon'] = $filename2;
            }

            $details[$i] = $detail;
        }
        //dd($details);


        $prono->details = json_encode($details);
        $prono->conseil = $request->request->get('conseil');
        $prono->conseil_fr = $request->request->get('conseil_fr');
        $prono->save();


        $notify[] = ['success', 'Create Successfully'];
        return back()->withNotify($notify);
    }

    public function edit(Request $request, $id)
    {

        $page_title = 'Editer Pronostics';
        $prono = Pronostic::find($id);
        $sports = Sport::all()->toJson();

        return view('admin.pronostic.edit', compact('page_title', 'prono', 'sports'));
    }

    public function update(Request $request, $id)
    {

        $this->validate($request, [
            'details' => 'required',
            'button' => 'required',
            'button_fr' => 'required'
        ]);
        $prono = Pronostic::find($id);
        $www = str_replace('core', '', base_path());
        $details = json_decode($request->request->get('details'), true);
        
        $count = count($details);
        for($i = 0; $i < $count; $i++){
            $detail = $details[$i];
            $icon1 = $detail['team1_icon'];
            $icon2 = $detail['team2_icon'];

            if($icon1){
                $decode1 = explode(',', $icon1);
                if(is_array($decode1) && count($decode1) > 1){
                    $mime = explode(':', explode(';', $decode1[0])[0])[1];
                    $filename = config('constants.pronostic.path').'/'.uniqid((string)time()).'.'.explode('/', $mime)[1];
                    file_put_contents($www.$filename, base64_decode($decode1[1]));
                    $detail['team1_icon'] = $filename;
                }
                
            }

            if($icon2){
                $decode2 = explode(',', $icon2);
                
                if(is_array($decode2) && count($decode2) > 1){
                    
                    $mime2 = explode(':', explode(';', $decode2[0])[0])[1];
                    $filename2 = config('constants.pronostic.path').'/'.uniqid((string)time()).'.'.explode('/', $mime2)[1];
                    file_put_contents($www.$filename2, base64_decode($decode2[1]));
                    $detail['team2_icon'] = $filename2;
                }
            }

            $details[$i] = $detail;
        }
        
        $prono->button = $request->request->get('button');
        $prono->button_fr = $request->request->get('button_fr');
        $prono->details = json_encode($details);
        $prono->conseil = $request->request->get('conseil');
        $prono->conseil_fr = $request->request->get('conseil_fr');
        $prono->save();


        $notify[] = ['success', 'Update Successfully'];
        return back()->withNotify($notify);
    }

    public function destroy(Request $request, $id){
        $prono = Pronostic::find($id);
        if($prono instanceof Pronostic){
            $prono->delete();
        }

        return redirect(route('admin.pronostic'));
    }

}
