@extends(activeTemplate() .'layouts.app')

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="table-responsive table-responsive">
                    <table class="table align-items-center table-light">
                        <thead>
                        <tr>
                            <th scope="col">@lang('Sl')</th>
                            <th scope="col">@lang('TRX-ID') </th>
                            <th scope="col">@lang('Amount') </th>
                            <th scope="col">@lang('After Balance') </th>
                            <th scope="col">@lang('Detail') </th>
                            <th scope="col">@lang('Time') </th>
                        </tr>
                        </thead>
                        <tbody class="list">
                        @forelse($table as $key=>$data)
                            <tr>
                                <td>{{$table->firstItem()+$key}}</td>
                                <td>{{$data->trx}}</td>
                                <td>{{$general->cur_sym}}{{formatter_money($data->amount)}}</td>
                                <td>{{$general->cur_sym}}{{formatter_money($data->balance)}}</td>
                                <td>{{$data->title}}</td>

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

                        {{$table->links()}}
                    </nav>
                </div>
            </div>
        </div>
    </div>

@endsection
