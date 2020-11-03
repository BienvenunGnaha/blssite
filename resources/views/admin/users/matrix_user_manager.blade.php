@extends('admin.layouts.app')

@section('panel')
    <div class="row">
    <form action="{{route('admin.matrix.up-down.update')}}" method="post">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div id="matrix-user-update-field-block" class="row">
                <div class="col-6 form-group">
                    <label for="" class="form-control-label">Utilisateurs</label>
                    <select name="user" id="user" class="form-control" required>
                            <option value="">Selectionnner un utilisateur</option>
                        @foreach ($users as $user)
                            <option value="{{$user->id}}" data-position="{{$user->position}}">{{$user->username}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 form-group">
                    <label for="" class="form-control-label">Mise à jour matrix</label>
                    <select name="matrix_update_type" id="matrix_update_type" class="form-control">
                        <option value="">Selectionnner le type de mise à jour matrix</option>
                        <option value="1">Monter</option>
                        <option value="2">Descendre</option>
                        <option value="3">Remplacer</option>
                    </select>
                </div>
                <div id="position" class="col-6 form-group"></div>
                <div id="user_target" class="col-6 form-group">
                    <label for="" class="form-control-label">Selectionner un utilisateur cible</label>
                    <select name="user_target" id="user_target_field" class="form-control">
                        
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" >Mettre à jour</button>
        </form>
    </div>
   
@endsection

@push('script')
    <script>
        $(function(){
            $('#user_target').hide();
            $('#matrix_update_type, #user').change(function(){
                if($('#matrix_update_type').val() !== '' && $('#user').val() !== ''){
                    $.ajax({
                        url: '/admin/matrix/up-down/'+$('#user').val()+'/'+$('#matrix_update_type').val(),
                        type: 'get',
                        success: function(res){
                            console.log(res);
                            var fieldsBlock = $('#matrix-user-update-field-block');
                            var count = res.length;
                            var user_id = $('#user').val();
                            var position = $('#user option[value='+user_id+']').attr('data-position');
                            var pos = $('#position');
                            fieldsBlock.find('#position').children().remove();
                            $('#user_target_field').children().remove();
                            if($('#matrix_update_type').val() !== '1'){
                                $('#user_target').show();
                            }
                            for(var i = 0; i < count; i++){
                                var item = res[i];
                                var user = res[i][0];
                                var countChild = res[i][1];
                                if(user !== null && countChild !== null){
                                    if($('#matrix_update_type').val() === '1'){
                                        $('#user_target').hide();
                                        var fieldBlock = '<label for="" class="form-control-label">Position</label>\
                                                        <input type="number" value="'+countChild+'" min="1" max="'+countChild+'"name="position"  class="form-control">\
                                                        ';
                                        pos.append(fieldBlock);
                                    }else{
                                        console.log('ccccccccccccccccc');
                                        $('#user_target_field').append('<option value="'+user.id+'" data-position="'+countChild+'">'+user.username+'</option>');
                                    }
                                }
                            }
                            
                        },
                        error: function(err){

                        }
                    })
                }
            });

            $('#user_target_field').change(function() {
                var value = $(this).val();
                var pos = $('#position');
                pos.children().remove();
                var feuills = Number($(this).find('option[value='+value+']').attr('data-position'));
                var fieldBlock = '<label for="" class="form-control-label">Position</label>\
                                <input type="number" value="'+feuills+'" min="1" max="'+feuills+'"name="position"  class="form-control">\
                                ';
                pos.append(fieldBlock);
            });
        });
    </script>
@endpush


