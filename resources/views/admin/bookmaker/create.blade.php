@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <form id="frmProducts" method="post" action="{{route('admin.bookmakers.store')}}" enctype="multipart/form-data">
                    @csrf
                <div class="card-body table-responsive">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <strong>@lang('Name')</strong>
                                    <input type="text" class="form-control form-control-lg" id="name"  name="name" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <strong>@lang('Photo') </strong>
                                    <input type="file" class="form-control form-control-lg" id="photo"  name="photo" required>
                                </div>
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
