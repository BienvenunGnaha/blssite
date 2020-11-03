<?php
namespace App\Http\Traits;


use App\GeneralSetting;
use App\Plan;
use App\User;
use App\MatrixLevel;


trait Matrix{

    function get_position($user_id){
        $user = User::find($user_id);
             if ($user->ref_id != 0){
                return $this->assign_position($user->id);
             }
         
    }

    function assign_position($user_id){

        $user = User::find($user_id);
        $refer = User::find($user->ref_id);
  
         $stststs = 0;
       
       
        if($this->khali_ache_naki($refer->id)!=0){
            $user->position_id = $refer->id;
            $user->position = $this->khali_ache_naki($refer->id);
            $user->save();
            
        }else{
        
             for ($level=1; $level < 100 ; $level++) {
            
                    $myref = $this->showPositionBelow($refer->id);
                         $nxt =   $myref;
                    for ($i=1; $i < $level ; $i++) {
                        $nxt = array();
                        foreach($myref as $uu){
                            $n = $this->showPositionBelow($uu);
                            $nxt = array_merge($nxt, $n);
                        }
                        $myref = $nxt;
                    }
            
            
                foreach($nxt as $uu){
                    
                    if($this->khali_ache_naki($uu)!=0){
                        $user->position_id = $uu;
                        $user->position =$this->khali_ache_naki($uu);
                        $user->save();
                       $stststs = 1;
                    }
                    
                    if($stststs == 1){
                        break;
                    }
          
                }
                
                 if($stststs == 1){
                    break;
                }
             }
            
        }
    
    }

    function showPositionBelow($id){
        $arr = array();
        $under_ref = \App\User::where('position_id',$id)->get();
        foreach($under_ref as $u){
            array_push($arr,$u->id);
        }
        return $arr;
    }

    function khali_ache_naki($user_id){
        $count = User::where('position_id',$user_id)->count();
        
        if($count < $this->matrix_width()){
            return $count+1;
        }else{
            return 0;
        }
        
    }

    function give_referral_commission($user_id, $plan_id){

        $user = User::find($user_id);
        $plan = Plan::whereId($plan_id)->first();
        if($user){
            if ($user->ref_id != 0){
                $refer = $this->user_valid($user->ref_id);
                $refer->update(['balance' => $refer->balance + $plan->ref_bonus]);

                $refer->transactions()->create([
                    'amount' => $plan->ref_bonus,
                    'user_id' => $refer->id,
                    'main_amo' => $plan->ref_bonus,
                    'balance' => $this->pricePayToCustomer($refer->id) ? $this->pricePayToCustomer($refer->id) : 0.00,
                    'type' => 4,
                    'title' => 'Referral Bonus from ' .$user->username,
                    'trx' => getTrx(),
                ]);

                $gnl = GeneralSetting::first();
                send_email($refer, 'referral_commission', [

                    'amount' => $plan->ref_bonus . ' ' . $gnl->cur_text,
                    'username' => $user->username,


                ]);

                send_sms($refer, 'referral_commission', [

                    'amount' => $plan->ref_bonus . ' ' . $gnl->cur_text,
                    'username' => $user->username,

                ]);
            }

        }
    }

    function give_level_commission($user_id, $plan_id){

        $fromUser = User::find($user_id);
        $usr = $user_id;
        $plan = Plan::whereId($plan_id)->with('plan_level')->first();
        $i = 1;
        while($usr !="" || $usr != "0" || $i <= $this->matrix_height()){
            $me = User::find($usr);
            if($this->user_valid($me->position_id) === false){ break; }
            $refer = $this->user_valid($me->position_id);

            if ($i <= $this->matrix_height()){


                // echo $refer->id.'<br>';

                $commission = $plan->plan_level->where('level',$i)->first();
                if (!$commission){ break; }
                $refer->update(['balance' => $refer->balance + $commission->amount]);
                $refer->transactions()->create([
                    'amount' => $commission->amount,
                    'user_id' => $refer->id,
                    'main_amo' => $commission->amount,
                    'balance' => $this->pricePayToCustomer($refer->id) ? $this->pricePayToCustomer($refer->id) : 0.00,
                    'type' => 11,
                    'title' => 'Level '.$i.' Commission From '. $fromUser->username,
                    'trx' => getTrx(),
                ]);


                $gnl = GeneralSetting::first();
                send_email($refer, 'level_commission', [

                    'amount' => $commission->amount . ' ' . $gnl->cur_text,
                    'level_number' => $i,
                    'username' => $fromUser->username,


                ]);

                send_sms($refer, 'level_commission', [

                    'amount' => $commission->amount . ' ' . $gnl->cur_text,
                    'level_number' => $i,
                    'username' => $fromUser->username,

                ]);

            }

            $usr = $refer->id;
            $i++;
        }
        return 0;
    }

    function user_valid($user_id){
        $user = User::find($user_id);
        return $user ? $user:false;
    }

    function matrix_width(){
       return GeneralSetting::first()->matrix_width;
    }

    function matrix_height(){
        return GeneralSetting::first()->matrix_height;
    }

    function pricePayToCustomer($user_id){
        $user = $this->user_valid($user_id);
        if($user && $user->plan_id != 0){

            $plan = Plan::find($user->plan_id);
            if($plan){
                //$matrix_level = MatrixLevel::where
                $count = User::where('ref_id', $user_id)->count();
                $height = $this->matrix_height();
                $width = $this->matrix_width();
                $listPrice = [];
                $listAffilie = [];
                $number = 0;
                $numAff = 0;
                for($i = 1; $i <= $height; $i++){
                    $numAff += $width ** $i;
                    $listPrice[] = [$numAff, MatrixLevel::where('plan_id', '=', $plan->id)->where('level', '=', $i)->first()->amount * ($width ** $i)];
                }

                for($i = 1; $i <= $height; $i++){
                    $numberPeople = $width ** $i;
                    
                    if($i == 1){
                        $people = User::where('position_id', $user_id)->where('plan_id', '!=', 0)->get();
                        $listAffilie[] = [$people];
                        $number += count($people);
                    }else{
                        if($number != 0){
                            $peoplePrev = $listAffilie[$i-2];
                            $peopl = [];
                            $listAffilie[] = [];
                            //$listAffilie[$i-1][] = 'grr';
                            foreach($peoplePrev as $pps){
                                for($n = 0; $n < count($pps); $n++){
                                    $pp = $pps[$n];
                                        //dump($p);
                                    if($pp instanceof User){
                                        $colsUser = User::where('position_id', $pp->id)->where('plan_id', '!=', 0)->get();
                                        //$peopl[] = $colsUser;
                                        $ppCount = count($colsUser);
                                        $number += $ppCount;
                                        $listAffilie[$i-1][] = $colsUser;
                                    }
                                        
                                }
                                
                            }
                            
                        }
                    }
                }
                
                //dump($number);

                $price = null;

                foreach ($listPrice as $key => $value) {
                    # code...
                    if($key != count($listPrice)-1){
                        if($value[0] <= $number){
                            $price = $value[1];
                            
                        }
                    }else{
                        if($value[0] <= $number && $listPrice[$key+1][0] > $number){
                            $price = $value[1];
                            //dd($price);
                        }
                        
                    }
                }
                //dd($price);
                return $price;
            }
        }

        return null;
    }

    function updateMatrixUp($user_id, $position=null){
        $user = $this->user_valid($user_id);
        if($user instanceof User && $user->position_id != '0'){
            $pos_user = User::find($user->position_id);
            
            $refUs = User::where('ref_id', $user->id)->get();
            $posUs = User::where('position_id', $user->id)->get();

            $refUs2 = User::where('ref_id', $pos_user->id)->get();
            $posUs2 = User::where('position_id', $pos_user->id)->get();

            $p_id = $user->position_id;
            $ref = $user->ref_id;
            $pos = $user->position;

            $p_id2 = $pos_user->position_id;
            $ref2 = $pos_user->ref_id;
            $pos2 = $pos_user->position;

            $pos_user->position_id = $user->id;
            $pos_user->ref_id = $ref;
            $pos_user->position = $pos;

            $user->position_id = $p_id2;
            $user->ref_id = $ref2;
            $user->position = $pos2;
            
            foreach($refUs as $refU){
                $refU->ref_id = $pos_user->id;
                $refU->save();
            }

            foreach($posUs as $posU){
                $posU->position_id = $pos_user->id;
                $posU->save();
            }

            foreach($refUs2 as $refU2){
                $refU2->ref_id = $user->id;
                $refU2->save();
            }

            foreach($posUs2 as $posU2){
                $posU2->position_id = $user->id;
                $posU2->save();
            }

           
            
            $user->save();
            $pos_user->save();
            
        }
    }

    function updateMatrixReplace($user_id, $user_rid){
        $user = $this->user_valid($user_id);
        $pos_user = User::find($user_rid);
        if($user instanceof User && $pos_user instanceof User){
            
            
            $refUs = User::where('ref_id', $user->id)->get();
            $posUs = User::where('position_id', $user->id)->get();

            $refUs2 = User::where('ref_id', $pos_user->id)->get();
            $posUs2 = User::where('position_id', $pos_user->id)->get();

            $p_id = $user->position_id;
            $ref = $user->ref_id;
            $pos = $user->position;

            $p_id2 = $pos_user->position_id;
            $ref2 = $pos_user->ref_id;
            $pos2 = $pos_user->position;

            $pos_user->position_id = $user->id;
            $pos_user->ref_id = $ref;
            $pos_user->position = $pos;

            $user->position_id = $p_id2;
            $user->ref_id = $ref2;
            $user->position = $pos2;
            
            foreach($refUs as $refU){
                $refU->ref_id = $pos_user->id;
                $refU->save();
            }

            foreach($posUs as $posU){
                $posU->position_id = $pos_user->id;
                $posU->save();
            }

            foreach($refUs2 as $refU2){
                $refU2->ref_id = $user->id;
                $refU2->save();
            }

            foreach($posUs2 as $posU2){
                $posU2->position_id = $user->id;
                $posU2->save();
            }

           
            
            $user->save();
            $pos_user->save();
            
        }
    }

}