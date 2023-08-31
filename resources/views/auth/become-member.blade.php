@extends('auth.master')
@section('content')
<div class="col-12 col-md-6 col-lg-7 col-xxl-8">
   <div class="sing-text become-a-member">
      <h1 class="font-50">Become a Member</h1>
      <div class="row">
         <div class="col-12 col-md-12 col-lg-6">
            <div class="from-box forms-box">
               <div class="form__group">
                  <input type="email" id="email" class="form__field" placeholder="Your Email">
                  <label for="email" class="form__label">Your Email <span class="tetxt-red">*</span></label>
               </div>
            </div>
         </div>
         <div class="col-12 col-md-12 col-lg-6">
            <div class="from-box forms-box">
               <div class="form__group">
                  <input type="number" id="phone" class="form__field" placeholder="Your Email">
                  <label for="phone" class="form__label">Phone Number <span class="tetxt-red">*</span></label>
               </div>
            </div>
         </div>
         <div class="col-12 col-md-12 col-lg-6">
            <div class="from-box forms-box">
               <div class="form__group">
                  <input type="text" id="Profession" class="form__field" placeholder="Your Email">
                  <label for="Profession" class="form__label">Profession <span class="tetxt-red">*</span></label>
               </div>
            </div>
         </div>
         <div class="col-12 col-md-12 col-lg-6">
            <div class="from-box forms-box">
               <div class="form__group">
                  <input type="text" id="ProfessionEmail" class="form__field" placeholder="Your Email">
                  <label for="ProfessionEmail" class="form__label">Personal Email Address<span class="tetxt-red">*</span></label>
               </div>
            </div>
         </div>
         <div class="col-12 col-md-12 col-lg-6">
            <div class="from-box forms-box">
               <div class="form__group">
                  <input type="text" id="Professionemail" class="form__field" placeholder="Your Email">
                  <label for="Professionemail" class="form__label">Work Email Address<span class="tetxt-red">*</span></label>
               </div>
            </div>
         </div>
         <div class="col-12 col-md-12 mb-5 col-lg-6">
            <p class="font-20 pb-3">Official Photo ID / Work ID<span class="tetxt-red">*</span></p>
            <div class="img-text d-flex align-items-center">
               <div class="img">
                  <img src="{{ URL::to('frontend/assets/img/gallery.png') }}" alt="gallery" class="img-fluid" >
               </div>
               <p class="ms-2">Upload</p>
            </div>
         </div>
      </div>
      <div class="row">
         <div class="col-12 col-md-12 col-lg-6">
            <div class="from-box">
               <div class="form__group">
                  <input type="password" id="PasswordSing" class="form__field" placeholder="Your Email">
                  <label for="PasswordSing" class="form__label">Password<span class="tetxt-red">*</span></label>
               </div>
               <div class="form-icon">
                  <i class="fa-solid fa-eye hide-icon toggle-password" toggle="#PasswordSing"></i>
               </div>
            </div>
         </div>
         <div class="col-12 col-md-12 col-lg-6">
            <div class="from-box">
               <div class="form__group">
                  <input type="password" id="PasswordConfrim" class="form__field" placeholder="Your Email">
                  <label for="PasswordConfrim" class="form__label">Confirm Password<span class="tetxt-red">*</span></label>
               </div>
               <div class="form-icon">
                  <i class="fa-solid fa-eye hide-icon toggle-password" toggle="#PasswordConfrim"></i>
               </div>
            </div>
         </div>
      </div>
      <div class="row pt-50">
         <div class="col-12 col-md-12 col-lg-6">
            <p class="account font-18">
               <span>  Already have an account? </span>
               <a href="{{ URL::to('login') }}"><span class="text-b28b37">Sign in</span></a>
            </p>
         </div>
         <div class="col-12 col-md-12 col-lg-6">
            <div class="submit-button ">
               <button class="submit-btn font-22" id="formSubmit">Submit Membership</button>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection
