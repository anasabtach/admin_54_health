@extends('auth.master')
@section('content')
<div class="col-12 col-md-7">
   <div class="login-text-box">
      <div class="login-text">
         <p class="font-20">Login to your existing account</p>
         <h1 class="font-45">Welcome to <br> Five Four Health</h1>
      </div>
      <div class="from-box forms-box">
         <form action="">
            <div class="form__group">
               <input type="email" id="Email" class="form__field" placeholder="Your Email">
               <label for="Email" class="form__label">Email</label>
            </div>
         </form>
         <div class="form-icon">
            <i class="fa-solid fa-envelope message-icon "></i>
         </div>
      </div>
      <div class="from-box">
         <div class="form__group">
            <input type="password" id="loginPassword" class="form__field" placeholder="Your Email">
            <label for="loginPassword" class="form__label">Password</label>
         </div>
         <div class="form-icon">
            <i class="fa-solid fa-eye-slash hide-icon toggle-password" toggle="#loginPassword"></i>
         </div>
      </div>
      <div class="form-check">
         <input class="form-check-input" type="checkbox" value="" id="Remember">
         <label class="form-check-label font-20 ms-1 text-1a1a1a" for="Remember">
         Remember me
         </label>
      </div>
      <div class="sign-btns">
         <button class="sign-btn font-22" id="signIn">
         Sign in
         </button>
      </div>
      <div class="forget-pass text-center">
         <p>
            <a href="{{ URL::to('forgot-passwprd') }}" class="font-18 become">Forgot Password?</a>
         </p>
         <p>
            <a href="{{ URL::to('become-member') }}" class="font-18 forget">Become a Member</a>
         </p>
      </div>
   </div>
</div>
@endsection
