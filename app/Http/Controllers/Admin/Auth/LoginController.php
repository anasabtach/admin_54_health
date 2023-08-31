<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class LoginController extends Controller
{
    public function login(Request $request)
    {   
        if( $request['auth_token'] != config('constants.ADMIN_AUTH_TOKEN') )
            return abort(404);

        if( $request->isMethod('post') )
            return self::_login($request);

        return $this->__cbAdminView('auth.login');
    }

    private function _login($request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|max:255',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $auth = User::auth('admin',$request['email'],$request['password']);
        if( $auth ){
            $this->generateVerificationCode();
            return redirect()->route('admin.dashboard');
        }else{
            return redirect()->back()->with('error','Invalid credential');
        }
    }


}
