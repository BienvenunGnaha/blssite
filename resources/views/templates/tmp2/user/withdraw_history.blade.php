@extends(activeTemplate() .'layouts.app')
@push('style')


@endpush

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="table-responsive table-responsive-xl">
                    <table class="table align-items-center table-light">
                        <thead>
                        <tr>

                            <th scope="col">@lang('Date')</th>
                            <th scope="col">@lang('Withdraw Id')</th>
                            <th scope="col">@lang('Method')</th>
                            <th scope="col">@lang('Amount')</th>
                            <th scope="col">@lang('Delay')</th>
                            <th scope="col">@lang('Status')</th>
                        </tr>
                        </thead>
                        <tbody >

                        @php $now = \Carbon\Carbon::now(); @endphp
                        @forelse($logs as $withdraw)
                            <tr>
                                <td>{{ show_datetime($withdraw->created_at) }}</td>
                                <td >{{ strtoupper($withdraw->trx) }}</td>
                                <td>{{ $withdraw->method->name }}</td>
                                <td >{{$general->cur_sym}}{{ formatter_money($withdraw->amount) }}</td>
                                <td >{{ $withdraw->delay }}</td>
                                <td>
                                    @if($withdraw->status == 3)
                                        <label class="badge badge-danger">@lang('Reject')</label>
                                    @elseif($withdraw->status == 2)
                                        <label class="badge badge-warning">@lang('Pending')</label>
                                    @elseif($withdraw->status == 1)
                                        <label class="badge badge-success">@lang('Complete')</label>
                                    @endif
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">{{ $empty_message }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer py-4">
                    <nav aria-label="...">
                        {{$logs->links()}}
                    </nav>
                </div>

            </div>
        </div>
    </div>


@endsection
