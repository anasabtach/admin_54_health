<?php

namespace App\Http\Controllers\Api;

use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\UserPackage;
use App\Models\User;
use App\Libraries\Payment\Payment;
use Carbon\Carbon;
use Stripe\Stripe;
use Stripe\Subscription;
use Stripe\Customer;

class SubscriptionController extends Controller
{
    public function __construct(Request $request)
    {
        $this->__request      = $request;
        $this->__apiResource  = 'Package';
        $this->_stripe = Payment::getInstance();
    }

    public function getPackges()
    {
        $records = Package::all();

        $this->__is_paginate = false;
        $this->__collection  = false;

        return $this->__sendResponse($records,200,__('app.success_listing_message'));
    }

    public function buySubscription()
    {
        $request = $this->__request;
        $param_rule['package_id'] = 'required|numeric';
        $param_rule['card_token'] = 'required';

     
        $response = $this->__validateRequestParams($request->all(),$param_rule);
        if( $this->__is_error )
            return $response;

        $package = Package::find($request['package_id']);
        if( !isset($package->id) ){
            return $this->__sendError('Validation Message',['message' => 'Invalid package id'],400);
        }

        //check subscription
        $checkUserSubscription = UserPackage::getActiveSubscription($request['user']->id);
        if( isset($checkUserSubscription->id) && $checkUserSubscription->package_id == $request['package_id'] ){
            return $this->__sendError('Validation Error',['message' => 'You have already subscribed to this package' ],400);
        }
        //gateway charge
        // $response = $this->_stripe->directCharge(
        //     $request['card_token'],
        //     $package->amount,
        //     $request['user']->email . ' has purchased a package #'.$package->title
        // );

        
        Stripe::setApiKey(env('STRIPE_SECRET'));
        
        $token = $request->input('card_token');
        // $user = auth()->user(); // Assuming you have user authentication
    
        // Create or update a Stripe customer
        $customer = Customer::create([
            'email' => $request['user']->email,
            'source' => $token,
        ]);
    
        // Create a subscription
        $subscription = Subscription::create([
            'customer' => $customer->id,
            'items' => [
                ['price' => 'plan_OSjvuIYxNc1ZOf'], // Replace with the actual plan ID
            ],
        ]);

        $expire_date = date('Y-m-d H:i:s', $subscription->current_period_end);

        // return $subscription;

        // if( $response['code'] != 200 ){
        //     return $this->__sendError('Validation Message',['message' => $response['message']],400);
        // }

        // $current_date = date('Y-m-d');
        // if( !empty($request['user']->subscription_expiry_date) && strtotime($request['user']->subscription_expiry_date) > strtotime($current_date) ){
        //     $current_date = $request['user']->subscription_expiry_date;
        // }
        // //save data
        // if( $package->duration_unit == 'day' ){
        //     $expire_date = Carbon::parse($current_date)->addDays($package->duration)->format('Y-m-d');
        // } elseif( $package->duration_unit == 'week' ){
        //     $expire_date = Carbon::parse($current_date)->addWeeks($package->duration)->format('Y-m-d');
        // } elseif( $package->duration_unit == 'month' ){
        //     $expire_date = Carbon::parse($current_date)->addMonth($package->duration)->format('Y-m-d');
        // } else {
        //     $expire_date = Carbon::parse($current_date)->addYears($package->duration)->format('Y-m-d');
        // }
        $records = UserPackage::create([
            'gateway_original_transaction_id' => '0',
            'gateway_transaction_id' => $subscription->id,
            'gateway'       => env('GATEWAY_TYPE'),
            'user_id'       => $request['user']->id,
            'package_id'    => $package->id,
            'charge_amount' => $package->amount,
            'expiry_date'   => $expire_date,
            'trial_period'  => '0',
            'device_type'   => 'web',
            'ip_address'    => $request->ip(),
            'created_at'    => Carbon::now()
        ]);
        //update user
        User::where('id',$request['user']->id)->update(['subscription_expiry_date' => $expire_date]);
        //get user
        $user = User::getUserByApiToken($request['api_token']);
        // $user->subscription_expiry_date = $expire_date;

        $this->__apiResource   = 'Auth';
        $this->__is_collection = false;
        $this->__is_paginate   = false;

        return $this->__sendResponse($user,200,'Subscription package has been purchased successfully');
    }

    public function subscriptionHistory()
    {
        $request = $this->__request;

        $records = UserPackage::getUserPackageHistory($request['user']->id);

        $this->__apiResource   = 'UserPackage';
        return $this->__sendResponse($records,200,'Subscription history retrieved successfully');
    }
}
