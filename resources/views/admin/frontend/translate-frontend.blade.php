<div class="col-lg-12">
    @php
        $items = (array)$front->value;
    @endphp
    
        @if (isset($items['lang']) && isset($items['lang']->$lang))
            @foreach ($items['lang']->$lang as $key => $item)
                @if ($key == 'image')
                    <div class="form-group">
                        <div class="image-upload">
                            <div class="thumb">
                                <div class="avatar-preview">
                                <div class="profilePicPreview" style="background-image: url({{ get_image(config('constants.frontend.'.$front->key.'.path') .'/'. $items[$key]) }})">
                                        <button type="button" class="remove-image"><i class="fa fa-times"></i></button>
                                    </div>
                                </div>
                                <div class="avatar-edit">
                                    <input type="hidden" value="{{$item}}">
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif($key == 'body' || $key == 'detail')
                    <div class="form-group">
                        <label>@lang(ucfirst(trans($key)))</label>
                        <textarea rows="10" class="form-control nicEdit" name="{{$key}}">{!!$item!!}</textarea>
                    </div>
                @else
                    <div class="form-group">
                        <label>@lang(ucfirst(trans($key)))</label>
                        <input type="text" class="form-control" name="{{$key}}" value="{{$item}}">
                    </div>
                @endif
            @endforeach
        @else
            @foreach ($items as $key => $item)
                @if ($key == 'image')
                        <div class="form-group">
                            <div class="image-upload">
                                <div class="thumb">
                                    <div class="avatar-preview">
                                    <div class="profilePicPreview" style="background-image: url({{ get_image(config('constants.frontend.'.$front->key.'.path') .'/'. $items[$key]) }})">
                                            <button type="button" class="remove-image"><i class="fa fa-times"></i></button>
                                        </div>
                                    </div>
                                    <div class="avatar-edit">
                                        <input type="hidden" name="{{$key}}" value="{{$item}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($key == 'body' || $key == 'detail')
                        <div class="form-group">
                            <label>@lang(ucfirst(trans($key)))</label>
                            <textarea rows="10" class="form-control nicEdit" name="{{$key}}">{!!$item!!}</textarea>
                        </div>
                    @else
                        <div class="form-group">
                            <label>@lang(ucfirst(trans($key)))</label>
                            <input type="text" class="form-control" name="{{$key}}" value="{{$item}}">
                        </div>
                @endif
           @endforeach
        @endif
        <script src="https://businesslifesport.com/assets/admin/js/nicEdit.js"></script>
        <script type="text/javascript">
            $(function () {
                $('body').find(".nicEdit").each(function (index) {
                    $(this).attr("id", "nicEditor" + index);
                    new nicEditor({fullPanel: true}).panelInstance('nicEditor' + index, {hasPanel: true});
                });
            });
        </script>
</div>