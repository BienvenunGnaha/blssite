@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-12 d-flex justify-content-end mt-3 mb-3"> <a class="btn btn-primaryE" href="{{route('admin.create.pronostic')}}">Ajouter pronostic</a></div>
        <div class="col-lg-12">
            <div class="card">
                <div class="table-responsive table-responsive-xl">
                    <table class="table align-items-center table-light">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Type</th>
                                <th scope="col">Create Date</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            @foreach($pronos as $prono)
                                @php
                                    $details = json_decode($prono->details, true)
                                @endphp
                                <tr>
                                    <td scope="row">
                                        {{$prono->id}}
                                    </td>
                                        
                                    <td>
                                        @foreach ($details as $detail)
                                            @php
                                                $sp = \App\Sport::find((int)$detail['sport']);
                                            @endphp
                                            @if ($sp)
                                                <div><img width="40px" src="{{asset(config('constants.sport.path').'/'.$sp->icon)}}"><span>{{$detail['team1']}} / {{$detail['team2']}}</span></div>
                                            @else
                                                <div><span>{{$detail['team1']}} / {{$detail['team2']}}</span></div>
                                            @endif
                                        @endforeach
                                        
                                    </td>

                                    <td>
                                        {{$prono->created_at}}
                                    </td>
                                    <td>
                                        <a href="{{route('admin.edit.pronostic', $prono->id)}}" class="btn btn-primary btn-block">@lang('Edit')</a>
                                        <form action="{{route('admin.destroy.pronostic', $prono->id)}}" method="post" class="mt-1">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <input type="hidden" name="_method" value="delete">
                                            <button type="submit" class="btn btn-danger btn-block">@lang('Delete')</button>
                                        </form>
                                    </td>
                                </tr>
                        
                            @endforeach
                        </tbody>
                        <tfoot>
                            {{ $pronos->links() }}
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
