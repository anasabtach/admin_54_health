<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\ResetPassword;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ResetPasswordController extends Controller
{
    public function resetPassword(Request $request,$token)
    {
        if( $request->isMethod('post') )
            return self::_resetPassword($request,$token);

        $checkRequest = ResetPassword::getRequest($token);
        if( !isset($checkRequest->id) )
            return redirect()->route('admin.login')->with('error','Invalid request');

        $requestTime = $checkRequest->created_at->addHour();
        if( strtotime(Carbon::now()) > strtotime($requestTime) )
            return redirect()->route('admin.login')->with('error','Reset password link has been expired.');

        return $this->__cbAdminView('auth.reset-password');
    }

    private function _resetPassword($request,$token)
    {
        $validator = Validator::make($request->all(), [
            'new_password'     => 'required|min:6|max:200',
            'confirm_password' => 'required|same:new_password',
        ]);
        if ($validator->fails()) {
            return redirect()->withErrors($validator)->withInput();
        }
        $checkRequest = ResetPassword::getRequest($token);
        if( !isset($checkRequest->id) )
            return redirect()->route('admin.login')->with('error','Invalid request');

        $requestTime = $checkRequest->created_at->addHour();
        if( strtotime(Carbon::now()) > strtotime($requestTime) )
            return redirect()->route('admin.login')->with('error','Reset password link has been expired.');

        ResetPassword::updateResetPassword($request['new_password'],$token);
        return redirect()->route('admin.login')->with('success','Password has been updated successfully');
    }
}
