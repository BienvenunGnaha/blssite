@extends(activeTemplate() .'layouts.app')



@section('style')

@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="table-responsive table-responsive-xl">
                    <table class="table align-items-center table-light">
                        <thead>
                        <tr>

                            <th scope="col">@lang('Date')</th>
                            <th scope="col">@lang('TRX')</th>
                            <th scope="col">@lang('Method')</th>
                            <th scope="col">@lang('Amount')</th>
                            <th scope="col">@lang('Status')</th>
                        </tr>
                        </thead>
                        <tbody >

                        @forelse($logs as $item)
                            <tr>
                                <td>{{ show_datetime($item->created_at) }}</td>
                                <td>{{ $item->trx }}</td>
                                <td>{{ __($item->gateway->name) }}</td>
                                <td>{{ $general->cur_sym }}{{ formatter_money($item->amount) }} </td>
                                <td>
                                    @if($item->status == 1)
                                        <span class="badge badge-success">@lang('Complete')</span>
                                    @elseif ($item->status == 2)
                                        <span class="badge badge-warning">@lang('Pending')</span>
                                    @elseif ($item->status == 3)
                                        <span class="badge badge-danger">@lang('Reject')</span>
                                    @endif

                                </td>
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
                        {{ $logs->links() }}
                    </nav>
                </div>

            </div>
        </div>
    </div>
@endsection



