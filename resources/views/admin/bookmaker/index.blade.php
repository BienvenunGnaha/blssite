@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-12 d-flex justify-content-end mt-3 mb-3"> <a class="btn btn-primaryE" href="{{route('admin.bookmakers.create')}}">Ajouter un bookmaker</a></div>
        <div class="col-lg-12">
            <div class="card">
                <div class="table-responsive table-responsive-xl">
                    <table class="table align-items-center table-light">
                        <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Icon</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            @foreach($bms as $bm)
                                <tr>
                                    <td>
                                        {{$bm->name}}
                                    </td>

                                    <td>
                                        <img class="img-fluid" src="{{asset(config('constants.bookmaker.path').'/'.$bm->photo)}}">
                                    </td>

                                    <td>
                                        <a href="{{route('admin.bookmakers.edit', $bm->id)}}" class="btn btn-primary btn-block">@lang('Edit')</a>
                                        <a href="{{route('admin.bookmakers.destroy', $bm->id)}}" class="btn btn-danger btn-block">@lang('Delete')</a>
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