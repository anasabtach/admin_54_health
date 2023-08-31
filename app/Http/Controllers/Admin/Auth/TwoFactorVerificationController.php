<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TwoFactorVerificationController extends Controller
{   

    public function __construct(){
        $this->middleware('guest_two_factor_auth');
        // dd(Auth::user()->id());
        // if(auth()->user()->two_factor_code == null){
        //     return redirect()->route('admin.dashboard');
        // }
    }

    public function verificationCodeForm(){
        $email = env('MAIL_FROM_ADDRESS');
        $parts = explode('@', $email);
        $maskedUsername = substr($parts[0], 0, 4) . str_repeat('*', strlen($parts[0]) - 4);
        $maskedEmail = $maskedUsername . '@' . $parts[1];
        
        return $this->__cbAdminView('auth.two_factor_verification',['masked_email'=>$maskedEmail]);
    }

    public function twoFactorVerificaiton(Request $request){
        $validator = Validator::make($request->all(), [
            'code' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        if(auth()->user()->two_factor_code == $request->code){
            $user = auth()->user();
            $user->two_factor_code = null;
            $user->save();
            return redirect()->route('admin.dashboard');
        }
        return redirect()->back()->with('error','Invalid code');
    }

    public function resendVerificationCode(){//resend verification code on email
        $this->generateVerificationCode();

        return redirect()->back()->with('success','Verification code has been sent on your email');
    }
}
