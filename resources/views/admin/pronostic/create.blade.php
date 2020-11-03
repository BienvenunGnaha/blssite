@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
        <form id="frmProducts" method="post" action="{{route('admin.create.pronostic')}}">
                    @csrf
                <div class="card-body table-responsive">
                    <div class="form-group">
                        <div id="bls-collection-details" class="w-100">

                        </div>
                        
                        <div class="w-100 mt-2">
                            <span id="bls-add-bet" class="btn btn-primary" data-sport="{{$sports}}">Ajouter un pari</span>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group d-none">
                                    <textarea class="" id="details"  name="details" required></textarea>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <strong>@lang('Button') </strong>
                                    <input class="form-control" name="button" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <strong>@lang('Button') (FR)</strong>
                                    <input class="form-control" name="button_fr" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <strong>@lang('Advice') :</strong>
                                    <textarea class="form-control" name="conseil" rows="10"></textarea>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <strong>@lang('Advice') (en Français):</strong>
                                    <textarea class="form-control" name="conseil_fr" rows="10"></textarea>
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
    <input id="bls-bookmaker-all" type="hidden" value="{{json_encode(App\Bookmaker::all()->toArray())}}">
@endsection

@push('script')
    <script>
        $(function(){

            var metaData = [];

            function uniqKey(){
                var random = Math.floor(Math.random() * 1000000); 
                var time = new Date().getTime();  
                return String(random)+String(time);
            }

            function elmentExists(uniqId){
                var i = 0;
                var item = null;
                var found = false;
                objs = metaData;
                objs.forEach(obj => {
                    if(obj.uniqid == uniqId){
                        item = i;
                        found =true;

                    }
                    i++;
                });

                if(found){
                    return item;
                }else{
                    return false;
                }
                
            }

            function remove(index){
                var len = metaData.length;
                var newMeta = [];
                for(var i = 0; i < len; i++) {
                    if(i != index){
                        newMeta.push(metaData[i]);
                    }
                }
                metaData = newMeta;
                updateDetails()
            }

            function updateDetails(){
                $('#details').val(JSON.stringify(metaData));
                console.log($('#details').val());
            }

            $('body').delegate('.bls-sport-select', 'click', function(){
                var uniqid = $(this).attr('data-id');
                $('#bls-collection-details').find('.bls-sport-option[data-id='+uniqid+']').toggleClass('d-none');
            });

            $('body').delegate('.bls-sport-radio', 'change', function(){
                var uniqid = $(this).attr('data-id');
                var el = elmentExists(uniqid);
                if(el !== false){
                    metaData[el].sport = $(this).val();
                    updateDetails();
                    var children = $('.bls-sport-option-item[data-id='+$(this).attr('data-tag')+uniqid+']').children().clone();
                    $('.bls-sport-select[data-id='+uniqid+']').children().remove();
                    $('.bls-sport-select[data-id='+uniqid+']').append(children);
                    $('.bls-sport-select[data-id='+uniqid+']').trigger('click');
                }

            });

            $('body').delegate('.team1', 'keyup', function(){
                var uniqid = $(this).attr('data-id');
                var el = elmentExists(uniqid);
                if(el !== false){
                    metaData[el].team1 = $(this).val();
                    updateDetails();
                }

            });

            $('body').delegate('.team1_icon', 'change', function(){
                var uniqid = $(this).attr('data-id');
                var el = elmentExists(uniqid);
                var file = $(this)[0].files[0];
                if(el !== false){
                    var reader = new FileReader();
                    reader.onload = function(event) {
                        metaData[el].team1_icon = event.target.result;
                        updateDetails();
                    };
                    reader.readAsDataURL(file);
                    
                }
            });

            $('body').delegate('.team2', 'keyup', function(){
                var uniqid = $(this).attr('data-id');
                var el = elmentExists(uniqid);
                if(el !== false){
                    metaData[el].team2 = $(this).val();
                    updateDetails();
                }
                
            });

            $('body').delegate('.team2_icon', 'change', function(){
                var uniqid = $(this).attr('data-id');
                var el = elmentExists(uniqid);
                var file = $(this)[0].files[0];
                if(el !== false){
                    var reader = new FileReader();
                    reader.onload = function(event) {
                        metaData[el].team2_icon = event.target.result;
                        updateDetails();
                    };
                    reader.readAsDataURL(file);
                    
                }
            });

            $('body').delegate('.comp', 'keyup change', function(){
                var uniqid = $(this).attr('data-id');
                var el = elmentExists(uniqid);
                if(el !== false){
                    metaData[el].comp = $(this).val();
                    updateDetails();
                }

            });

            $('body').delegate('.comp_fr', 'keyup change', function(){
                var uniqid = $(this).attr('data-id');
                var el = elmentExists(uniqid);
                if(el !== false){
                    metaData[el].comp_fr = $(this).val();
                    updateDetails();
                }

            });

            $('body').delegate('.prono_match', 'keyup change', function(){
                var uniqid = $(this).attr('data-id');
                var el = elmentExists(uniqid);
                if(el !== false){
                    metaData[el].prono_match = $(this).val();
                    updateDetails();
                }

            });

            $('body').delegate('.prono_match_fr', 'keyup change', function(){
                var uniqid = $(this).attr('data-id');
                var el = elmentExists(uniqid);
                if(el !== false){
                    metaData[el].prono_match_fr = $(this).val();
                    updateDetails();
                }

            });

            $('body').delegate('.bookmaker', 'change', function(){
                var uniqid = $(this).attr('data-id');
                var el = elmentExists(uniqid);
                if(el !== false && $(this).val() !== ''){
                    metaData[el].bookmaker = $(this).val();
                    updateDetails();
                }

            });

            $('body').delegate('.date', 'change keyup', function(){
                var uniqid = $(this).attr('data-id');
                var el = elmentExists(uniqid);
                if(el !== false){
                    metaData[el].date = $(this).val();
                    updateDetails();
                }
                
            });

            $('body').delegate('.cote', 'keyup', function(){
                var uniqid = $(this).attr('data-id');
                var el = elmentExists(uniqid);
                if(el !== false){
                    metaData[el].cote = $(this).val();
                    updateDetails();
                }
                
            });//

            $('body').delegate('.remove', 'click', function(){
                var uniqid = $(this).attr('data-id');
                var el = elmentExists(uniqid);
                if(el !== false){
                    remove(el);
                    $('body').find('.bls-row-bet[data-id='+uniqid+']').remove();
                }
                
            });

            $('#bls-add-bet').click(function(){
                var uniqid = uniqKey();
                var sports = JSON.parse($(this).attr('data-sport'));
                var bms = JSON.parse($('#bls-bookmaker-all').val());
                console.log(sports);
                var optionArray = [];
                var bmOptionArray = [];
                var len = sports.length;
                for(var i = 0; i < len; i++) {
                    var sport = sports[i];
                    optionArray.push('<div data-id="'+uniqid+'" class="w-100 p-2 border-bottom bls-sport-option-item-wrap">\
                                            <input data-tag="'+i+'" data-id="'+uniqid+'" type="radio" name="'+uniqid+'" id="sport_'+sport.name+uniqid+'" value="'+sport.id+'" class="d-none bls-sport-radio" required>\
                                            <label for="sport_'+sport.name+uniqid+'" data-id="'+i+uniqid+'" data-tag="'+i+'" class="bls-sport-option-item"> <img src="/assets/images/sport/'+sport.icon+'" width="40px"><span class="ml-2">'+sport.name+'</span></label>\
                                        </div>');
                }

                var lenbm = bms.length;
                for(var i = 0; i < lenbm; i++) {
                    var bm = bms[i];
                    bmOptionArray.push('<option data-id="'+uniqid+'" value="'+bm.id+'">'+bm.name+'</option>');
                }
                var toAppend = '<div data-id="'+uniqid+'" class="row bls-row-bet border-bottom">\
                            <div class="col-md-6">\
                                <div class="form-group">\
                                    <strong>Team 1</strong>\
                                    <input data-id="'+uniqid+'" type="text" class="form-control form-control-lg team1"  placeholder="Team 1" required>\
                                </div>\
                            </div>\
                            <div class="col-md-6">\
                                <div class="form-group">\
                                <strong>Team 2:</strong>\
                                    <div class="input-group">\
                                        <input data-id="'+uniqid+'" type="text" class="form-control form-control-lg team2" placeholder="Team 2" required>\
                                        <div class="input-group-append">\
                                            <span class="input-group-text"></span>\
                                        </div>\
                                    </div>\
                                </div>\
                            </div>\
                            <div class="col-md-6">\
                                <div class="form-group">\
                                    <strong>Drepeau Team 1</strong>\
                                    <input data-id="'+uniqid+'" type="file" class="form-control form-control-lg team1_icon"  placeholder="Team 1" required>\
                                </div>\
                            </div>\
                            <div class="col-md-6">\
                                <div class="form-group">\
                                <strong>Drapeau Team 2:</strong>\
                                    <div class="input-group">\
                                        <input data-id="'+uniqid+'" type="file" class="form-control form-control-lg team2_icon" placeholder="Team 2" required>\
                                        <div class="input-group-append">\
                                            <span class="input-group-text"></span>\
                                        </div>\
                                    </div>\
                                </div>\
                            </div>\
                            <div class="col-md-6">\
                                <div class="form-group">\
                                    <strong>Compétition</strong>\
                                    <input data-id="'+uniqid+'" type="text" class="form-control form-control-lg comp"  placeholder="Competition" required>\
                                </div>\
                            </div>\
                            <div class="col-md-6">\
                                <div class="form-group">\
                                <strong>Compétition (FR):</strong>\
                                    <div class="input-group">\
                                        <input data-id="'+uniqid+'" type="text" class="form-control form-control-lg comp_fr" placeholder="Competition" required>\
                                        <div class="input-group-append">\
                                            <span class="input-group-text"></span>\
                                        </div>\
                                    </div>\
                                </div>\
                            </div>\
                            <div class="col-md-6">\
                                <div class="form-group">\
                                    <strong>Pronotic</strong>\
                                    <input data-id="'+uniqid+'" type="text" class="form-control form-control-lg prono_match"  placeholder="Pronotic" required>\
                                </div>\
                            </div>\
                            <div class="col-md-6">\
                                <div class="form-group">\
                                <strong>Pronotic (FR):</strong>\
                                    <div class="input-group">\
                                        <input data-id="'+uniqid+'" type="text" class="form-control form-control-lg prono_match_fr" placeholder="Pronotic" required>\
                                        <div class="input-group-append">\
                                            <span class="input-group-text"></span>\
                                        </div>\
                                    </div>\
                                </div>\
                            </div>\
                            <div class="col-md-6">\
                                <div class="form-group">\
                                    <strong>Sport:</strong>\
                                    <span data-id="'+uniqid+'" class="bls-sport-select p-2 mt-2 font-weight-bold border w-100 text-center d-block" id="bls-sport-fr-list"><span>Sélectionner un sport</span></span>\
                                    <div data-id="'+uniqid+'" class="bls-sport-option w-100 border" class="d-none">\
                                        '+optionArray.join('')+'\
                                    </div>\
                                </div>\
                            </div>\
                            <div class="col-md-6">\
                                <div class="form-group">\
                                    <strong>Bookmaker</strong>\
                                    <select data-id="'+uniqid+'" class="bookmaker">\
                                        <option value="">Select BookMaker</option>\
                                        '+bmOptionArray.join('')+'\
                                    </select>\
                                </div>\
                            </div>\
                            <div class="col-md-6">\
                                <div class="form-group">\
                                    <strong>Cote :</strong>\
                                    <input data-id="'+uniqid+'"  type="text" min="1" class="form-control form-control-lg cote" placeholder="Cote" required>\
                                </div>\
                            </div>\
                            <div class="col-md-6">\
                                <div class="form-group">\
                                    <strong>Meeting Date :</strong>\
                                    <input data-id="'+uniqid+'" type="text" class="form-control form-control-lg date" placeholder="Cote" required>\
                                </div>\
                            </div>\
                            <div class="col-md-6">\
                                <div class="form-group">\
                                    <span data-id="'+uniqid+'" class="btn btn-danger remove"> Supprimer le pari</span>\
                                </div>\
                            </div>\
                        </div>';

                $('#bls-collection-details').append(toAppend);
                metaData.push({
                    uniqid: uniqid, 
                    team1: null, 
                    team2: null, 
                    sport: null, 
                    cote: null, 
                    date: null, 
                    prono_macth: null, 
                    prono_match_fr: null,
                    comp: null,
                    comp_fr: null,
                    team1_icon: null,
                    team2_icon: null, 
                    bookmaker: null
                });
            });
            
            
            

            $('#bls-add-bet').trigger('click');
            $('.bls-sport-select').trigger('click');
            setTimeout(() => {
                $('#bls-collection-details').find(".date").flatpickr({
                enableTime: true,
                dateFormat: "Y-m-d H:i",
            });
            }, 2000);
        });
    </script>
@endpush