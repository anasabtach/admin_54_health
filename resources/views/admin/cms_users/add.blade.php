@extends('admin.master')
@section('content')
    <section class="main-content">
        <div class="row">
            <div class="col-sm-12">
                @include('admin.flash-message')
                <div class="card">
                    <div class="card-header card-default">
                        Add Cms User
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('cms-users-management.store') }}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <input type="hidden" name="user_type" value="admin">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Name</label>
                                        <input required type="text" value="" name="name" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input required type="email" name="email" value="" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Profile Picture</label>
                                        <input required type="file" name="image_url" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Mobile No</label>
                                        <input required type="text" value="" name="mobile_no" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Password</label>
                                        <input required type="password" value="" name="password" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Confirm Password</label>
                                        <input required type="password" value="" name="confirm_password" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>CMS privilege</label>
                                        <select name="user_group_id" class="form-control">
                                            <option required value="">-- Select Privilege --</option>
                                            @if( count($getCmsRole) )
                                                @foreach($getCmsRole as $cmsRole)
                                                    <option value="{{ $cmsRole->id }}">{{ $cmsRole->title }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
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
