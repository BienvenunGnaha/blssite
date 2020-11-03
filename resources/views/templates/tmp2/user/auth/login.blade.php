@extends(activeTemplate().'layouts.user-master')

@section('panel')

    <div class="signin-section pt-5" >
        <div class="container-fluid">
            <div class="row justify-content-center align-items-center">
                <div class="col-xl-4 col-md-6 col-sm-8">
                    <div class="login-area">
                        <div class="login-header-wrapper text-center">
                           <a href="{{url('/')}}"> <img class="logo" src="{{ get_image(config('constants.logoIcon.path') .'/logo.png') }}" alt="image"> </a>
                            <p class="text-center admin-brand-text">@lang('User Sign In')</p>
                        </div>
                        <form action="{{ route('user.login') }}" method="POST" class="login-form" id="recaptchaForm">
                            @csrf
                            <div class="login-inner-block">
                                <div class="frm-grp">
                                    <label>@lang('Username')</label>
                                    <input type="text" name="username" @keyup="checkPassword"  value="{{ old('username') }}" placeholder="@lang('Enter your username')">
                                </div>
                                <div class="frm-grp">
                                    <label>@lang('Password')</label>
                                    <input type="password" name="password" @keyup="checkPassword" placeholder="@lang('Enter your password')">
                                </div>
                            </div>
                            <div class="d-flex mt-3 justify-content-between">
                                <div class="frm-group">
                                    <input type="checkbox" name="remember" id="checkbox">
                                    <label for="checkbox">@lang('Remember me')</label>
                                </div>
                            </div>
                            <div class="btn-area text-center">
                                <button type="submit" id="recaptcha" class="submit-btn">@lang('Sign In')</button>
                            </div>
                            <br>

                            <div class="d-flex mt-3 justify-content-between">
                                <a href="{{ route('user.password.request') }}" class="forget-pass">@lang('Forget password?')</a>
                                <a href="{{route('user.register')}}" class="forget-pass">@lang('Sign Up')</a>
                            </div>


                        </form>
                        <script src="//code.jquery.com/jquery-3.4.1.min.js"></script>
                        @php echo recaptcha() @endphp

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('style-lib')
    <link rel="stylesheet" href="{{asset(activeTemplate(true) .'users/css/signin.css')}}">
@endpush
