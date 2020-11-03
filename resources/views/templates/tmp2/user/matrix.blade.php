@extends(activeTemplate() .'layouts.app')

@section('style')
    
@stop
@section('content')
    <style>
        .button-red-orange{
            background: -webkit-linear-gradient(177deg, #cc332e 0%, #361a00e1 60%);
            -webkit-transition: all ease 0.3s;
            -moz-transition: all ease 0.3s;
            transition: all ease 0.3s;
            -webkit-border-radius: 3px;
        }

        .button-orange-green{
            background: -webkit-linear-gradient(177deg, #08a13be1 0%,#361a00e1  60%);
            -webkit-transition: all ease 0.3s;
            -moz-transition: all ease 0.3s;
            transition: all ease 0.3s;
            -webkit-border-radius: 3px;
        }

        .bls-level-w{
            width: 20% !important;
        }

        @media screen and (min-width: 400px) and (max-width: 700px) {
            .bls-level-w{
                width: 33.33% !important;
            }
        }

        @media screen and (max-width: 399px) {
            .bls-level-w{
                width: 50% !important;
            }
        }
    </style>
    <div class="row">
        <div class="col-lg-12">
            <div class="card-header">
                <div class="row">
                        
                    @for($i = 1; $i <= $general->matrix_height; $i++)
                        @php
                            $numAff = $general->matrix_width ** $i;
                            $number = showCount(auth()->user()->id, $i);
                            $coef = $number/ $numAff;
                        @endphp
                        @if ($coef < 0.34)
                            <div class="bls-level-w pb-3">
                                <a href="{{route('user.matrix.index', ['lv_no' => $i])}}" class="btn btn-danger">@lang('Level '.$i)</a>
                            </div>

                        @elseif($coef >= 0.34 && $coef < 0.67)
                            <div class="bls-level-w pb-3">
                                <a href="{{route('user.matrix.index', ['lv_no' => $i])}}" class="btn button-red-orange">@lang('Level '.$i)</a>
                            </div>
                        @elseif($coef >= 0.67 && $coef < 0.75)
                            <div class="bls-level-w pb-3">
                                <a href="{{route('user.matrix.index', ['lv_no' => $i])}}" class="btn btn-warning">@lang('Level '.$i)</a>
                            </div>
                        @elseif($coef >= 0.75 && $coef <= 0.99)
                            <div class="bls-level-w pb-3">
                                <a href="{{route('user.matrix.index', ['lv_no' => $i])}}" class="btn button-orange-green">@lang('Level '.$i)</a>
                            </div>
                        @elseif($coef == 1)
                            <div class="bls-level-w pb-3">
                                <a href="{{route('user.matrix.index', ['lv_no' => $i])}}" class="btn btn-success">@lang('Level '.$i)</a>
                            </div>
                        @endif
                        
                    @endfor
                </div>
            </div>
            <div class="card">
                <div class="table-responsive table-responsive-xl">
                    <table class="table align-items-center table-light">
                        <thead>
                        <tr>
                            <th scope="col">@lang('Username')</th>
                            <th scope="col">@lang('Under Position')</th>
                            <th scope="col">@lang('Ref. By')</th>
                            <th scope="col">@lang('Balance')</th>

                        </tr>
                        </thead>
                        <tbody class="list">
                        {{ showUserLevel(auth()->user()->id, $lv_no) }}
                        </tbody>

                    </table>
                </div>

            </div>
        </div>
    </div>
@endsection


