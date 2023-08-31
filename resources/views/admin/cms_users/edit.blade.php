@extends('admin.master')
@section('content')
    <section class="main-content">
        <div class="row">
            <div class="col-sm-12">
                @include('admin.flash-message')
                <div class="card">
                    <div class="card-header card-default">
                        Edit Cms User
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('cms-users-management.update',['cms_users_management' => $record->slug]) }}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <input type="hidden" name="_method" value="PUT">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Name</label>
                                        <input type="text" required value="{{ $record->name }}" name="name" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" required name="email" value="{{ $record->email }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Profile Picture</label>
                                        <input type="file" name="image_url" class="form-control">
                                        <img style="width: 150px; height: 100px; object-fit: contain;" src="{{ (\Storage::exists($record->image_url)) ? \Storage::url($record->image_url) : \URL::to('images/user-placeholder.jpg'), }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Mobile No</label>
                                        <input type="text" required value="{{ $record->mobile_no }}" name="mobile_no" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>CMS privilege</label>
                                        <select name="user_group_id" class="form-control">
                                            <option required value="">-- Select Privilege --</option>
                                            @if( count($getCmsRole) )
                                                @foreach($getCmsRole as $cmsRole)
                                                    <option {{ $record->user_group_id == $cmsRole->id ? 'selected' : '' }} value="{{ $cmsRole->id }}">{{ $cmsRole->title }}</option>
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
