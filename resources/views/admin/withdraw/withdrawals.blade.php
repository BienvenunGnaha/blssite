@extends('admin.layouts.app')

@section('panel')
<div class="row">

    <div class="col-lg-12">
        <div class="card">
            <div class="table-responsive table-responsive-xl">
                <table class="table align-items-center table-light">
                    <thead>
                        <tr>
                            <th scope="col">Date</th>
                            <th scope="col">Withdrawal Code</th>
                            <th scope="col">Username</th>
                            <th scope="col">Withdrawal Method</th>

                            <th scope="col">Amount</th>
                            <th scope="col">Charge</th>
                            <th scope="col">Payable Amount</th>
                            <th scope="col">In Method Currency</th>
                            @if(request()->routeIs('admin.withdraw.pending'))
                            <th scope="col">Action</th>
                            @elseif(request()->routeIs('admin.withdraw.log') || request()->routeIs('admin.withdraw.search'))
                            <th scope="col">Status</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @php $now = \Carbon\Carbon::now(); @endphp
                        @forelse($withdrawals as $withdraw)
                        <tr>
                            <td>{{ show_datetime($withdraw->created_at) }}</td>

                            <td class="font-weight-bold">{{ strtoupper($withdraw->trx) }}</td>

                            <td><a href="{{ route('admin.users.detail', $withdraw->user->id) }}">{{ $withdraw->user->username }}</a></td>

                            <td>{{ $withdraw->method->name }}</td>

                            <td class="budget">{{ $general->cur_sym }}{{ formatter_money($withdraw->amount) }}</td>

                            <td class="budget text-danger">{{ $general->cur_sym }}{{ formatter_money($withdraw->charge) }}</td>

                            @php $payable = $withdraw->amount - $withdraw->charge; @endphp
                            <td class="budget">{{ $general->cur_sym }}{{ formatter_money($payable) }}</td>

                            <td class="budget">{{ $withdraw->currency}}{{ formatter_money($withdraw->rate * $payable) }}</td>
                            @if(request()->routeIs('admin.withdraw.pending'))
                            <td>
                                <button class="btn btn-success approveBtn" data-id="{{ $withdraw->id }}" data-amount="{{ $general->cur_sym }}{{ formatter_money($withdraw->amount) }}" data-username="{{ $withdraw->user->username }}"><i class="fa fa-fw fa-check"></i></button>
                                <button class="btn btn-danger rejectBtn" data-id="{{ $withdraw->id }}" data-amount="{{ $general->cur_sym }}{{ formatter_money($withdraw->amount) }}" data-username="{{ $withdraw->user->username }}"><i class="fa fa-fw fa-ban"></i></button>
                                @if(!empty($withdraw->detail))
                                <button class="btn btn-info viewBtn" data-ud="{{ json_encode($withdraw->detail) }}" ><i class="fa fa-fw fa-eye"></i></button>
                                @endif
                            </td>
                            @elseif(request()->routeIs('admin.withdraw.log') || request()->routeIs('admin.withdraw.search'))
                            <td>

                                @if($withdraw->status == 1)
                                <span class="badge badge-success">Approved</span>
                                @elseif($withdraw->status == 3)
                                    <span class="badge badge-danger">Rejected</span>
                                    @else
                                    <span class="badge badge-warning">Pending</span>

                                @endif
                            </td>
                            @endif

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
                    {{ $withdrawals->appends($_GET)->links() }}
                </nav>
            </div>
        </div>
    </div>
</div>

{{-- View MODAL --}}
<div id="viewModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">View User Information</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="userdata"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
{{-- APPROVE MODAL --}}
<div id="approveModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approve Withdrawal Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.withdraw.approve') }}" method="POST">
                @csrf
                <input type="hidden" name="id">
                <div class="modal-body">
                    <p>Are you sure to <span class="font-weight-bold">approve</span> <span class="font-weight-bold withdraw-amount text-success"></span> withdrawal of <span class="font-weight-bold withdraw-user"></span>?</p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Approve</button>
                    <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- REJECT MODAL --}}
<div id="rejectModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Withdrawal Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.withdraw.reject') }}" method="POST">
                @csrf
                <input type="hidden" name="id">
                <div class="modal-body">
                    <p>Are you sure to <span class="font-weight-bold">reject</span> <span class="font-weight-bold withdraw-amount text-success"></span> withdrawal of <span class="font-weight-bold withdraw-user"></span>?</p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Reject</button>
                    <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    var path = `{{ asset(config('constants.withdraw.verify.path')) }}`;
    $('.approveBtn').on('click', function() {
        var modal = $('#approveModal');
        modal.find('input[name=id]').val($(this).data('id'));
        modal.find('.withdraw-amount').text($(this).data('amount'));
        modal.find('.withdraw-user').text($(this).data('username'));
        modal.modal('show');
    });

    $('.rejectBtn').on('click', function() {
        var modal = $('#rejectModal');
        modal.find('input[name=id]').val($(this).data('id'));
        modal.find('.withdraw-amount').text($(this).data('amount'));
        modal.find('.withdraw-user').text($(this).data('username'));
        modal.modal('show');
    });

    $('.viewBtn').on('click', function() {
        var modal = $('#viewModal');
        var data = $(this).data('ud');

        var html = `<ul class="list-group">`;
        html += `<li class="list-group-items">`
        $.each(data, function(idx, val) {
            html += `<li>${idx} - ${val}</li>`;
        });
        html += `</ul>`;

        modal.find('.userdata').html(html);
        modal.modal('show');
    });
</script>
@endpush

@push('breadcrumb-plugins')
@if(request()->routeIs('admin.users.withdrawals'))

<form action="" method="GET" class="form-inline">
    <div class="input-group has_append">
        <input type="text" name="search" class="form-control" placeholder="Withdrawal code" value="{{ $search ?? '' }}">
        <div class="input-group-append">
            <button class="btn btn-success" type="submit"><i class="fa fa-search"></i></button>
        </div>
    </div>
</form>
@else
<form action="{{ route('admin.withdraw.search', $scope ?? str_replace('admin.withdraw.', '', request()->route()->getName())) }}" method="GET" class="form-inline">
    <div class="input-group has_append">
        <input type="text" name="search" class="form-control" placeholder="Withdrawal code/Username" value="{{ $search ?? '' }}">
        <div class="input-group-append">
            <button class="btn btn-success" type="submit"><i class="fa fa-search"></i></button>
        </div>
    </div>
</form>
@endif
@endpush
