@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
        <form id="frmProducts" method="post" action="{{route('admin.create.coupon')}}">
            @csrf
            <div class="card-body table-responsive">
                <div class="col-6">
                    <div class="form-group">
                        <strong>@lang('Name')</strong>
                        <input type="text" class="form-control" name="name" rows="10">
                    </div>
                </div>

                <div class="col-6">
                    <div class="form-group">
                        <strong>@lang('Duration')</strong>
                        <select name="duration">
                            <option value="once">@lang('Once')</option>
                            <option value="repeating">@lang('Repeating')</option>
                            <option value="forever">@lang('Forever')</option>
                        </select>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <strong>@lang('The amount to subtract from an invoice total')</strong>
                        <input type="number" class="form-control" name="amount_off" rows="10">
                    </div>
                </div>

                <div class="col-6">
                    <div class="form-group">
                        <strong>@lang('The number of months the discount will be in effect')</strong>
                        <input type="number" class="form-control" name="duration_in_months" rows="10">
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <strong>@lang('The percent the coupon will apply')</strong>
                        <input type="number" class="form-control" name="percent_off" rows="10">
                    </div>
                </div>

                <div class="col-6">
                    <div class="form-group">
                        <strong>@lang('Maximum number of times this coupon can be redeemed')</strong>
                        <input type="number" class="form-control" name="max_redemptions" rows="10">
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <strong>@lang('Date after which the coupon can no longer be redeemed')</strong>
                        <input class="date" type="datetime-local" class="form-control" name="redeem_by" rows="10">
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <strong>@lang('User')</strong>
                        
                        <select name="user_id">
                            <option >@lang('Selected user')</option>
                            @foreach($users as $user)
                                <option value="{{$user->id}}">{{$user->firstname.' '.$user->lastname}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-6">
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-success btn-block bold uppercase"><i class="fa fa-send"></i> @lang('Save')</button>
            </div>
        </form>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $(function(){
            setTimeout(() => {
                $(".date").flatpickr({
                enableTime: true,
                dateFormat: "Y-m-d H:i",
            });
            }, 2000);
        });
    </script>
@endpush
