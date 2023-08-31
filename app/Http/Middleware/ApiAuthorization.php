<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Http\Request;

class ApiAuthorization
{
    /**
     * The authentication factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth , $request;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth, Request $request)
    {
        $this->auth    = $auth;
        $this->request = $request;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $token = $this->request->header('token');
        if( empty($token) ){
            return response()->json([
                'code'    => 401,
                'message' => 'Unauthorized',
                'data'    => [ 'auth' => __('app.authorize_header_invalid') ]
            ],401);
        }
        $key_file = config('constants.AES_SECRET');
        $iv_file  = config('constants.AES_IV');
        $token = openssl_decrypt($token, 'aes-256-cbc', $key_file,0,$iv_file);
        if( empty($token) ){
            return response()->json([
                'code'    => 401,
                'message' => 'Unauthorized',
                'data'    => [ 'auth' => __('app.authorize_header_invalid') ]
            ],401);
        }
        if( $token != config('constants.CLIENT_ID') ){
            return response()->json([
                'code'    => 401,
                'message' => 'Unauthorized',
                'data'    => [ 'auth' => __('app.authorize_header_invalid') ]
            ],401);
        }
        $request['token'] = $token;
        return $next($request);
    }

}
