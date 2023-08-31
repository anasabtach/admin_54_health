<?php

namespace App\Http\Controllers;

use App\Models\ResetPassword;
use App\Models\User;
use App\Models\UserApiToken;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;

class UserController extends Controller
{
    public function resetPassword(Request $request, $token)
    {
        $checkRequest = ResetPassword::getUserRequest($token);
        if( !isset($checkRequest->id) )
            return redirect('/')->with('error',__('app.invalid_new_password_link'));

        if( strtotime(Carbon::now()) > strtotime(Carbon::make($checkRequest->created_at)->addHour()) )
            return redirect('/')->with('error',__('app.invalid_new_password_link'));

        if( $request->isMethod('post') )
            return self::_submitResetPassword($request,$checkRequest);

        return view('reset-password');
    }

    public function _submitResetPassword($request, $reset_pass_record)
    {
        $custom_messages = [
            'new_password.regex' => __('app.password_regex')
        ];
        $validator = Validator::make($request->all(), [
            'new_password'     => [
                'required',
                'regex:/^(?=.*[A-Z])(?=.*[!@#$&*])(?=.*[0-9])(?=.*[a-z]).{8,150}$/'
            ],
            'confirm_password' => 'required|same:new_password',
        ],$custom_messages);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator) ->withInput();
        }
        //update new password
        User::updateUserByEmail($reset_pass_record->email,['password' => Hash::make($request['new_password'])]);
        //delete reset password request
        $reset_pass_record->forceDelete();
        //delete all api token
        UserApiToken::where('user_id',$reset_pass_record->user_id)->forceDelete();

        return redirect('/')->with('success',__('app.password_success_msg'));
    }

    public function verifyEmail($token)
    {
        $email = decrypt($token);
        $user = User::getUserByEmail($email);
        if( !isset($user->id) )
            return redirect('/');

        User::updateUser($user->id,['is_email_verify' => 1, 'email_verify_at' => Carbon::now()]);
        if( $user->user_group_id == 4 ){ //busineess user
            return \Redirect::away(env('BUSINESS_PANEL_URL') . '/login?email-verify=true');
        } else {
            return \Redirect::away(env('USER_PANEL_URL') . '/login?email-verify=true');
        }
    }

    public function getContent($slug)
    {
        $data['content'] = \DB::table('content_management')
                                ->where('slug',$slug)
                                ->first();
        return view('content',$data);
    }
}
