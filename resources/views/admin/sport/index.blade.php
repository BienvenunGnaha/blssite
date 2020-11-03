@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-12 d-flex justify-content-end mt-3 mb-3"> <a class="btn btn-primaryE" href="{{route('admin.create.sport')}}">Ajouter sport</a></div>
        <div class="col-lg-12">
            <div class="card">
                <div class="table-responsive table-responsive-xl">
                    <table class="table align-items-center table-light">
                        <thead>
                            <tr>
                                <th scope="col">Icon</th>
                                <th scope="col">name</th>
                                <th scope="col">name (FR)</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            @foreach($sports as $sport)
                                <tr>
                                    <td scope="row">
                                        <img class="img-fluid" src="{{asset(config('constants.sport.path').'/'.$sport->icon)}}">
                                    </td>
        
                                    <td>
                                        {{$sport->name}}
                                    </td>

                                    <td>
                                        {{$sport->name_fr}}
                                    </td>

                                    <td>
                                        <a href="{{route('admin.edit.sport', $sport->id)}}" class="btn btn-primary btn-block">@lang('Edit')</a>
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
