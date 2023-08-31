@extends('admin.auth.master')
@section('content')
    <div class="col-5">
        @include('admin.auth.header')
        @include('admin.flash-message')
        <div class="misc-box">
            <form method="post" role="form">
                {{ csrf_field() }}
                <div class="form-group">
                    <label  for="exampleuser1">New Password</label>
                    <div class="group-icon">
                        <input id="exampleuser1" type="password" name="new_password" placeholder="New Password" class="form-control" required="">
                        <span class="icon-lock text-muted icon-input"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label  for="exampleuser1">Confirm Password</label>
                    <div class="group-icon">
                        <input id="exampleuser1" type="password" name="confirm_password" placeholder="Confirm Password" class="form-control" required="">
                        <span class="icon-lock text-muted icon-input"></span>
                    </div>
                </div>
                <div class="clearfix">
                    <div class="float-right">
                        <button type="submit" class="btn btn-block btn-primary btn-rounded box-shadow">Submit</button>
                    </div>
                </div>
            </form>
        </div>
        @include('admin.auth.footer')
    </div>
@endsection

