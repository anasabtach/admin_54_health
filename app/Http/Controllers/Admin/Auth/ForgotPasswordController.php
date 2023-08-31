<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\ResetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    public function forgotPassword(Request $request)
    {
        if( $request->isMethod('post') )
            return self::_submitForgotPassword($request);

        return $this->__cbAdminView('auth.forgot-password');
    }

    private function _submitForgotPassword($request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
        ]);
        if ($validator->fails()) {
            return redirect()->withErrors($validator) ->withInput();
        }
        $resetPasswordRequest = ResetPassword::resetPassword($request['email']);
        if( $resetPasswordRequest ){
            $admin_url = route('admin.login') . '?auth_token=' . config('constants.ADMIN_AUTH_TOKEN');
            return redirect($admin_url)->with('success','Reset password link has been sent to your email address');
        }
        else
            return redirect()->back()->with('error','Invalid email address');
    }
}
