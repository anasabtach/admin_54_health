@extends('admin.auth.master')
@section('content')
    <div class="col-5">
        @include('admin.auth.header')
        @include('admin.flash-message')
        <div class="misc-box">
            <form method="post" role="form">
                {{ csrf_field() }}
                <div class="form-group">
                    <label  for="exampleuser1">Email</label>
                    <div class="group-icon">
                        <input id="exampleuser1" type="email" name="email" placeholder="Email" class="form-control" required="">
                        <span class="icon-user text-muted icon-input"></span>
                    </div>
                </div>
                <div class="clearfix">
                    <div class="float-right">
                        <button type="submit" class="btn btn-block btn-primary btn-rounded box-shadow">Submit</button>
                    </div>
                </div>
                <hr>
                <p class="text-center">
                    <a href="{{ route('admin.login') . '?auth_token=' . config('constants.ADMIN_AUTH_TOKEN') }}">Back To Login</a>
                </p>
            </form>
        </div>
        @include('admin.auth.footer')
    </div>
@endsection

