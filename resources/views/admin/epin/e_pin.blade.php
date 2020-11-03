@extends('admin.layouts.app')

@section('panel')


    <div class="row">

        <div class="col-lg-12">
            <div class="card">
                <div class="table-responsive table-responsive-xl">
                    <table class="table align-items-center table-light">
                        <thead>
                        <tr>
                            @if(request()->path() == 'admin/used-pin')
                                <th scope="col">User</th>
                                <th scope="col">Amount</th>
                                <th scope="col">PIN</th>
                                <th scope="col">Used At</th>
                            @elseif(request()->path() == 'admin/manage-pin')
                                <th scope="col">Amount</th>
                                <th scope="col">PIN</th>
                            @endif

                        </tr>
                        </thead>


                        <tbody class="list">
                        @if(request()->path() == 'admin/used-pin')
                            @foreach($trans as $p)
                                <tr>
                                    <td><a href="{{route('admin.users.detail', $p->pin_user->id)}}">{{$p->pin_user->fullname}}</a></td>
                                    <td>{{$general->cur_sym}}{{ $p->amount }}</td>
                                    <td>{{ $p->pin }}</td>
                                    <td>{{ $p->updated_at }}</td>
                                </tr>
                            @endforeach
                        @elseif(request()->path() == 'admin/manage-pin')
                            @foreach($trans as $p)
                                <tr>
                                    <td>{{$general->cur_sym}}{{ $p->amount }}</td>
                                    <td>{{ $p->pin }}</td>
                                </tr>
                            @endforeach
                        @endif


                        </tbody>




                    </table>
                </div>
                <div class="card-footer py-4">
                    <nav aria-label="...">
                        {{$trans->links()}}
                    </nav>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title bold uppercase" id="myModalLabel">@lang('Generate E-PIN')</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <form class="form-horizontal" method="post" action="{{route('admin.storePin')}}">
                    {{csrf_field()}}
                    <div class="modal-body">


                        <div class="form-group error">
                            <label for="inputName" class="col-sm-12  bold uppercase">@lang('Amount') : </label>
                            <div class="col-sm-12">
                                <div class="input-group">
                                    <input type="text" class="form-control input-lg" name="amount">
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                             {{$general->cur_sym}}
                                            </span>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="form-group error">
                            <label for="inputName" class="col-sm-12 bold uppercase">@lang('Number (How Many Pins want to create)'): </label>
                            <div class="col-sm-12">
                                <input type="number" class="form-control has-error bold" name="number" >
                                <code>@lang('PIN will generate automatically')</code>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary bold uppercase"> @lang('Generate') </button>
                    </div>
                </form>
            </div>
        </div>
    </div>






@endsection

@push('breadcrumb-plugins')

    @if(request()->path() == 'admin/manage-pin')
        <button data-toggle="modal" data-target="#myModal" class="btn btn-success"><i class="fa fa-fw fa-plus"></i> @lang('Add New') </button>
    @endif

@endpush

@push('script')

@endpush





