<?php

use App\Http\Controllers\Admin\ContentManagementController;
use App\Http\Controllers\Admin\FaqController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CmsUserController;
use App\Http\Controllers\Admin\CmsRoleController;
use App\Http\Controllers\Admin\ApplicationSettingController;
use App\Http\Controllers\Admin\Auth\TwoFactorVerificationController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\QuoteController;
/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
*/
Route::prefix('admin')->group(function () {

    Route::middleware(['guest:web'])->group( function(){

        Route::match( ['get','post'],'login', [ LoginController::class,'login' ])->name('admin.login');
        Route::match( ['get','post'],'forgot-password', [ ForgotPasswordController::class,'forgotPassword' ])->name('admin.forgot-password');
        Route::match( ['get','post'],'reset-password/{any}', [ ResetPasswordController::class,'resetPassword' ])->name('admin.reset-password');

    });

    Route::middleware(['custom_auth:web'])->group( function(){
        
        Route::get('/two-factor-verification', [TwoFactorVerificationController::class, 'verificationCodeForm'])->name('admin.two_factor_verification');
        Route::post('/two-factor-verification', [TwoFactorVerificationController::class, 'twoFactorVerificaiton'])->name('admin.two_factor_verification');
        Route::get('/resend-verification-code', [TwoFactorVerificationController::class, 'resendVerificationCode'])->name('admin.resend_verification_code');
        
        Route::middleware(['two_factor_auth'])->group(function(){
            Route::match(['get','post'],'profile',[CmsUserController::class,'profile'])->name('admin.profile');
            Route::match(['get','post'],'change-password',[CmsUserController::class,'changePassword'])->name('admin.change-password');
            Route::get('logout',[CmsUserController::class,'logout'])->name('admin.logout');
    
            Route::get('dashboard',[DashboardController::class,'index'])->name('admin.dashboard');
            Route::get('dashboard/small-widget',[DashboardController::class,'getSmallWidget'])->name('admin.dashboard.small-widget');
            Route::get('dashboard/line-chart',[DashboardController::class,'getLineChart'])->name('admin.dashboard.line-chart');
    
            Route::get('cms-roles-management/ajax-listing',[CmsRoleController::class,'ajaxListing'])->name('cms-roles-management.ajax-listing');
            Route::resource('cms-roles-management',CmsRoleController::class);
    
            Route::get('cms-users-management/ajax-listing',[CmsUserController::class,'ajaxListing'])->name('cms-users-management.ajax-listing');
            Route::resource('cms-users-management',CmsUserController::class);
    
            Route::match(['get','post'],'application-setting',[ApplicationSettingController::class,'index'])->name('admin.application-setting');
    
            Route::get('app-users/subscriptions/',[UserController::class,'subscriptions'])->name('app-users.subscriptions');
            Route::post('app-users/subscriptions/',[UserController::class,'subscriptionUpdate'])->name('app-users.subscriptionUpdate');
    
            Route::get('app-users/ajax-listing',[UserController::class,'ajaxListing'])->name('app-users.ajax-listing');
            Route::resource('app-users',UserController::class);
            Route::post('app-users/new-user',[UserController::class,'newUser'])->name('app-users.new-user');
    
    
    
            Route::get('vendor/ajax-listing',[UserController::class,'ajaxListing'])->name('vendor.ajax-listing');
            Route::resource('vendor',UserController::class);
            Route::get('add-business-account',[UserController::class, 'addBusinessAccount'])->name('vendor.add-business-account');
    
            Route::get('business-category/ajax-listing',[CategoryController::class,'ajaxListing'])->name('business-category.ajax-listing');
            Route::resource('business-category',CategoryController::class);
    
            Route::get('promote-category/ajax-listing',[CategoryController::class,'ajaxListing'])->name('promote-category.ajax-listing');
            Route::resource('promote-category',CategoryController::class);
    
            Route::get('content-management/ajax-listing',[ContentManagementController::class,'ajaxListing'])->name('content-management.ajax-listing');
            Route::resource('content-management',ContentManagementController::class);
    
            Route::get('faq/ajax-listing',[FaqController::class,'ajaxListing'])->name('faq.ajax-listing');
            Route::resource('faq',FaqController::class);
    
            Route::get('quote/ajax-listing',[QuoteController::class,'ajaxListing'])->name('quote.ajax-listing');
            Route::resource('quote',QuoteController::class);
    
            Route::get('user-ratings',[UserController::class,'userRatingList'])->name('rating.index');
        });
    });
    Route::get('user-rating/{id}/delete',[UserController::class,'userRatingDelete'])->name('deleteRating');
        Route::get('/cache-clear', function(){
        \Artisan::call('cache:clear');
        dd('done');
    });
});
Route::get('/test-stripe', [UserController::class, 'stripeTest']);
Route::post('/test-stripe', [UserController::class, 'stripeTestpost'])->name('stripe_test');
