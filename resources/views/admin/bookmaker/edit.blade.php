@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
        <form id="frmProducts" method="POST" action="{{route('admin.bookmakers.update', $bookmaker->id)}}" enctype="multipart/form-data">
                    @csrf
                    <input name="_method" type="hidden" value="PUT">
                <div class="card-body table-responsive">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <strong>@lang('Name')</strong>
                                <input type="text" class="form-control form-control-lg" id="name"  name="name" value="{{$bookmaker->name}}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <strong>@lang('Photo') </strong>
                                    <input type="file" class="form-control form-control-lg" id="photo"  name="photo">
                                </div>
                                <img class="img-fluid" src="{{asset(config('constants.bookmaker.path').'/'.$bookmaker->photo)}}">
                            </div>
                        </div>
                    </div>


                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success btn-block bold uppercase"><i class="fa fa-send"></i> @lang('Save')</button>
                </div>
                </form>
        </div>
    </div>
@endsection
