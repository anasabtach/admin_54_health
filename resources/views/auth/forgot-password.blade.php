@extends('auth.master')
@section('content')
<div class="col-12 col-md-7">
   <div class="forget-text">
      <h1 class="font-45">Forgot Password?</h1>
      <p class="font-20 font-222">We get it, stuff happens. Just enter your email address below associated with your account.</p>
      <div class="from-box forms-box">
         <div class="form__group">
            <input type="email" id="Email" class="form__field" placeholder="Your Email">
            <label for="Email" class="form__label">Email</label>
         </div>
         <div class="form-icon">
            <i class="fa-solid fa-envelope message-icon"></i>
         </div>
      </div>
      <div class="forget-btn">
         <button class="reset font-22" id="resetPassword">
            Reset Password
         </button>
      </div>
   </div>
</div>
@endsection
