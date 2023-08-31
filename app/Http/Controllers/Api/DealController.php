<?php
namespace App\Http\Controllers\Api;

use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Http\Controllers\RestController;
use App\Models\Deal;
use App\Libraries\Payment\Payment;
use Carbon\Carbon;

class DealController extends RestController
{
    private $_stripe;

    public function __construct(Request $request)
    {
        parent::__construct('Deal');
        $this->__request     = $request;
        $this->__apiResource = 'Deal';
        $this->_stripe = Payment::getInstance();
        $this->__success_store_message  = __('app.success_store_deal');
        $this->__success_update_message = __('app.success_update_deal');
        $this->__success_delete_message = __('app.success_delete_deal');
    }

    /**
     * This function is used for validate restfull request
     * @param $action
     * @param string $slug
     * @return array
     */
    public function validation($action,$slug=0)
    {
        $validator = [];
        $request = $this->__request;
        switch ($action){
            case 'POST':
                $validator = Validator::make($this->__request->all(), [
                    'name'       => [
                        'required',
                        'max:150'
                    ],
                    'image_url'  => 'required|image|max:5120',
                    'price_type' => 'in:special_price,off,free',
                    'price'      => [
                        'nullable',
                        Rule::requiredIf(function() use ($request){
                            return $request['price_type'] == 'free' ? false : true;
                         }),
                        'regex:/^(\d{1,5})(\.\d{1,2})?$/'
                    ],
                    'sale_price' => [
                        'nullable',
                        Rule::requiredIf(function() use ($request){
                            return $request['price_type'] == 'off' ? true : false;
                        }),
                        'regex:/^(\d{1,5})(\.\d{1,2})?$/'
                    ],
                    'discount_percentage' => 'nullable|numeric|digits_between:1,100',
                    'description' => [
                        'required',
                        'max:1000'
                    ],
                    'time_bound'    => 'required|in:ongoing,time_bound',
                    'start_date'    => ['nullable','date_format:Y-m-d'],
                    'end_date'      => ['nullable','date_format:Y-m-d','after_or_equal:start_date'],
                    'redeem_type'   => 'required|in:one_time,multiple',
                    'redeem_length' => 'numeric',
                    'deal_type'     => 'required|in:member,both',
                    'deal_code'     => [
                        'required',
                        Rule::unique('deals')->whereNull('deleted_at'),
                        'regex:/^([A-Za-z0-9])\w+$/'
                    ],
                    'how_to_redeem' => 'nullable|max:1000',
                ]);
                break;
            case 'PUT':
                $validator = Validator::make($this->__request->all(), [
                    'name'       => [
                        'max:150'
                    ],
                    'image_url'  => 'nullable|image|max:5120',
                    'price_type' => 'in:special_price,off,free',
                    'price'      => [
                        'nullable',
                         Rule::requiredIf(function() use ($request){
                            return empty( $request['price_type']) || $request['price_type'] == 'free' ? false : true;
                         }),
                        'regex:/^(\d{1,5})(\.\d{1,2})?$/'
                    ],
                    'sale_price' => [
                        'nullable',
                        Rule::requiredIf(function() use ($request){
                            return $request['price_type'] == 'off' ? true : false;
                        }),
                        'regex:/^(\d{1,5})(\.\d{1,2})?$/'
                    ],
                    'discount_percentage' => 'nullable|numeric|digits_between:1,100',
                    'description' => [
                        'max:1000',
                    ],
                    'time_bound'    => 'in:ongoing,time_bound',
                    'start_date'    => ['nullable','date_format:Y-m-d'],
                    'end_date'      => ['nullable','date_format:Y-m-d','after_or_equal:start_date'],
                    'redeem_type'   => 'in:one_time,multiple',
                    'redeem_length' => 'numeric',
                    'deal_type'     => 'in:member,both',
                    'deal_code'     => [
                        'nullable',
                        Rule::unique('deals')->whereNull('deleted_at')->ignore($slug,'slug'),
                        'regex:/^([A-Za-z0-9])\w+$/'
                    ],
                    'how_to_redeem' => 'nullable|max:1000',
                ]);
                break;
        }
        return $validator;
    }

    /**
     * @param $request
     */
    public function beforeIndexLoadModel($request)
    {

    }

    /**
     * @param $request
     * @param $record
     */
    public function afterIndexLoadModel($request,$record)
    {

    }

    /**
     * @param $request
     */
    public function beforeStoreLoadModel($request)
    {
        if( $request['price_type'] == 'off' ){
            if( empty($request['discount_percentage']) ){
                $this->__is_error = true;
                return $this->__sendError('Validation Message',['message' => 'The discount percentage must be between 1 and 100 digits' ]);
            }
        }

        if( !empty($request['sale_price']) ){
            if( $request['sale_price'] >= $request['price'] ){
                $this->__is_error = true;
                return $this->__sendError('Validation Message',['message' => 'Price should be greater than the sale price' ]);
            }
        }
        if( !empty($request['card_token']) ){
            $email     = $request['user']->email;
            $deal_name = $request['name'];
            $response  = $this->_stripe->directCharge(
                $request['card_token'],
                env('PROMOTE_DEAL_AMOUNT'),
                "('$email') created a promotion deal ('$deal_name')"
            );
            if( $response['code'] != 200 ){
                $this->__is_error = true;
                return $this->__sendError('Gateway Error',['message' => $response['message']],400);
            }
            $request['charge_amount'] = env('PROMOTE_DEAL_AMOUNT');
            $request['paid_promotion'] = '1';
            $request['paid_pormotion_expire_date'] = Carbon::now()->addMonths(1);
            $request['gateway_transaction_id'] = $response['data']['transaction_id'];
        }
    }

    /**
     * @param $request
     * @param $record
     */
    public function afterStoreLoadModel($request,$record)
    {

    }

    /**
     * @param $request
     * @param $slug
     */
    public function beforeShowLoadModel($request,$slug)
    {

    }

    /**
     * @param $request
     * @param $record
     */
    public function afterShowLoadModel($request,$record)
    {

    }

    /**
     * @param $request
     * @param $slug
     */
    public function beforeUpdateLoadModel($request,$slug)
    {
        if( $request['price_type'] == 'off' ){
            if( empty($request['discount_percentage']) ){
                $this->__is_error = true;
                return $this->__sendError('Validation Message',['message' => 'The discount percentage must be between 1 and 100 digits' ]);
            }
        }
        if( !empty($request['sale_price']) ){
            if( $request['sale_price'] >= $request['price'] ){
                $this->__is_error = true;
                return $this->__sendError('Validation Message',['message' => 'Price should be greater than the sale price' ]);
            }
        }
        $record = Deal::getDealBySlug($slug);
        if( $record->user_id != $request['user']->id ){
            $this->__is_error = true;
            return $this->__sendError('Validation Message',['message' => 'You are not authorized to process this request'],400);
        }
    }

    /**
     * @param $request
     * @param $record
     */
    public function afterUpdateLoadModel($request,$record)
    {

    }

    /**
     * @param $request
     * @param $slug
     */
    public function beforeDestroyLoadModel($request,$slug)
    {
        $record = Deal::getDealBySlug($slug);
        if( $record->user_id != $request['user']->id ){
            $this->__is_error = true;
            return $this->__sendError('Validation Message',['message' => 'You are not authorized to process this request'],400);
        }
    }

    /**
     * @param $request
     * @param $slug
     */
    public function afterDestroyLoadModel($request,$slug)
    {

    }

    public function favouriteDeal()
    {
        $request = $this->__request;
        $param_rule['deal_id'] = 'required|numeric';

        $response = $this->__validateRequestParams($request->all(),$param_rule);
        if( $this->__is_error )
            return $response;

        $record = Deal::addOrRemoveFavourite($request->all());

        $this->__is_paginate   = false;
        $this->__is_collection = false;

        return $this->__sendResponse($record,200,__('app.success_update_message'));
    }

    public function dealRedeem()
    {
        $current_date = date('Y-m-d');
        $request = $this->__request;
        $param_rule['deal_id']     = 'required|numeric';
        $param_rule['redeem_code'] = 'required';

        $response = $this->__validateRequestParams($request->all(),$param_rule);
        if( $this->__is_error )
            return $response;

        $deal = Deal::find($request['deal_id']);
        if( !isset($deal->id) ){
            return $this->__sendError('Validation Message',['message' => 'Invalid deal id' ]);
        }
        if( $deal->status != 1 ){
            return $this->__sendError('Validation Message',['message' => 'Sorry, the deal has been deactivated.' ]);
        }
        //check subscription
        if( $request['user']->user_group_id == 3 && empty($request['user']->subscription_expiry_date) ){
            return $this->__sendError('Validation Message',['message' => 'Please buy a subscription plan' ]);
        }
        if(  $request['user']->user_group_id == 3 && strtotime($current_date) > strtotime($request['user']->subscription_expiry_date) ){
            return $this->__sendError('Validation Message',['message' => 'Your subscription has been expired. Please upgrade your plan' ]);
        }

        //check deal type
        if( $deal->deal_type == 'business_user' && $request['user']->user_group_id != 4 ){
            return $this->__sendError('Validation Message',['message' => 'you can not redeem this deal cause this deal is valid for business user' ]);
        }

        if( $deal->deal_type == 'member' && $request['user']->user_group_id!= 3 ){
            return $this->__sendError('Validation Message',['message' => 'you can not redeem this deal cause this deal is valid for member' ]);
        }

        //check deal expiry
        if( $deal->time_bound == 'time_bound' ){
            if( strtotime($current_date) < strtotime($deal->start_date) ){
                return $this->__sendError('Validation Message',['message' => 'The deal is not started yet' ]);
            }
            if( strtotime($current_date) > strtotime($deal->end_date) ){
                return $this->__sendError('Validation Message',['message' => 'Deal has been expired' ]);
            }
        }
        //check deal redeem before
        if( $deal->redeem_type == 'one_time' ){
            $checkDealRedeem = Deal::checkDealRedeem($request['deal_id'],$request['user']->id);
            if( $checkDealRedeem ){
                return $this->__sendError('Validation Message',['message' => 'You cannot redeem this deal more than once' ]);
            }
        }
        //check redeem code
        // if( $deal->deal_code != $request['redeem_code'] ){
        //     return $this->__sendError('Validation Message',['message' => 'The redeeemable code you entered is not correct. Please try again!' ]);
        // }
        //insert deal redeem data
        Deal::dealRedeem($request->all(),$deal->paid_promotion);

        $this->__is_paginate = false;
        $this->__collection  = false;

        return $this->__sendResponse([],200,'Deal has been redeemed successfully');
    }
}
