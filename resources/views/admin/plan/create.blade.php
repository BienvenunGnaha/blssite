@extends('admin.layouts.app')
@section('style')
    <style>
        .form-control-lg{
            padding: 20px 12px;
            background: #000000;
            color: red;
            font-weight: 700;
            font-size: 30px;
        }

        .form-control:focus {
            color: #ff0000;
            background-color: #000000;
            border-color: #ff0000;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }
    </style>
@stop
@section('panel')



    <div class="row">

        <div class="col-md-6">
            <div class="card ">
                <h3 class="card-header "> @lang($page_title)</h3>
                <form id="frmProducts" method="post" action="{{route('admin.plan.store')}}">
                    @csrf
                <div class="card-body table-responsive">
                    <div class="form-group">
                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <strong>@lang('Plan Name'):</strong>
                                    <input type="text" class="form-control form-control-lg"  name="name" placeholder="Name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <strong>@lang('Plan Price'):</strong>
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-lg plan-price'" id="plan_price"    name="price"  placeholder="@lang('Plan Price')" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">{{$general->cur_sym}}</span>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <strong>@lang('Referral Bonus'):</strong>
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-lg "   name="ref_bonus"   placeholder="@lang('Referral Bonus')" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">{{$general->cur_sym}}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-12 pb-4"><h4>Level Commissions</h4></div>

                            @if(intval($general->matrix_height))
                                @for($i = 1; $i <= intval($general->matrix_height); $i++)
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <strong>@lang('Level '.$i.' Commission'):</strong>
                                            <div class="input-group">
                                                <input type="text" class="form-control form-control-lg planamountsum" onkeyup="testcalc()"   name="amount[]"  placeholder="@lang('Amount')" required>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">{{$general->cur_sym}}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            @endif
                        </div>
                    </div>


                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success btn-block bold uppercase"><i class="fa fa-send"></i> @lang('Save')</button>
                </div>
                </form>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-dark">
                <h3 class="card-header text-white">@lang('Profit/Loss Calculation')</h3>
               <div class="card-body">
                   <div class="row  mt-50">
                       <div class="col-md-12">
                           <div class="alert alert-secondary text-center" id="giveStatus">
                               <h2><strong>@lang('Plan Price') : {{$general->cur_sym}}</strong><strong id="planPrice"></strong></h2>
                               <h2><strong>@lang('Referral Commission') : {{$general->cur_sym}}</strong><strong id="refCom"></strong></h2>
                               <h2><strong>@lang('Total Level Commission') : {{$general->cur_sym}}</strong><strong id="totalLvlCom"></strong></h2>
                               <h1 id="profitORLoss"></h1>
                           </div>
                       </div>
                   </div>
               </div>
            </div>
        </div>
    </div>

@stop
@push('script')

    <script>

        $('#plan_price').keyup(function(){
           $('#planPrice').text($(this).val());
        });

        $('input[name=ref_bonus]').keyup(function(){
            $('#refCom').text($(this).val());
        });


        $('#profitORLoss').val();



        function testcalc(){
            var x=0;
            var planPrice   = $('#plan_price').val();
            var refBonus    = $('input[name=ref_bonus]').val();

            $('.planamountsum').each(function(e){
                if($(this).val()!=''){
                    x += +$(this).val();
                }
            })

            var totalLvlCom = $('#totalLvlCom');


            totalLvlCom.text(x);

            var total = x + Number(refBonus);

            var finalAmount = planPrice - total;

            if(planPrice > total){
                $('#giveStatus').removeClass().addClass('alert alert-success text-center')
                $('#profitORLoss').html('<strong>Admin Profit : ' + finalAmount + '{{$general->cur_sym}}' + '</strong>')

            }else {
                $('#giveStatus').addClass('alert alert-danger text-center')
                $('#profitORLoss').html('<strong>Admin Loss : ' + finalAmount + '{{$general->cur_sym}}' + '</strong>')
            }




        };
    </script>


@endpush
