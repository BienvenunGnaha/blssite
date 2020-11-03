@extends('admin.layouts.app')

@section('panel')
    <div class="col-lg-12">
        <div class="card">
            <form action="{{ route('admin.frontend.translate.front.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="card-body">

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>@lang('Language')</label>
                                <select id="bls-select-lang" name="lang" required>
                                    <option value="">@lang('Select a language')</option>
                                    @foreach($langs as $lang)
                                        <option value="{{$lang->code}}">@lang($lang->name)</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="bls-frontend-block" class="form-group d-none">
                                <label>@lang('Frontend Manager')</label>
                                <select id="bls-select-frontend" name="frontend" required>
                                    <option value="">@lang('Select a frontend')</option>
                                    @foreach($fronts as $front)
                                        <option value="{{$front->id}}">@lang($front->key)</option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="bls-frontend-content" class="form-group w-100p-2">

                            </div>

                            <div class=" p-2">
                                <img id="bls-frontend-loader" class="ml-auto mr-auto d-none" src="{{asset('assets/images/ajax-loader.gif')}}">
                            </div>

                        </div>

                    </div>
                </div>





                <div class="card-footer py-4">
                    <button type="submit" class="btn btn-block btn-primary mr-2">@lang('Update')</button>
                </div>
            </form>


        </div>
    </div>

@endsection

@push('breadcrumb-plugins')


@endpush

@push('style-lib')

@endpush


@push('script')

<script>
    $(function(){
        $('#bls-select-lang').change(function(){
            var value = $(this).val();
            if(value && value !== ''){
                if($('#bls-frontend-block').hasClass('d-none')){
                    $('#bls-frontend-block').removeClass('d-none');
                }
            }else{
                if(!$('#bls-frontend-block').hasClass('d-none')){
                    $('#bls-frontend-block').addClass('d-none');
                }
            }
        });

        $('#bls-select-frontend').change(function(){
            var value = $(this).val();
            console.log(value)
            if(value && value !== ''){
                console.log('Attrapé')
                if($('#bls-frontend-loader').hasClass('d-none')){
                    $('#bls-frontend-loader').removeClass('d-none');
                }

                var url = "/admin/frontend/translate/front/"+value+"/"+$('#bls-select-lang').val();

                $.ajax({
                    url: url,
                    type: 'get',
                    success: function(res){
                        if(!$('#bls-frontend-loader').hasClass('d-none')){
                            $('#bls-frontend-loader').addClass('d-none');
                        }
                        $('#bls-frontend-content').children().remove();
                        $('#bls-frontend-content').append(res);
                    },
                    error: function(error){
                        console.log(error);
                        $('#bls-frontend-content').find('#bls-frontend-error-message').remove();
                        $('#bls-frontend-content').prepend('<span id="bls-frontend-error-message">Une erreur s\'est produite</span>');
                    }
                })
            }
        });
    });
</script>

@endpush
