@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <form id="frmProducts" method="post" action="{{route('admin.create.sport')}}" enctype="multipart/form-data">
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
                                    <strong>@lang('Name') (FR)</strong>
                                    <input type="text" class="form-control form-control-lg" id="name_fr"  name="name_fr" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <strong>@lang('Icon') </strong>
                                    <input type="file" class="form-control form-control-lg" id="icon"  name="icon" required>
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

@push('script')
    <script>
        $(function(){
            $('#bls-sport-en-select').click(function(){
                $('#bls-sport-en-option').toggleClass('d-none');
            });

            $('#bls-sport-fr-select').click(function(){
                $('#bls-sport-fr-option').toggleClass('d-none');
            });

            $('.bls-sport-en-option').click(function(){
                $chidren = $(this).children().clone();
                $('#bls-sport-en-select').children().remove();
                $('#bls-sport-en-select').append($chidren);
            });

            $('.bls-sport-fr-option').click(function(){
                $chidren = $(this).children().clone();
                $('#bls-sport-fr-select').children().remove();
                $('#bls-sport-fr-select').append($chidren);
            });

            $('#bls-sport-en-select').trigger('click');
            $('#bls-sport-fr-select').trigger('click');
        });
    </script>
@endpush