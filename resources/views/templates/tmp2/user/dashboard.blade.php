@extends(activeTemplate() .'layouts.app')

@section('content')



<div class="row" style="margin-top: 100px;">
    <div class="col-md-9">
        <div class="row">
            <div class="col-xl-6 col-lg-6 col-sm-6">
                <div class="dashboard-w2 slice border-radius-5"  data-bg="2ecc71" data-before="27ae60"
                    style="background: #2ecc71; --before-bg-color:#27ae60;">
                    <div class="details" style="height: 180px !important;">
                         @php
                            $pptc = pricePayToCustomer(Auth::user()->id);
                         @endphp
                        <h2 class="amount mb-2 font-weight-bold">{{$general->cur_sym}}{{ $pptc ? $pptc : 0 }} </h2>
                        <h4 class="mb-3">@lang('Current Balance')</h4>
                        <a href="{{route('user.deposit.history')}}" class="btn btn-sm btn-neutral">@lang('View all')</a>
                    </div>
                    <div class="icon">
                        <i class="fa fa-money" style="font-size: 80% !important"></i> 
                    </div>
                </div>
            </div>

            {{--<div class="col-xl-4 col-lg-6 col-sm-6">
                <div class="dashboard-w2 slice bg-primary border-radius-5">
                    <div class="details">
                        <h2 class="amount mb-2 font-weight-bold">{{$general->cur_sym}}{{formatter_money($total_deposit)}} </h2>
                        <h4 class="mb-3">@lang('Total Deposit')</h4>
                        <a href="{{route('user.deposit.history')}}" class="btn btn-sm btn-neutral">@lang('View all')</a>
                    </div>
                    <div class="icon">
                        <i class="fa fa-money"></i>
                    </div>
                </div>
            </div>--}} 


            <div class="col-xl-6 col-lg-6 col-sm-6">
                <div class="dashboard-w2 slice bg-info border-radius-5">
                    <div class="details" style="height: 180px !important;">
                        <h2 class="amount mb-2 font-weight-bold">{{$general->cur_sym}}{{formatter_money($total_withdraw)}} </h2>
                        <h4 class="mb-3">@lang('Total Withdraw')</h4>
                        <a href="{{route('user.withdraw')}}" class="btn btn-sm btn-neutral">@lang('View all')</a>
                    </div>
                    <div class="icon">
                        <i class="fa fa-money" style="font-size: 80% !important"></i>
                    </div>
                </div>
            </div>

        {{--<div class="col-xl-4 col-lg-6 col-sm-6">
                <div class="dashboard-w2 slice bg-warning border-radius-5">
                    <div class="details">
                        <h2 class="amount mb-2 font-weight-bold">{{$general->cur_sym}}{{formatter_money($ref_com)}}</h2>
                        <h4 class="mb-3">@lang('Total Referral Commission')</h4>
                        <a href="{{route('user.level.com')}}" class="btn btn-sm btn-neutral">@lang('View all')</a>
                    </div>
                    <div class="icon">
                        <i class="fa fa-money"></i>
                    </div>
                </div>
            </div>--}}


            <div class="col-xl-6 col-lg-6 col-sm-6">
                <div class="dashboard-w2 slice border-radius-5" data-bg="ff793f">
                    <div class="details" style="height: 180px !important;">
                          @php
                            $pptc = pricePayToCustomer(Auth::user()->id);
                          @endphp
                        <h2 class="amount mb-2 font-weight-bold">{{$general->cur_sym}}{{ $pptc ? $pptc : 0 }}</h2>
                        <h4 class="mb-3">@lang('Total Level Commission')</h4>
                        <a href="{{route('user.level.com')}}" class="btn btn-sm btn-neutral">@lang('View all')</a>
                    </div>
                    <div class="icon">
                        <i class="fa fa-money" style="font-size: 80% !important"></i>
                    </div>
                </div>
            </div>


            {{--<div class="col-xl-4 col-lg-6 col-sm-6">
                <div class="dashboard-w2 slice bg-dark border-radius-5">
                    <div class="details">
                        <h2 class="amount mb-2 font-weight-bold">{{$general->cur_sym}}{{formatter_money($total_epin_recharge)}}</h2>
                        <h4 class="mb-3">@lang('Total E-Pin Recharged')</h4>
                        <a href="{{route('user.e_pin.recharge')}}" class="btn btn-sm btn-neutral">@lang('View all')</a>
                    </div>
                    <div class="icon">
                        <i class="fa fa-cart-plus"></i>
                    </div>
                </div>
            </div>


            <div class="col-xl-4 col-lg-6 col-sm-6">
                <div class="dashboard-w2 slice bg-default border-radius-5">
                    <div class="details">
                        <h2 class="amount mb-2 font-weight-bold">{{$general->cur_sym}}{{formatter_money($total_epin_generate)}}</h2>
                        <h4 class="mb-3">@lang('Total E-Pin Generated')</h4>
                        <a href="{{route('user.e_pin.generated')}}" class="btn btn-sm btn-neutral">@lang('View all')</a>
                    </div>
                    <div class="icon">
                        <i class="fa fa-plus-circle"></i>
                    </div>
                </div>
            </div>



            <div class="col-xl-4 col-lg-6 col-sm-6">
                <div class="dashboard-w2 slice bg-blue border-radius-5">
                    <div class="details">
                        <h2 class="amount mb-2 font-weight-bold">{{$general->cur_sym}}{{formatter_money($total_bal_transfer)}}</h2>
                        <h4 class="mb-3">@lang('Total Transferred Balance')</h4>
                        <a href="{{route('user.balance.tf.log')}}" class="btn btn-sm btn-neutral">@lang('View all')</a>
                    </div>
                    <div class="icon">
                        <i class="fa fa-random"></i>
                    </div>
                </div>
            </div>--}}
            <div class="col-xl-6 col-lg-6 col-sm-6">
                <div class="dashboard-w2 slice bg-red border-radius-5">
                    <div class="details" style="height: 180px !important;">
                        <h2 class="amount mb-2 font-weight-bold">{{$total_direct_ref}}</h2>
                        <h4 class="mb-3">@lang('Total My Direct Referral')</h4>
                        <a href="{{route('user.ref.index')}}" class="btn btn-sm btn-neutral">@lang('View all')</a>
                    </div>
                    <div class="icon">
                        <i class="fa fa-sitemap" style="font-size: 80% !important"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="row">
            @php
                $data = \App\Plan::first();
            @endphp
            @if ($data)
                <div class="price-body text-center bg-white col-12">
                    <ul class="margin-bottom-30">
                        @php $total = 0; @endphp
                        @php $counter = 0; @endphp
                        @foreach($data->plan_level as $key => $lv)
                            @if($key+1 <= $general->matrix_height)
                                <li class="d-flex justify-content-start">
                                    <strong>  @lang('Level '.$lv->level.' ')
                                    {{--: {{$general->cur_sym}} {{$lv->amount}}
                                    X {{pow($general->matrix_width,$key+1)}} <i class="fa fa-users"></i>--}} =
                                    @php
                                        $pr = $lv->amount*pow($general->matrix_width,$key+1);
                                    @endphp
                                    @if ($pr > 100000 && $pr <= 101000)
                                        {{$general->cur_sym}}{{number_format((float)100000, 0, '.', ' ')}}</strong>
                                    @else
                                        {{$general->cur_sym}}{{number_format((float)$pr, 0, '.', ' ')}}</strong>
                                    @endif
                                    
                                </li>
                                @php $total += $lv->amount*pow($general->matrix_width,$key+1); @endphp
                            @endif
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
</div>


@endsection
