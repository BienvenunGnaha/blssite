@extends('admin.layouts.app')

@section('panel')
<div class="row">

    <div class="col-lg-12">
        <div class="card">
            <form action="{{ route('admin.frontend.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="key" value="rgpd.post">
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Post Title</label>
                                <input type="text" class="form-control" placeholder="Your Post Title" name="title" requierd/>
                            </div>
                            
                            <div class="form-group">
                                <label>Post Content</label>
                                <textarea rows="10" class="form-control nicEdit" placeholder="Post description" name="body" requierd></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="form-row justify-content-center">
                        <div class="form-group col-md-12">
                            <button type="submit" class="btn btn-block btn-primary mr-2">Post</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
<a href="{{ route('admin.frontend.blog.index') }}" class="btn btn-dark" ><i class="fa fa-fw fa-reply"></i>Back</a> 
@endpush