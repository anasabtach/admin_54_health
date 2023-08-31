<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\GeneralController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DealController;
use App\Http\Controllers\Api\UserRatingController;
use App\Http\Controllers\Api\SubscriptionController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::middleware(['api_authorization'])->group(function(){

    Route::post('generate-secret',[GeneralController::class,'generateSecret']);

    Route::post('contact-us',[GeneralController::class,'contactUs']);

    Route::get('country',[GeneralController::class,'getCountry']);
    Route::get('state',[GeneralController::class,'getState']);
    Route::get('city',[GeneralController::class,'getCity']);

    Route::get('quote',[GeneralController::class,'getQuote']);

    Route::get('content',[GeneralController::class,'getContent']);

    Route::resource('category',CategoryController::class)->only('index');

    Route::get('packages',[SubscriptionController::class,'getPackges']);

    Route::get('vendors',[UserController::class,'vendors']);
    Route::get('vendor/rating',[UserController::class,'vendorRating']);
    Route::get('vendor/deals',[UserController::class,'vendorDeals']);
    Route::get('vendor/related-deals',[UserController::class,'vendorRelatedDeals']);
    Route::get('vendor/{name}',[UserController::class,'vendor']);
    Route::get('vendor/deal/{name}',[UserController::class,'vendorDeal']);

    Route::post('user/login',[UserController::class,'login']);
    Route::post('user/forgot-password',[UserController::class,'forgotPassword']);
    Route::post('user/change-password',[UserController::class,'changePassword']);
    Route::post('user/logout',[UserController::class,'userLogout']);
    Route::post('user/social-login',[UserController::class,'socialLogin']);
    Route::post('user/verify-code',[UserController::class,'verifyCode']);
    Route::post('user/resend-code',[UserController::class,'resendCode']);
    Route::resource('user',UserController::class)->except(['delete']);

    Route::resource('faq',FaqController::class)->only('index');

    Route::post('truncate-data',[GeneralController::class,'truncateData']);

    Route::middleware(['custom_auth:api'])->group(function(){

        Route::get('user-invite',[UserController::class,'userInvite']);

        Route::get('statistics',[UserController::class,'getStatistics']);

        Route::post('deal/redeem',[DealController::class,'dealRedeem']);
        Route::resource('deal',DealController::class);

        Route::resource('rating', UserRatingController::class);

        Route::post('favourite',[DealController::class,'favouriteDeal']);

        Route::get('subscription/history',[SubscriptionController::class,'subscriptionHistory']);
        Route::post('subscription/buy',[SubscriptionController::class,'buySubscription']);

        Route::get('notification',[NotificationController::class,'index']);
        Route::put('notification/{any}',[NotificationController::class,'update']);
        Route::post('notification/send',[NotificationController::class,'sendNotification']);
        Route::post('notification/setting',[NotificationController::class,'saveNotificationSetting']);
        Route::get('notification/setting',[NotificationController::class,'getNotificationSetting']);

    });
});
