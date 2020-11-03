<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="ga-site-verification" content="LbMvlsO8jVCmMFmqSqA_Pg7J" />

    <link rel="shortcut icon" href="{{get_image(config('constants.logoIcon.path') .'/favicon.png')}}"
          type="image/x-icon">

    <title>{{ $general->sitename(__($page_title)) }} </title>


    <link rel="stylesheet" href="{{asset(activeTemplate(true) .'front/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset(activeTemplate(true) .'front/css/all.min.css')}}">
    <link rel="stylesheet" href="{{asset(activeTemplate(true) .'front/css/animate.css')}}">
    <link rel="stylesheet" href="{{asset(activeTemplate(true) .'front/css/flaticon.css')}}">
    <link rel="stylesheet" href="{{asset(activeTemplate(true) .'front/css/lightcase.css')}}">
    <link rel="stylesheet" href="{{asset(activeTemplate(true) .'front/css/odometer.css')}}">
    <link rel="stylesheet" href="{{asset(activeTemplate(true) .'front/css/swiper.min.css')}}">
    <link rel="stylesheet" href="{{asset(activeTemplate(true) .'front/css/nice-select.css')}}">


    <link rel="stylesheet" href="{{asset(activeTemplate(true) .'front/css/iziToast.css')}}">
    <link rel="stylesheet" href="{{asset(activeTemplate(true) .'front/css/main.css')}}">
    <link href="https://vjs.zencdn.net/7.8.4/video-js.css" rel="stylesheet" />
    <link
  href="https://unpkg.com/@videojs/themes@1/dist/city/index.css"
  rel="stylesheet"
/>
    <style>
        @media (max-width: 991px){
            .menu li a {
                padding: 1px 15px !important;
            }
            .menu{
                min-height: 340px !important;
            }
        }

        #bls-btn-whatsapp{
            position: fixed;
            float: right;
            bottom : 20px;
            left: 15px;
            z-index: 200;
        }
    </style>
  

    @yield('style')

    @include('partials.seo')

    @yield('css')

    @php echo $general->chat_script; @endphp

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css"/>

    <link rel="stylesheet"
          href='{{ asset(activeTemplate(true) . "front/css/color.php?color=$general->bclr&color2=$general->sclr")}}'>
    <!-- Global site tag (gtag.js) - Google Analytics -->

    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-176089769-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-176089769-1');
</script>
<!--Start of Tawk.to Script-->
<script type="text/javascript">
    var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
    (function(){
    var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
    s1.async=true;
    s1.src='https://embed.tawk.to/5f427a1ccc6a6a5947ae1173/default';
    s1.charset='UTF-8';
    s1.setAttribute('crossorigin','*');
    s0.parentNode.insertBefore(s1,s0);
    })();
    </script>
    <!--End of Tawk.to Script-->
    
</head>


<body>

<div class="preloader">
    <div class="preloader-inner">
        <div class="preloader-icon">
            <span></span>
            <span></span>
        </div>
    </div>
</div>

<div class="overlay"></div>
<a href="#0" class="scrollToTop">
    <i class="fas fa-angle-up"></i>
</a>

<header>
    <div class="header-section">
        <div class="container">
            <div class="header-area">
                <div class="logo">
                    <a href="{{url('/')}}"><img src="{{get_image(config('constants.logoIcon.path') .'/logo.png')}}"
                                                alt="logo"></a>
                </div>
                <ul class="menu">

                    <li>
                        <a href="{{url('/')}}">@lang('Home')</a>
                    </li>

                    <li><a @if(request()->path() == '/') href="#about"
                           @else href="{{url('/')}}#about" @endif>@lang('About')</a></li>

                    <li><a @if(request()->path() == '/') href="#plan"
                           @else href="{{url('/')}}#plan" @endif>@lang('Plan')</a></li>
                           
                    @if(auth()->user())
                        @if(auth()->user()->plan_id != 0)
                            <li>
                                <a href="{{route('user.pronostics')}}">@lang('Pronostics')</a>
                            </li>
                        @else
                            <li>
                                <a href="{{ route('user.plan.index') }}">@lang('Pronostics')</a>
                            </li>
                        @endif
                    @else
                        <li>
                            <a href="{{ route('user.plan.index') }}">@lang('Pronostics')</a>
                        </li>
                    @endif
				   <li>
                        <a href="{{route('faq')}}">@lang('Faq')</a>
                    </li>
					
					<li>
                        <a href="{{route('blog')}}">@lang('News')</a>
                    </li>

                    <li>
                        <a href="{{route('contact')}}">@lang('Contact')</a>
                    </li>
					
					    <select id="langSel" class="select-bar">
                        <option style="color: black" value="en">@lang('English')</option>
						@foreach($lang as $data)
                            <option value="{{strtolower($data->code)}}"
                                    @if(Session::get('lang') == strtolower($data->code)) selected="selected"
                                    @endif style="color: black"> {{$data->name}}
                            </option>
                        @endforeach
                    </select>
                    
                    @if(Auth::user())
                        <li>
                            <a href="{{route('user.home')}}" class="header-button custom-button white">@lang('My Business')</a>
                        </li>
                    @else
                        <li>
                            <a href="{{route('user.login')}}" class="header-button custom-button white">@lang('Sign In')</a>
                        </li>
                    @endif
                </ul>
                <div class="header-bar d-lg-none">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <form class="search-form">
                    <div class="form-group m-0">
                        <input type="text" placeholder="Search Here">
                        <button type="submit">
                            <i class="flaticon-magnifying-glass"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="header-fix w-100"></div>

</header>



    @yield('content')


<div id="bls-btn-whatsapp">
    <a href="https://api.whatsapp.com/send?phone=33613639723">
    <img width="60px" src="/assets/images/1200px-WhatsApp_logo-color-vertical.svg.png"/></a>
</div>
<footer class="dark-bg bg_img" data-paroller-factor="0.5" data-paroller-type="background"
        data-paroller-direction="vertical" data-background="./assets/images/shape/shape04.png">
    <div class="footer-top padding-top padding-bottom">
        <div class="container">
            <div class="row mb-50-none justify-content-center">
                <div class="col-sm-6 col-lg-8 ">
                    <div class="footer-widget widget-about ">
                        <div class="logo">
                            <a href="{{url('/')}}"><img
                                        src="{{get_image(config('constants.logoIcon.path') .'/logo.png')}}" alt="logo">
                            </a>
                        </div>
                        <div class="content text-center">
                            <ul class="social-icons-area">
                                @foreach($social as $item)<li>
                                            <a href="{{$item->value->url}}"
                                               title="{{$item->value->title}}">@php echo $item->value->icon; @endphp</a></li>
                                @endforeach								
                        </div>	
<div class="footer-top padding-top padding-bottom">
        <div class="container">
            <div class="row mb-50-none justify-content-center">
                <div class="col-sm-6 col-lg-8 ">
                    <div class="footer-widget widget-about ">
                        <div class="content text-center">
							<a href="{{route('mentions legales', 65)}}">@lang ('Legal Mentions')</a> -
							<a href="{{route('mentions legales', 66)}}">@lang ('Terms and Conditions')</a> -
							<a href="{{route('mentions legales', 67)}}">@lang ('Privacy Policy')</a>
						</div>							
					</ul>
                        </div>						
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom text-center">
        <div class="container">
            <p class="m-0"> {{__($footer->title)}}</p>
        </div>
    </div>
    <div class="right banner-shape shape04"></div>
</footer>


<script src="{{asset(activeTemplate(true) .'front/js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset(activeTemplate(true) .'front/js/modernizr-3.6.0.min.js')}}"></script>
<script src="{{asset(activeTemplate(true) .'front/js/plugins.js')}}"></script>
<script src="{{asset(activeTemplate(true) .'front/js/bootstrap.min.js')}}"></script>
<script src="{{asset(activeTemplate(true) .'front/js/isotope.pkgd.min.js')}}"></script>
<script src="{{asset(activeTemplate(true) .'front/js/lightcase.js')}}"></script>
<script src="{{asset(activeTemplate(true) .'front/js/swiper.min.js')}}"></script>
<script src="{{asset(activeTemplate(true) .'front/js/wow.min.js')}}"></script>
<script src="{{asset(activeTemplate(true) .'front/js/odometer.min.js')}}"></script>
<script src="{{asset(activeTemplate(true) .'front/js/viewport.jquery.js')}}"></script>
<script src="{{asset(activeTemplate(true) .'front/js/nice-select.js')}}"></script>
<script src="{{asset(activeTemplate(true) .'front/js/paroller.js')}}"></script>
<script src="{{asset(activeTemplate(true) .'front/js/main.js')}}"></script>


<script src="{{asset(activeTemplate(true) .'front/js/iziToast.min.js')}}"></script>


<script src="{{asset(activeTemplate(true) .'front/vue/vue-handle-error.js')}}"></script>
<script src="{{asset(activeTemplate(true) .'front/vue/vue.js')}}"></script>
<script src="{{asset(activeTemplate(true) .'front/vue/axios.js')}}"></script>
<!-- If you'd like to support IE8 (for Video.js versions prior to v7) -->
<script src="https://vjs.zencdn.net/7.8.4/video.js"></script>
@include('partials.notify')

@yield('js')

@stack('js')

@yield('script')

<script>
   $(function(){
      $(document).on('change', '#langSel', function () {
        var code = $(this).val();
        window.location.href = "{{url('/')}}/change-lang/" + code;
        console.log($('#player-first').length); 
      });
      var width = $('.video-js').width();
      var height = $('.video-js').height(); 
      var left = (((width-35)/2)*100)/width;
      var top = (((height-20)/2)*100)/height;
      if($(window).width() < 700 && $(window).width() > 400){
          left = (((width-45)/2)*100)/width;
      }

      if($(window).width() <= 400){
          left = (((width-60)/2)*100)/width;
          top = (((height-50)/2)*100)/height;
      }
      
      $('.vjs-big-play-button').css({'left': left+'%', 'top': top+'%', 'background-color': 'rgba(43, 51, 63, 0)', 'border': '0.06666em solid red'});
      $('.vjs-big-play-button').css({'color': 'red'});//vjs-control-text
   });
</script>


</body>
</html>


		