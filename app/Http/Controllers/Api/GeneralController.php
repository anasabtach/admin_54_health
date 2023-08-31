<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use FFMpeg;

class GeneralController extends Controller
{
    public function generateSecret(Request $request)
    {
        $data['secret'] = bin2hex(openssl_random_pseudo_bytes(16));
        $data['iv']     = bin2hex(openssl_random_pseudo_bytes(16));

        file_put_contents(resource_path('secret-key/secret.txt'),$data['secret']);
        file_put_contents(resource_path('secret-key/iv.txt'),$data['iv']);

        $this->__collection  = false;
        $this->__is_paginate = false;

        return $this->__sendResponse($data, 200, 'Secret has been generated successfully');
    }

    public function getCountry(Request $request)
    {
        $query = \DB::table('country');
        if( !empty($request['name']) ){
            $name = $request['name'];
            $query->where('name','like',"%$name%");
        }
        $records = $query->orderBy('name','asc')->get();

        $this->__collection  = false;
        $this->__is_paginate = false;
        return $this->__sendResponse($records, 200, 'Countries retrieved successfully');
    }

    public function getState(Request $request)
    {
        $params = $request->all();
        $param_rule['country_id'] = 'required|numeric';

        $response = $this->__validateRequestParams($params,$param_rule);
        if( $this->__is_error )
            return $response;

        $query = \DB::table('state')->where('country_id',$request['country_id']);
        if( !empty($request['name']) ){
            $name = $request['name'];
            $query->where('name','like',"%$name%");
        }
        $records = $query->orderBy('name','asc')->get();

        $this->__collection  = false;
        $this->__is_paginate = false;
        return $this->__sendResponse($records, 200, 'States retrieved successfully');
    }

    public function getCity(Request $request)
    {
        $params = $request->all();
        $param_rule['state_id'] = 'required|numeric';

        $response = $this->__validateRequestParams($params,$param_rule);
        if( $this->__is_error )
            return $response;

        $query = \DB::table('city')->where('state_id',$request['state_id']);
        if( !empty($request['name']) ){
            $name = $request['name'];
            $query->where('name','like',"%$name%");
        }
        $records = $query->orderBy('name','asc')->get();

        $this->__collection  = false;
        $this->__is_paginate = false;
        return $this->__sendResponse($records, 200, 'Cities retrieved successfully');
    }

    public function getQuote(Request $request)
    {
        $record = \DB::table('quotes')->select('description')->first();

        $this->__collection  = false;
        $this->__is_paginate = false;
        return $this->__sendResponse($record, 200, 'Quote retrieved successfully');
    }

    public function getContent()
    {
        $data = [];
        $records = \DB::table('content_management')
                        ->whereNull('deleted_at')
                        ->where('status','1')
                        ->get();
        if( count($records) ){
            foreach( $records as $record ){
                $data[$record->slug] = $record->content;
            }
        }

        $this->__collection  = false;
        $this->__is_paginate = false;
        return $this->__sendResponse($data, 200, 'Content retrieved successfully');
    }

    public function generateVideoThumb(Request $request)
    {
        $file = $request->file('file');
        generateVideoThumb($file->getPathName(),public_path('image.jpg'));
        echo 'success'; exit;
    }

    public function contactUs(Request $request)
    {
        $params = $request->all();
        $param_rule['name'] = 'required|min:3|max:50';
        $param_rule['email'] = 'required|email|max:50';
        $param_rule['message'] = 'required|string|min:25|max:1000';

        $response = $this->__validateRequestParams($params,$param_rule);
        if( $this->__is_error )
            return $response;

        $mail_param['NAME']     = $params['name'];
        $mail_param['EMAIL']    = $params['email'];
        $mail_param['MESSAGE']  = $params['message'];
        $mail_param['YEAR']     = date('Y');
        $mail_param['APP_NAME'] = appSetting('application_setting','application_name');
        sendMail(env('ADMIN_EMAIL'),'contact-us',$mail_param);

        $this->__collection  = false;
        $this->__is_paginate = false;
        return $this->__sendResponse([], 200, 'Your query has been submitted successfully');
    }

    public function truncateData(Request $request)
    {
        if( \App::environment('production') )
            return abort(404);

        if( $request['password'] != 'admin@!@#' )
            return $this->__sendError('Validation Message',['message' => 'Invalid Credential' ], 400);

        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        \DB::table('deal_redeem')->truncate();
        \DB::table('deals')->truncate();
        \DB::table('favourites')->truncate();
        \DB::table('media')->truncate();
        \DB::table('notification_setting')->truncate();
        \DB::table('notifications')->truncate();
        \DB::table('personal_access_tokens')->truncate();
        \DB::table('reset_password')->truncate();
        \DB::table('user_api_token')->truncate();
        \DB::table('user_deal_transaction')->truncate();
        \DB::table('user_invites')->truncate();
        \DB::table('user_packages')->truncate();
        \DB::table('user_rating')->truncate();
        \DB::table('users')->whereNotIn('user_group_id',[1,2])->delete();

        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->__is_paginate = false;
        $this->__collection  = false;

        return $this->__sendResponse([],200, 'Data truncate successfully');
    }
}
