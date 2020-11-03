@extends(activeTemplate() .'layouts.app')


@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="card mt-3">
                <div class="card-header">
                    <h4 class="card-title font-weight-normal">@lang('Referrer Link')</h4>
                </div>

                <div class="card-body">

                    <form id="copyBoard" >
                        <div class="form-row align-items-center">
                            <div class="col-md-10 my-1">
                                <input value="{{url('/')}}/user/register/{{auth()->user()->username}}" type="url" id="ref" class="form-control from-control-lg" readonly>
                            </div>
                            <div class="col-md-2 my-1">
                                <button   type="button" @click="copyBtnClick" data-copytarget="#ref" id="copybtn" class="btn btn-primary btn-block"> <i class="fa fa-copy"></i> Copy</button>
                            </div>
                        </div>
                    </form>
                    <img class="ml-auto mr-auto" src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(150)->generate('https://businesslifesport.com/user/register/'.auth()->user()->username)) !!} ">
                
                </div>

            </div>
        </div>

        <div class="col-lg-12">

            <div class="card">
                <div class="table-responsive table-responsive-xl">
                    <table class="table align-items-center table-light">
                        <thead>
                        <tr>
                            <th scope="col">@lang('Username')</th>
                            <th scope="col">@lang('Plan')</th>
                            <th scope="col">@lang('Join date')</th>
                        </tr>
                        </thead>


                        <tbody class="list">
                        @forelse($referrals as $data)
                            <tr>
                                <td>{{$data->username}}</td>
                                <td>
                                    @php $plan = \App\Plan::find($data->plan_id); @endphp
                                    @if($plan != NULL)
                                        {{$plan->name}}
                                    @else
                                        @lang('N/A')
                                    @endif
                                </td>
                                <td>{{show_datetime($data->created_at)}}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">{{__('NO DATA FOUND')}}</td>
                            </tr>
                        @endforelse
                        </tbody>


                    </table>
                </div>
                <div class="card-footer py-4">
                    <nav aria-label="...">

                        {{$referrals->links()}}
                    </nav>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('js')
    <script>
        (function() {
            'use strict';
            document.body.addEventListener('click', copy, true);
            function copy(e) {
                var
                    t = e.target,
                    c = t.dataset.copytarget,
                    inp = (c ? document.querySelector(c) : null);
                if (inp && inp.select) {
                    inp.select();
                    try {
                        document.execCommand('copy');
                        inp.blur();
                        t.classList.add('copied');
                        setTimeout(function() { t.classList.remove('copied'); }, 1500);
                    }catch (err) {
                        alert('please press Ctrl/Cmd+C to copy');
                    }
                }
            }
        })();
    </script>
@endpush

