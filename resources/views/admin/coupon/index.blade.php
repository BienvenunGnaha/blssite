@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-12 d-flex justify-content-end mt-3 mb-3"> <a class="btn btn-primary" href="{{route('admin.create.coupon')}}">Ajouter un coupon</a></div>
        <div class="col-lg-12">
            <div class="card">
                <div class="table-responsive table-responsive-xl">
                    <table class="table align-items-center table-light">
                        <thead>
                            <tr>
                                <th scope="col">Code Promo</th>
                                <th scope="col">Name</th>
                                <th scope="col">Create Date</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            @foreach($coupons as $coupon)
                                <tr>
                                    <td scope="row">
                                        {{$coupon->id_coupon}}
                                    </td>
                                        
                                    <td>
                                        {{$coupon->name}}
                                    </td>

                                    <td>
                                        {{$coupon->created_at}}
                                    </td>
                                    <td>
                                        <form action="{{route('admin.delete.coupon', $coupon->id)}}" method="post" class="mt-1">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <input type="hidden" name="_method" value="delete">
                                            <button type="submit" class="btn btn-danger btn-block">@lang('Delete')</button>
                                        </form>
                                    </td>
                                </tr>
                        
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
