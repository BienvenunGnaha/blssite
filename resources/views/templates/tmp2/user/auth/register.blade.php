@extends(activeTemplate().'layouts.user-master')
@push('style-lib')
    <link rel="stylesheet" href="{{asset(activeTemplate(true) .'build/css/intlTelInput.css')}}">
    <style>
        .intl-tel-input {
            width: 100%;
        }

    </style>
@endpush
@section('panel')

    <div class="signin-section pt-5">
        <div class="container-fluid">
            <div class="row justify-content-center align-items-center">
                <div class="col-md-6 ">
                    <div class="login-area registration-form-area">
                        <div class="login-header-wrapper text-center">
                          <a href="{{url('/')}}" > <img class="logo" src="{{ get_image(config('constants.logoIcon.path') .'/logo.png') }}"
                                 alt="image"> </a>
                            <p class="text-center admin-brand-text">@lang('User Sign Up')</p>
                        </div>
                        @if ($discount !== null)
                            <h6 id="myModalLabel"> @lang('Benefit from a') {{$discount}} @lang('reduction on subscription') ?</h6>
                        @endif
                        <form action="{{ route('user.register') }}" method="POST" class="login-form" id="recaptchaForm">
                            @csrf
                            <div class="login-inner-block">

                                    <div class="form-row">

                                        @isset($ref_user)

                                            <div class="frm-grp form-group col-md-12">

                                                <label>@lang('Referred By')</label>
                                                <input style="background: #b6b9c1" type="text" value="{{$ref_user->username}}"

                                                       disabled readonly required>
                                                <input type="hidden" value="{{$ref_user->id}}" name="ref_id">

                                            </div>
                                            @else
                                            <div class="frm-grp form-group col-md-6">

                                                <label>@lang('Referred By')</label>
                                                <input type="text" value=""
                                                       name="ref_bls" >
                                            </div>
                                                <input type="hidden" value="0" name="ref_id">

                                        @endisset


                                        <div class="frm-grp form-group col-md-6">

                                            <label>@lang('Your first name')</label>
                                            <input type="text" value="{{old('firstname')}}"
                                                   placeholder="@lang('Enter your first name')"
                                                   name="firstname" id="firstname" required>
                                        </div>

                                        <div class="frm-grp form-group col-md-6">

                                            <label>@lang('Your last name')</label>
                                            <input type="text" value="{{old('lastname')}}"
                                                   placeholder="@lang('Enter your last name')"
                                                   name="lastname" id="lastname" required>
                                        </div>


                                        <div class="frm-grp form-group col-md-6">
                                            <label>@lang('Your email')</label>
                                            <input type="text" value="{{old('email')}}"
                                                   placeholder="@lang('Enter your email')"
                                                   name="email" id="email" required>
                                        </div>

                                        <div class="frm-grp form-group col-md-6">
                                            <label>@lang('Your mobile')</label>
                                            <input type="text" value="{{old('mobile')}}"
                                                   placeholder="@lang('Enter your mobile number')"
                                                   name="mobile" id="mobile" required>
                                        </div>
                                        <div class="frm-grp form-group col-md-6">
                                            <label>@lang('Your Country')</label>

                                            <select class="frm-grp" name="country">
                                                @include('partials.country')

                                            </select>
                                        </div>

                                        <div class="frm-grp form-group col-md-6">
                                            <label>@lang('Your Username')</label>
                                            <input type="text" name="username"  id="username"
                                                   value="{{ old('username') }}" placeholder="@lang('Enter your username')">
                                        </div>

                                        <div class="frm-grp form-group col-md-6">
                                            <label>@lang('Password')</label>
                                            <input type="password" name="password" id="password"
                                                   placeholder="@lang('Enter your password')">
                                        </div>
                                        <div class="frm-grp form-group col-md-6">
                                            <label>@lang('Confirm Password')</label>
                                            <input type="password" name="password_confirmation" id="password_confirmation"
                                                   placeholder="@lang('Confirm your password')">
                                        </div>
                                    </div>
                            </div>
                            <div class="d-flex mt-3 justify-content-between">
                                <div class="frm-group">
                                    <input type="checkbox" name="accept_term" id="accept_term" required checked>
                                <label for="accept_term"><span>@lang('You confirm that you have read')</span><a href="{{route('mentions legales', 66)}}"> @lang('the terms and conditions') </a><span>@lang('and accept them')</span></label>
                                </div>
                            </div>

                            <div class="d-flex mt-3 justify-content-between">
                                <div class="frm-group">
                                    <input type="checkbox" name="accept_news" id="accept_news" checked>
                                    <label for="accept_news">@lang('You agree that your details will be transmitted to a third party company.')</label>
                                </div>
                            </div>

                            <div class="btn-area text-center">
                                <button type="submit" id="recaptcha" class="submit-btn d-none">@lang('Sign Up')</button>
                                  <a  id="subs_user" class="submit-btn" href="#confBuyModal" data-toggle="modal" style="display: none;">@lang('Sign Up')</a>
                            </div>
                            <br>

                           <input type="hidden" value="" name="stripe_cus" id="stripe_cus">
                           <input type="hidden" value="" name="pm" id="pm">
                           <input type="hidden" id="plan_id" name="plan_id" value="{{$plan ? $plan->id : ''}}">


                            <div class="d-flex mt-3 justify-content-between">
                                <a href="{{ route('user.password.request') }}" class="forget-pass">@lang('Forget password?')</a>
                                <a href="{{route('user.login')}}"
                                   class="forget-pass">@lang('Sign In')</a>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="confBuyModal" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel"> @lang('Confirm Purchase '.$plan->name)?</h4>
                    @if ($discount !== null)
                        <h6 class="modal-title" id="myModalLabel"> @lang('Benefit from a') {{$discount}} @lang('reduction on subscription') ?</h6>
                    @endif
                    
                </div>
                <div id="modal-pay-body" class="modal-body">

                </div>
                    
            </div>
        </div>
    </div>

    @if (!session('pdf_popup'))
    <a id="bls-popup-btn" class="submit-btn" href="#popNewsletterPopup" data-toggle="modal" style="display: none;"></a>
    @endif

    <div class="modal fade" id="popNewsletterPopup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content p-3">
                <form action="{{route('send.mail.guide')}}" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="form-group">
                        <label for="" class="form-control-label text-center mt-2 mb-2">@lang("You don't want to register right away? Looking for more information?") @lang('Enter your email for an overview of the prediction sites.')</label>
                        <input type="text" name="email" id="" class="form-control" placeholder="@lang('Enter your email')">
                        <button type="submit" class="btn mt-2" style="background-color: #081636 !important; color: white !important;">@lang('I want my guide')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('style-lib')
    <link rel="stylesheet" href="{{asset(activeTemplate(true) .'users/css/signin.css')}}">
    <style>
        .registration-form-area .frm-grp+.frm-grp {
            margin-top: 0;
        }
        .registration-form-area .frm-grp label {
            color: #98a6ad!important;
            font-weight: 400;
        }
        .registration-form-area select {
            border: 1px solid #5220c5;
            width: 100%;
            padding: 12px 20px;
            color: #ffffff;;
            z-index: 9;
            background-color: #3c139c;
            border-radius: 3px;
        }
        .registration-form-area select option {
            color: #ffffff;
        }
    </style>
@endpush

@push('script')
    
    <script>
        $(function(){
          
          $('#recaptchaForm').find('input').keyup(function(){
             if($('#lastname').val() !== '' && $('#firstname').val() !== '' && $('#email').val() !== '' && $('#mobile').val() !== '' && $('#password').val() !== '' && $('#pasword_confirmation').val() !== '' && $('#accept_term').val() !== ''){
                $('#subs_user').show();
             }else{
                $('#subs_user').hide();  
             }

          });

          $('#subs_user').click(function(){
              $.ajax({
                 url: '/subscription/plan',
                 type: 'post',
                 dataType: 'json',
                 contentType: 'application/json',
                 success: function (data) {
                  $('#modal-pay-body').children().remove();
                     $('#stripe_cus').val(data.cus);
                  $('#modal-pay-body').html(data.html);
                 },
                data: JSON.stringify({
                     _token: "{{ csrf_token() }}",
                     lastname: $('#lastname').val(),
                     firstname: $('#firstname').val(),
                     email: $('#email').val(),
                  }),
                error: function (err) {
                  $('#target').html(data.msg);
                 },
              });
          });

      });

      $('#bls-popup-btn').trigger('click');
    </script>
@endpush

@section('js')


@stop
			