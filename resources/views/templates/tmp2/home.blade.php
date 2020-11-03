@extends(activeTemplate() .'layouts.master')

@section('content')
<style>
    #player-first{
        border: solid 2px red;
    }
</style>
<div class="slider-banner">
    <div class="swiper-wrapper">{{--<div class="">--}}
        @foreach ($sliders as $slider)
            <div class="swiper-slide">
            @php 
                $sld = (new \App\Classes\TranslateFrontend())->translate($slider);
            @endphp
            <section class="banner-section gradient-bg-two bg_img"
            data-background="{{ get_image(config('constants.frontend.banner.path') .'/'. $slider->value->image) }}">
            <div class="banner-shape shape01"></div>
            <div class="banner-shape shape02"></div>
            <div class="banner-shape shape03"></div>
            <div class="banner-shape shape04"></div>
            <div class="container">
                <div class="banner-content text-center" style="margin: auto;">
                    <span class="banner-cate">@lang($sld->subtitle)</span>
                    <h1 class="title">@lang($sld->title)</h1>
                    <p>@lang($sld->details)</p>
                    <div class="banner-button justify-content-center">
                        <a href="{{route('user.register')}}" class="custom-button">@lang('Sign Up')</a>
                        <a href="{{route('user.login')}}" class="custom-button white">@lang('Sign In')</a>
                    </div>
                </div>
            </div>
        </section>
    </div>
    @endforeach
</div>
</div>

<section id="how-it-works" class="service-section padding-top padding-bottom bg-f8">
    <div class="container">
        <div class="row justify-content-center mb-30-none">
            <video id="player-first" controlsList="nodownload" controls disablePictureInPicture poster="https://businesslifesport.com/assets/images/logo.png" class="video-js" data-setup="{}" preload="auto" style="height: auto !important; position: relative !important;">
                <source src="/assets/video/video-1597522475.mp4" type="video/mp4" />
            </video>
            
        </div>
    </div>
</section>

<section id="plan" class="pricing-section padding-bottom padding-top">
    <div class="container">
        <div class="section-header">
            <div class="left-side">
                <h2 class="title">@lang($plan_title->value->title)</h2>
            </div>
            <div class="right-side">
                <p>@lang($plan_title->value->subtitle)</p>
            </div>
        </div>
        <div class="pricing-wrapper">
            <div class="row justify-content-center mb-30-none">
                @foreach($plans as $data)
                    <div class="col-md-6 col-sm-10 col-lg-4">
                    <div class="ticket-item-three">
                        <div class="ticket-header">
                            <i class="flaticon-pig"></i>
                            <h5 class="subtitle">@lang($data->name)</h5>
                            <h2 class="title">{{__($data->price)}} {{$general->cur_text}}</h2>
                            <span class="text-white mt-2">@lang('by month')</span>
                        </div>
                        <div class="ticket-body">
                            <ul class="ticket-info">
                                {{--<li>
                                 <h5 class="pt-2 choto"> @lang('Direct Referral Bonus') :  {{$general->cur_sym}}{{$data->ref_bonus}} </h5>
                                </li>--}}
                                <li>
                                 <h5 class="pt-2 choto"> @lang('Exclusively : our advices and pronostics')</h5>
                                </li>
                                <li>
                                 <h5 class="pt-2 choto"> @lang('Create your network')</h5>
                                </li>
                                <li>
                                 <h5 class="pt-2 choto"> @lang('Earn money without betting')</h5>
                                </li>
                                <li>
                                 <h5 class="pt-2 choto"> @lang('Lost money? You redo thanks to the affiliation')</h5>
                                </li>
                                <li>
                                 <h5 class="pt-2 choto"> @lang('Join a family team')</h5>
                                </li>
                                <li>
                                 <h5 class="pt-2 choto"> @lang('24 hour support')</h5>
                                </li>
                                @php $total = 0; @endphp
                                @foreach($data->plan_level as $key => $lv)
                                @if($key+1 <= $general->matrix_height)
                                {{--<li class="level-com">
                                    <strong>  @lang('L'.$lv->level.'')
                                        : {{$general->cur_sym}}{{$lv->amount}}
                                        X {{pow($general->matrix_width,$key+1)}}  <i class="fa fa-users"></i>
                                        =<span class="sec-colorsss"> {{$general->cur_sym}}{{$lv->amount*pow($general->matrix_width,$key+1)}}</span></strong>
                                    </li>--}}
                                    @php $total += $lv->amount*pow($general->matrix_width,$key+1); @endphp
                                    @endif
                                    @endforeach
                                    {{--<li class="bgcolor">
                                        <h6 class="pt-2 pb-3 choto"> @lang('Total Level Commission') : {{$total}} {{$general->cur_text}}</h6>
                                    
                                        @php
                                        $per = intval($total/$data->price*100);
                                        @endphp

                                        <strong style="font-size: 14px;">@lang('Returns')  <span class="sec-color">{{$per}}%</span> @lang('of Invest')</strong>
                                    </li>--}}
                                </ul>
                                <div class="t-b-group d-flex justify-content-center">
                                    <a href="{{route('user.plan.index')}}"
                                    class="custom-button transparent">@lang('Subscribe Now')</a>
                                </div>

                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

<section id="about" class="about-section padding-bottom padding-top bg_img"
data-background="./assets/images/shape/shape01.png"
data-paroller-factor=".5" data-paroller-type="background" data-paroller-direction="vertical">
    @php 
        $ab = (new \App\Classes\TranslateFrontend())->translate($about);
    @endphp
<div class="container">
    <div class="about-wrapper">
        <div class="about-thumb">
            <div class="c-thumb">
                <img src="{{ get_image(config('constants.frontend.about.title.path') .'/'. $about->value->image) }}" alt="about">
            </div>
        </div>
        <div class="about-content">
            <div class="section-header left-style mw-620">
                <div class="left-side">
                    <h2 class="title">@lang($ab->title)</h2>
                </div>
                <div class="right-side">
                    <p>@php echo $ab->detail; @endphp</p>
                </div>
            </div>

        </div>
    </div>
</div>
</section>

<section id="how-it-works" class="service-section padding-top padding-bottom bg-f8">
    <div class="container">
        <div class="section-header">
            <div class="left-side">{{-- @lang($how_it_work_title->value->title) --}}
                <h2 class="title">@lang('Let\'s earn more together')</h2>
            </div> 
            <div class="right-side">
                <p>@lang($how_it_work_title->value->subtitle)</p>
            </div>
        </div>
        <div class="row justify-content-center mb-30-none">
            @foreach($how_it_work as $data)
            <div class="col-lg-4 col-md-6 col-sm-10">
                <div class="service-item wow slideInUp">
                    <div class="service-thumb">
                        @php echo $data->value->icon; @endphp
                    </div>
                    <div class="service-content">
                        <h6 class="title">@lang($data->value->title)</h6>
                        <p>@lang($data->value->sub_title)</p>
                    </div>
                </div>
            </div>
            @endforeach

        </div>
    </div>
</section>

    <section class="service-section padding-top padding-bottom bg-f8">
        <div class="container">
            <div class="section-header">
                <div class="left-side">
                    <h2 class="title">@lang('Why Choose Us')</h2>
                </div>
                <div class="right-side">
                    <p>@lang($service_titles->value->subtitle)</p>
                </div>
            </div>
            <div class="row justify-content-center mb-30-none">
                @foreach($service as $data)
                <div class="col-lg-4 col-md-6 col-sm-10">
                    <div class="service-item wow slideInUp">
                        <div class="service-thumb">
                            @php echo $data->value->icon; @endphp
                        </div>
                        <div class="service-content">
                            <h6 class="title">@lang($data->value->title)</h6>
                            <p>@lang($data->value->sub_title)</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
 <section class="testimonial-section padding-bottom padding-top ">
        <div class="container">
            <div class="section-header">
                <div class="left-side">
                    <h2 class="title">@lang($testimonial_title->value->title)</h2>
                </div>
                <div class="right-side">
                    <p>@lang($testimonial_title->value->subtitle) </p>
                </div>
            </div>
            <div class="client-slider-area-wrapper wow slideInUp">
                <div class="client-slider-area">
                    <div class="swiper-wrapper">
                        @foreach($testimonial as $data)
                        @php 
                            $ts = (new \App\Classes\TranslateFrontend())->translate($data);
                        @endphp
                        <div class="swiper-slide">
                            <div class="client-item">
                                <div class="client-quote">
                                    <i class="flaticon-left-quote-sketch"></i>
                                </div>
                                <p>@lang($ts->quote)</p>
                                <div class="client">
                                    <div class="thumb">
                                        <a>
                                            <img src="{{ get_image(config('constants.frontend.testimonial.path') .'/'. $data->value->image) }}"
                                            alt="client">
                                        </a>
                                    </div>
                                    <div class="content">
                                        <h6 class="sub-title">
                                            <a>@lang($ts->author)</a>
                                        </h6>
                                        <span>@lang($ts->designation)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="consulting-section bg-theme">
        <div class="bg_img padding-top padding-bottom" data-paroller-factor=".5" data-paroller-type="background"
        data-paroller-direction="vertical"
        data-background="./assets/images/shape/shape03.png">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="c-thumb consult-thumb">
                        <img src="{{ get_image(config('constants.frontend.vid.post.path') .'/'. $video_section->value->image) }}"
                        alt="consult">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="section-header left-style">
                        <div class="left-side">
                            <h2 class="title">@lang($video_section->value->title)</h2>
                        </div>
                        <div class="right-side">
                            @php 
                                $blTl = (new \App\Classes\TranslateFrontend())->translate($video_section);
                            @endphp
                            <p>@lang($blTl->detail)</p>
                        </div>
                    </div>
                    <div class="video-group">
                        <a href="/assets/video/video-1597522475.mp4" data-rel="lightcase:myCollection"
                         class="video-button">
                         <i class="flaticon-play-button-1"></i>
                     </a>
                 </div>
             </div>
         </div>
     </div>
 </div>
</section>

<section class="blog-section padding-bottom padding-top">
    <div class="container">
        @php 
            $blTl = (new \App\Classes\TranslateFrontend())->translate($blog_title);
        @endphp
        <div class="section-header">
            <div class="left-side">
                <h2 class="title">{{__($blTl->title)}}</h2>
            </div>
            <div class="right-side">
                <p>{{__($blTl->subtitle)}}</p>
            </div>
        </div>
        <div class="row justify-content-center mb-30-none">
            @foreach($blogs as $blog)
            <div class="col-md-4 col-xl-4 ">
                @php 
                    $bl = (new \App\Classes\TranslateFrontend())->translate($blog);
                @endphp
                <div class="post-item  wow slideInUp">
                    <div class="post-thumb c-thumb">
                        <a href="{{ route('singleBlog', [slug($bl->title) , $blog->id]) }}">
                            <img src="{{ get_image(config('constants.frontend.blog.post.path') .'/'. $blog->value->image) }}"
                            alt="blog">
                        </a>
                    </div>
                    <div class="post-content">
                        <div class="blog-header">
                            <h6 class="title">
                                <a href="{{ route('singleBlog', [slug($bl->title) , $blog->id]) }}">{{__($bl->title)}}</a>
                            </h6>
                        </div>
                        <div class="meta-post">
                            <div class="date">
                                <a>
                                    <i class="flaticon-calendar"></i>
                                    {{\Carbon\Carbon::parse($blog->created_at)->diffForHumans()}}
                                </a>
                            </div>
                        </div>
                        <div class="entry-content">
                            <p>{{ \Illuminate\Support\Str::limit(strip_tags($bl->body), 160, '...') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

@endsection
