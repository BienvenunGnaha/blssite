<?php

namespace App\Classes;

use App\Frontend;

class TranslateFrontend
{
    public function translate(Frontend $frontend){
        $lang = session()->get('lang');
        
        $found = false;
        $value = $frontend->value;
        if(isset($value->lang)){
            $langg = (array)$value->lang;
            if(isset($langg[$lang])){
                $found = true;
            }
        }

        $data = null;
        if($found){
            $data = $value->lang->$lang;
        }
        else{
            $data = $value;

        }

        

        return $data;
    }
}