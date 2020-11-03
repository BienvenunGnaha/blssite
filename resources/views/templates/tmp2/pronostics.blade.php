@extends(activeTemplate() .'layouts.master')

@section('style')
   <style>
    .bd-top-warning{
        border-top: 10px solid rgb(44,44,134);
    }

    .bd-right-primary{
        border-right: 10px solid rgb(44,44,134);
    }

    .bd-warning{
        border: 3px solid rgb(44,44,134);
        
    }

    .bls-bg-blue-logo{
        background: rgb(44,44,134);
    }

   </style>
    
@endsection

@section('content')

@include(activeTemplate() .'layouts.breadcrumb')


<div class="row justify-content-center bg-light pt-3 pb-3 pr-1 pl-1">
    <div class="col-12 bg-white shadow-lg">
        <div class="row pt-3">
            <div class="col-lg-8 bd-right-primary">
                <div class="row justify-content-center"><span class="h2 col-11" style="padding-left: 0px !important;">@lang('THE LATEST FORECASTS')</span></div>
                <div class="row justify-content-center">
                    @php 
                        $fe = \App\Frontend::find(68);
                    @endphp
                    @foreach ($pronostics as $prono)
                        @php
                            $details = json_decode($prono->details, true);
                            $count = count($details);
                        @endphp
                        
                        <div class="mb-3 col-11 bls-prono bg-white" data-id="{{$prono->id}}">
                            <div class="row">
                                @if ($count > 1)
                                    <div class="col-md-3 col-6 bls-bg-blue-logo  text-white font-weight-bold text-center" style="margin-bottom: 8px;">
                                        @lang('Combined')
                                    </div>
                                @else
                                    <div class="col-md-3 col-6 bls-bg-blue-logo text-white font-weight-bold text-center" style="margin-bottom: 8px;">
                                        @lang('Simple')
                                    </div>
                                @endif
                                
                                {{--<div class="col-md-9 col-sm-12 bg-light">
                                    <div class="row">
                                        @if (session()->get('lang') == 'fr') 
                                            <div class="col-4 d-flex justify-content-start">
                                                <span class="bd-warning p-1">{{$prono->button_fr}}</span>
                                            </div>
                                        @else
                                            <div class="col-4 d-flex justify-content-start">
                                                <span class="bd-warning p-1">{{$prono->button}}</span>
                                            </div>
                                        @endif
                                        
                                        <div class="col-4 d-flex justify-content-start">
                                            <span class="bd-warning p-1">@lang('Resume')</span>
                                        </div>
                                        
                                        <div class="col-4 d-flex justify-content-end">
                                            <span>@lang('TOTAL RATING')</span>
                                        </div>
                                    </div>
                                </div>--}}
                            </div>
                            <div class="row">
                                <div class="col-12 bls-prono-conseil bg-light d-none"  data-id="{{$prono->id}}">
                                    @if (session()->get('lang') == 'fr')
                                        <span class="">{{$prono->conseil_fr}}</span>
                                    @else
                                        <span class="">{{$prono->conseil}}</span>
                                    @endif
                                </div>
                            </div>
                            
                            @foreach ($details as $detail)
                                @php
                                    $sp = \App\Sport::find((int)$detail['sport']);
                                    $cp = isset($detail['comp']) ? $detail['comp'] : '';
                                    $cp_fr = isset($detail['comp_fr'])  ? $detail['comp_fr'] : '';
                                    $comp = session()->get('lang') == 'fr' ? $cp_fr : $cp;
                                    $t1_icon = isset($detail['team1_icon']) ? $detail['team1_icon'] : null;
                                    $t2_icon = isset($detail['team2_icon']) ? $detail['team2_icon'] : null;
                                    $ourProno = session()->get('lang') == 'fr' ? 'Notre pronos' : 'Our prognosis'; 
                                    $p_match = isset($detail['comp']) ? $detail['prono_match'] : '';
                                    $p_match_fr = isset($detail['comp_fr'])  ? $detail['prono_match_fr'] : '';
                                    $prono_match = session()->get('lang') == 'fr' ? $p_match_fr : $p_match;
                                    $bm = isset($detail['bookmaker']) ? \App\Bookmaker::find((int)$detail['bookmaker']) : null;
                                @endphp
                                
                                <div class="row mb-2 border-bottom" style="padding-left: 0px !important; padding-bottom: 10px;">
                                    <div class="col-md-5 col-12"  style="padding-left: 0px !important;">
                                        
                                        <div>
                                            <span class="text-secondary d-lg-block d-sm-block d-none h6">{{str_replace('T', ' ',$detail['date'])}}</span> 
                                            <span class="text-secondary d-lg-none d-md-none d-block h6">{{str_replace('T', ' ',$detail['date'])}}<span class="text-secondary "> - {{$comp}}</span></span> 
                                            <span>{{$detail['team1']}} - {{$detail['team2']}}</span> 
                                            <span class="float-right"  style="margin-right: 10px;">
                                                @if ($t1_icon)
                                                    <img width="30px" src="{{asset($t1_icon)}}">  
                                                @endif
                                                @if ($t2_icon)
                                                    <img width="30px" src="{{asset($t2_icon)}}" style="margin-left: -10px;">   
                                                @endif
                                            </span>
                                        </div>
                                        <div class="d-lg-block d-md-block d-none">
                                            <span class="text-secondary">{{$sp->name}} - {{$comp}}</span> 
                                        </div>
                                    </div>
                                    <div class="col-md-7 col-12"  style="padding-left: 0px !important;">
                                        <div class="row">
                                            <div class="col-md-4 col-9">
                                                <div class="d-block font-weight-bold">{{$ourProno}}</div>
                                                <span class="d-block ">{{$prono_match}}</span>
                                            </div>
                                            <div class="col-md-2 col-3">
                                                <div class="col-2 d-block font-weight-bold pl-0">Cote</div>
                                                <span class="d-block">{{$detail['cote']}}</span>
                                            </div>
                                            <div class="col-md-6 d-lg-inline-block d-md-inline-block d-none">
                                                <div class="d-block font-weight-bold"><span>@lang('Bookmaker advised')</span></div> 
                                                @if ($bm)
                                                    <img class="d-block" width="auto" height="40px" src="{{asset(config('constants.bookmaker.path').'/'.$bm->photo)}}">  
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
                <div class="row">
                    <div class="col-12 d-flex justify-content-center mb-2">   
                        <nav aria-label="...">
                            {{ $pronostics->links() }}
                        </nav>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="row justify-content-center"><span class="h5">@lang('Sports News')</span></div>
                    <div class="row">
                        
                        @foreach ($flux as $it) 
                           
                            <div class="mb-3 mr-4 ml-2 col-12 bls-testimonials rounded bg-light">
                                <div class="row">
                                    <div class="col-12 pb-2 pt-2">
                                        <div class="row">
                                            <div class="col-3 d-flex justify-content-start">
                                                <span class="badge-danger p-1">{{ date('H:i', strtotime($it->pubDate)) }}</span>
                                            </div>
                                            {{--<div class="col-9 d-flex justify-content-end">
                                                <span class="name mb-0 badge-danger">{{ date('H:i', strtotime($it->pubDate))}}</span>
                                            </div>--}}
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="row p-2">
                                            <div class="col-12"><span class="font-weight-bold">
                                                <a class="text-secondary" href="{{ $it->link }}">{{ $it->title }}</a>
                                                {{--<div>
                                                    {{explode("\"",(string)$it->description)[count(explode("\"",(string)$it->description))-2]}}
                                                </div> --}} 
                                            </div>

                                            <div class="col-12"><span class="font-weight-bold">
                                                <a class="text-danger" href="{{ $it->link }}">{{ Illuminate\Support\Str::limit($it->link, 30, '...') }}</a>
                                            </div>
                                        </div>
                                    </div>
                                    {{--<div class="col-3">
                                        <div class="row p-1">
                                            <img style="width: 100%; height: auto;" src="{{explode("\"",(string)$it->description)[1]}}" alt="image">
                                        </div>
                                    </div>--}}
                                </div>
                            </div>
                        @endforeach
                        {{--@foreach ($testimonials as $testimonial)
                           
                            @php 
                                $ts = (new \App\Classes\TranslateFrontend())->translate($testimonial);
                            @endphp
                            <div class="mb-3 col-12 bg-white shadow-lg bls-testimonials" data-id="{{$testimonial->id}}">
                                <div class="row">
                                    <div class="col-3">
                                        <div class="row p-1">
                                            <img class="avatar avatar-sm rounded-circle mr-3 img-fluid" src="{{ get_image(config('constants.frontend.testimonial.path') .'/'. $testimonial->value->image) }}" alt="image">
                                            <div class="media-body col-12">
                                                
                                                <span class="name mb-0">{{ $ts->author }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-9">
                                        <div class="row p-2">
                                            <div class="col-12"><span class="font-weight-bold">{{ $ts->quote }}</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach--}}
                        <div class="col-12 d-flex justify-content-center mb-2">   
                            <nav aria-label="...">
                                <ul class="pagination">
                                        @php
                                            $l = $current_page-1;
                                            $k = $current_page+1;
                                        @endphp
                                        @if ($current_page < $page_number-1 && $current_page > 1)
                                            
                                            <li class="page-item"><a class="page-link" href="{{'?page='.$l}}"><<</a></li>
                                            <li class="page-item"><a class="page-link" href="{{'?page='.$k}}">>></a></li>
                                        @elseif($current_page == $page_number-1) 
                                            <li class="page-item"><a class="page-link" href="{{'?page='.$l}}"><<</a></li>
                                            <li class="page-item"><a class="page-link" href="#">>></a></li>
                                        @elseif($current_page == 1) 
                                            <li class="page-item"><a class="page-link" href="#"><<</a></li>
                                            <li class="page-item"><a class="page-link" href="{{'?page='.$k}}">>></a></li>
                                        @endif
                                </ul>
            
                                    
                    
                    
                                    
                        
                        
                                                                                                
                                                                                      
                            </nav>
                        </div>
                    </div>
                    {{--<div class="row justify-content-center">
                        <div class="col-6">
                            @php
                                $isFirst = true;
                            @endphp
                            @foreach ($testimonials as $testimonial)
                                @if ($isFirst)
                                    <i class="fas fa-circle bls-show-testimonial" data-id="{{$testimonial->id}}"></i>
                                    @php
                                        $isFirst = false;
                                    @endphp
                                @else 
                                    <i class="far fa-circle bls-show-testimonial" data-id="{{$testimonial->id}}"></i>
                                @endif
                            @endforeach
                        </div>
                    </div>--}}
                </div>
            </div>
        </div>
    </div>
    
    {{--
            <div class="card-footer py-4">
                <nav aria-label="...">
                    {{ $testimonials->links() }}
                </nav>
            </div>
        <div class="col-12 bg-dark shadow-lg pt-3">
        <div class="row">
            <div class="col-6 mt-3 bg-secondary" style="padding-right: 10%">
                <div class="row">
                    <div class="col-6 mt-3"><span class="h-5 text-warning">PRONO VIP DU JOUR</span></div>
                    <div class="col-6 d-flex justify-content-end">
                        <a href="#">DEVENIR VIP</a>
                    </div>
                </div>
            </div>
            <div class="col-6 bg-warning" style='height: 40px;'>

            </div>
        </div>
    </div>--}}
</div>

@endsection

@section('script')

    <script>
        $(function(){
            /*var ts = $('.bls-testimonials');
            var tsFirst = $('.bls-testimonials:first');
            ts.hide();
            tsFirst.show();

            $('.bls-show-testimonial').click(function(){
                var id = $(this).attr('data-id');
                
                ts.fadeOut();
                $('.bls-testimonials[data-id='+id+']').fadeIn();
                $('.bls-show-testimonial').removeClass('fas');
                
                $('.bls-show-testimonial').toggleClass('far');
                $(this).addClass('fas');
                $(this).removeClass('far');
            });*/

            $('.bls-prono').click(function(e) {
                /*$('.bls-prono').removeClass('bd-warning');
                $(this).addClass('bd-warning');
                var prono = $('.bls-prono-conseil');
                console.log()
                var len = prono.length;
                for(var i =0; i < len; i++){
                    if(!$(prono[i]).hasClass('d-none') && $(prono[i]).attr('data-id') != $(this).attr('data-id')){
                        $(prono[i]).addClass('d-none');
                    }
                    
                }*/
                $(this).find('.bls-prono-conseil').toggleClass('d-none');
            });

            
        });
    </script>

@endsection