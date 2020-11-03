<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Frontend;
use App\GeneralSetting;
use App\MatrixLevel;
use App\Pronostic;
use App\Plan;
use App\Bookmaker;
use App\User;
use App\Gateway;
use App\Http\Traits\Matrix;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

use Stripe\Stripe;
use Stripe\Customer;
use Stripe\SetupIntent;
use Stripe\Subscription;
use Stripe\OAuth;
use Stripe\PaymentIntent;
use Slim\App;
use Slim\Http\Request as SlimRequest;
use Slim\Http\Response;

class SiteController extends Controller
{

    use Matrix;

    public function home(){
        $frontend = Frontend::where('key', 'blog.title')->OrWhere('key', 'testimonial.title')
            ->orWhere('key', 'service.title')->orWhere('key', 'howWork.title')
            ->orWhere('key', 'about.title') ->orWhere('key', 'vid.post')->orWhere('key', 'plan.title')->get();


        $data['blog_title'] = $frontend->where('key', 'blog.title')->first() ;
        $data['blogs'] = Frontend::where('key', 'blog.post')->latest()->take(3)->get();
        $data['sliders'] = Frontend::where('key', 'banner')->get();

        $data['testimonial_title'] = $frontend->where('key', 'testimonial.title')->first();
        $data['testimonial'] = Frontend::where('key', 'testimonial')->get();
        $data['service_titles'] = $frontend->where('key', 'service.title')->first();
        $data['service'] = Frontend::where('key', 'service.item')->get();
        $data['about'] = $frontend->where('key', 'about.title')->first();
        $data['plans'] = Plan::where('status', 1)->get();
        $data['plan_title'] = $frontend->where('key', 'plan.title')->first();

        $data['how_it_work_title'] = $frontend->where('key', 'howWork.title')->first();
        $data['video_section'] = $frontend->where('key', 'vid.post')->first();

        $data['how_it_work'] = Frontend::where('key', 'howWork.item')->get();
        return response()->json($data, 200);
    }

    function plans(){
        $gs = GeneralSetting::first();
        $data['matrix_width'] = $gs ? $gs->matrix_width : 0;
        $data['matrix_height'] = $gs ? $gs->matrix_height : 0;
        $data['plans'] = Plan::where('status', 1)->get();
        $data['matrix_level'] = MatrixLevel::all();
        $data['user'] = auth('api')->user();
        return response()->json($data, 200);
    }

    function planStore(Request $request)
    {


        $this->validate($request, ['plan_id' => 'required|integer', 'pm' => 'required|string']);
        $plan = Plan::find($request->plan_id);
        $gnl = GeneralSetting::first();
        if ($plan) {
            $user = auth('api')->user();
            if ($user->plan_id != 0) {

                $notify = ['error', 'You have already subscribed. Please unsubscribe you and choose another plan if you want change plan.'];
                return response()->json($notify, 500);
            }
            $settings = json_decode($plan->plan_settings);
            $gwStripe = Gateway::where('alias', '=', 'Stripe')->where('status', '=', 1)->first();
            $parameter = json_decode($gwStripe->parameter_list);
            Stripe::setApiKey($parameter->secret_key->value);
            $alias = $gwStripe->alias;
            
            try {
                Customer::update(
                  $user->stripe_cus,
                  ['invoice_settings' => ['default_payment_method' => $request->pm]]
                );
            } catch (CardException $e) {
                $notify = ['error', 'Could not create subscription with this payment method.'];
                return response()->json($notify, 500);
            }

            $subscription = Subscription::create([
                'customer' => $user->stripe_cus,
                'items' => [['plan' => $settings->$alias]],
                'off_session' => TRUE
            ]);
            //dd($subscription->status);
            if($subscription->status == 'active' || $subscription->status == 'succeeded'){
                $user->update(['plan_id' => $plan->id, 'stripe_subs' => $subscription->id, 'temp' => 0]);
                
                if ($user) {
                    $user->transactions()->create([
                        'trx' => 'Subscription '.$plan->name,
                        'user_id' => $user->id,
                        'amount' => $plan->price,
                        'main_amo' => $plan->price,
                        'balance' => $user->balance,
                        'title' => 'Purchased ' . $plan->name,
                        'charge' => 0,
                        'type' => 7,
                    ]);
                    //hit position start
                    $this->get_position($user->id);
                    //hit position end



                    //hit position start
                    //$this->give_referral_commission($user->id, $plan->id);
                    //hit position end

                    /// //hit ref level commission start
                    $this->give_level_commission($user->id, $plan->id);
                    //hit ref level commission end



                    send_email($user, 'pan_purchased', [

                        'name' => $plan->name,
                        'price' => $plan->price . ' ' . $gnl->cur_text,
                        'balance_now' => $user->balance,

                    ]);

                    /*send_sms($user, 'pan_purchased', [

                        'name' => $plan->name,
                        'price' => $plan->price . ' ' . $gnl->cur_text,
                        'balance_now' => $user->balance,
                    ]);*/

                    $notify = ['success', 'Subscribed ' . $plan->name . ' Successfully'];
                    return response()->json($notify, 200);
                }
            }

                $notify = ['error', 'Something Went Wrong'];
                return response()->json($notify, 500);

        }
        $notify = ['error', 'Something Went Wrong'];
        return response()->json($notify, 500);
    }

    public function pronostics(Request $request)
    {
        $data['page_title'] = "pronostics";
        $data['pronostics'] = Pronostic::orderBy('created_at', 'DESC')->paginate(10);
        //$data['today'] = Pronostic::where('is_win', '=', false)->orderBy('match_date', 'DESC')->firstOrFail();
        $data['testimonials'] = Frontend::where('key', 'testimonial')->latest()->paginate(5);
        $rss = session()->get('lang') == 'en' ? file_get_contents('https://www.eurosport.com/rss.xml') : file_get_contents('https://www.eurosport.fr/rss.xml');
        $items = (new \SimpleXmlElement($rss))->channel->item;
        $total = count($items);
        
        $flux = [];
        for($i = 0; $i < $total; $i++){
            $item = $items[$i];
            //dd($item);   
            $flux[] = (object)['link' => (string)$item->link, 'title' => (string)$item->title, 'pubDate' => (string)$item->pubDate, 'sport' => (string)$item->category[0]];
        }
        //dd($flux);   
        $currentPage = (int)$request->request->get('page') > 1 ? (int)$request->request->get('page') : 1;
        //$flux = new LengthAwarePaginator($flux, count($flux), 5, $currentPage);
        $fluxPart = array_chunk($flux, 10);
        $flux = array_slice($flux, ($currentPage-1)*10, $currentPage*10);
        $data['flux'] = $flux;
        $data['page_number'] = count($fluxPart);
        $data['current_page'] = $currentPage;
        $data['bookmakers'] = Bookmaker::all();
        //dd($testimonials);
        return response()->json($data, 200);
    }
	

    function sendEmailContact(Request $request)
    {

        $this->validate($request, [
            'email' => 'required',
            'name' => 'required|',
            'message' => 'required|',
        ]);


        $from = $request->email;
        $name = $request->name;
        $message= $request->message;
        $subject= 'Contact mail from '. $request->name;

        $general = GeneralSetting::first();
        $config = $general->mail_config;
        if ($config->name == 'php') {
            send_php_mail($general->contact_email, $from, $name, $subject, $message);
        } else if ($config->name == 'smtp') {
            send_smtp_mail($config, $general->contact_email, $general->sitetitle, $from, $name, $subject, $message);
        } else if ($config->name == 'sendgrid') {
            send_sendgrid_mail($config, $general->contact_email, $general->sitetitle, $from, $name, $subject, $message);
        } else if ($config->name == 'mailjet') {
            send_mailjet_mail($config, $general->contact_email, $general->sitetitle, $from, $name, $subject, $message);
        }



        $notify[] = ['success', 'Mail Send successfully'];
        return response()->json($notify);

    }

    function matrixIndex($lv_no)
    {


        $gnl = GeneralSetting::first();
        $user = auth('api')->user();
        if ($lv_no > $gnl->matrix_height) {

            $notify[] = ['error', 'No Level Found.'];

            return response()->json($notify, 500);
        }
        $data = apiShowUserLevel($user->id, $lv_no);
        return response()->json($data, 200);
    }
}
