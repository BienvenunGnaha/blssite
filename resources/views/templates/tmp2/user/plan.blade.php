@extends(activeTemplate() .'layouts.app')



@section('content')

    <div class="row">
        @foreach($plans as $data)
            <div class="col-xl-4 col-lg-6 col-md-4 col-sm-6">
                <div class="pricingTable">
                    <div class="pricingTable-header">
                        <h3 class="title">@lang($data->name)</h3>
                    </div>
                    <div class="price-value">
                        <span class="currency">{{$general->cur_sym}}</span>
                        <span class="amount">{{$data->price}}</span>
                    </div>
                    <div class="price-body text-center">
                        <ul class="margin-bottom-30">
                            <li>
                                <h4 class="pb-3"> @lang('Direct Referral Bonus') </h4>
                                <h5> {{$general->cur_sym}}{{$data->ref_bonus}} <span
                                            class='sec-color'> @lang('No Limit')</span> </h5>
                               </li>
                            @php $total = 0; @endphp
                            @php $counter = 0; @endphp
                            @foreach($data->plan_level as $key => $lv)
                                    @if($key+1 <= $general->matrix_height)
                                        <li>
                                            <strong>  @lang('L'.$lv->level.' ')
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

                    <div class="pricingTable-signup">
                        <a href="#confBuyModal{{$data->id}}" data-toggle="modal">@lang('Subscribe Now')</a>
                    </div>
                </div>
            </div>

                    <div class="modal fade" id="confBuyModal{{$data->id}}" tabindex="-1" role="dialog"
                 aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="myModalLabel"> @lang('Confirm Purchase '.$data->name)?</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                        aria-hidden="true">×</span></button>
                        </div>
                        {{--<div class="modal-body">
                            <h5 class="text-danger text-center">{{__($data->price)}} {{$general->cur_text}} @lang('You will subscribe ')</h5>
                        </div>--}}
                        <form method="post" action="{{route('user.plan.purchase')}}">
                            @csrf
                            <div class="modal-footer">
                                <input id="bls-stripe-pm" type="hidden" name="pm" value=""/>
                                <button id="bls-subscribe-plan-button" type="submit" name="plan_id" value="{{$data->id}}"
                                        class="btn btn-primary bold uppercase d-none"><i
                                            class="fa fa-send"></i> @lang('Subscribe')</button>
                                <button type="button" class="btn btn-danger d-none" data-dismiss="modal"><i
                                            class="fa fa-times"></i> @lang('Close')</button>
                            </div>
                        </form>
                        <div class="col-12 border mt-3 p-2"> 
                            <input id="cardholder-name" type="text" class="form-control" placeholder="Full Name">
                            <!-- placeholder for Elements -->
                            <form id="setup-form" >
                                <div class="p-3"></div>
                                <div id="card-element" class="m-2"></div>
                                <div style="height: 15px;"></div>
                                <button id="card-button" data-secret="{{$clientSecret}}" class="btn btn-primary bold uppercase">@lang('Subscribe')</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        @endforeach
    </div>
    <input type="hidden" id="bls-stripe-pk" value="{{$publishable_key}}">

@endsection


@push('style')

@endpush

@push('script')
    
    <script>
        $(function(){
  
            var stripe = Stripe($('#bls-stripe-pk').val());
  
              var elements = stripe.elements();
              var cardElement = elements.create('card', {hidePostalCode: true});
              cardElement.mount('#card-element');
              function closeModal() {
                  overlay.style.display='none';
              }
  
              var cardholderName = document.getElementById('cardholder-name');
              var cardButton = document.getElementById('card-button');
              var clientSecret = cardButton.dataset.secret;
  
            cardButton.addEventListener('click', function(ev) {
                ev.preventDefault();
                stripe.confirmCardSetup(
                    clientSecret,
                    {
                    payment_method: {
                    card: cardElement,
                    billing_details: {
                    name: cardholderName.value,
                    },
                },
                }
            ).then(function(result) {
            if (result.error) {
                console.log(result.error);
                $.notify('Votre moyen de paiement n\'a pas été accepté.', 'error');
            } else {
                $.notify('Le paiement est en cours.', 'success');
            // 
                $('#bls-stripe-pm').val(result.setupIntent.payment_method) ; 
                setTimeout(function(){
                    $('#bls-subscribe-plan-button').trigger('click');
                }, 2000);
        }   
        })
        
        });

    });
    </script>
@endpush

