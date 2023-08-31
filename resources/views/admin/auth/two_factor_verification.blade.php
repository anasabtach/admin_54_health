@extends('admin.auth.master')
@section('content')
    <div class="col-5">
        @include('admin.flash-message')
        @include('admin.auth.header')
        <div class="misc-box">
            <form method="post" action="{{ route('admin.two_factor_verification') }}">
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="exampleInputPassword1">Verification code</label>
                    <div class="group-icon">
                        <input id="exampleInputPassword1" type="text" name="code" placeholder="code" class="form-control" pattern="[0-9]*" maxlength="4">
                        <span class="icon-lock text-muted icon-input"></span>
                    </div>
                </div>
                <div class="clearfix">
                    <div class="float-right">
                        <button type="submit" class="btn btn-block btn-primary btn-rounded box-shadow">Verify</button>
                    </div>
                </div>
                <hr>
                <p class="text-center">
                    <a href="{{ route('admin.resend_verification_code') }}">Resend Code?</a><br />
                    <a href="#" title="verification code has been sent over this email">Email : {{ $masked_email }}</a>
                </p>
            </form>
        </div>
        @include('admin.auth.footer')
    </div>
@endsection

