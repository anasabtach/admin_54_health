@extends('admin.master')
@section('content')
    <section class="main-content">
        <div class="row">
            <div class="col-sm-12">
                @include('admin.flash-message')
                <div class="card">
                    <div class="card-header card-default">
                        Change Password
                    </div>
                    <div class="card-body">
                        <form method="post" action="">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label>Current Password</label>
                                <input type="password" required name="current_password" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>New Password</label>
                                <input type="password" required name="new_password" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Confirm Password</label>
                                <input type="password" required name="confirm_password" class="form-control">
                            </div>
                            <div class="form-group">
                                <div class="buttons">
                                    <button class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @include('admin.footer')
    </section>
@endsection
