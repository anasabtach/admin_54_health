@extends('admin.master')
@section('content')
    <section class="main-content">
        <div class="row">
            <div class="col-sm-12">
                @include('admin.flash-message')
                <div class="card">
                    <div class="card-header card-default">
                        Profile
                    </div>
                    <div class="card-body">
                        <form method="post" action="" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" value="{{ currentUser()->name }}" name="name" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" value="{{ currentUser()->email }}" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Mobile No</label>
                                <input type="text" value="{{ currentUser()->mobile_no }}" name="mobile_no" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Profile Picture</label>
                                <input type="file" name="image_url" class="form-control">
                                <input type="hidden" name="old_file" value="{{ currentUser()->image_url }}">
                                @if( !empty(currentUser()->image_url) )
                                    <div style="margin:10px 0;width:200px; height: 100px;">
                                        <img style="width:100%;height:100%;object-fit:contain;" src="{{ Storage::url(currentUser()->image_url) }}">
                                    </div>
                                @endif
                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @include('admin.footer')
    </section>
@endsection
