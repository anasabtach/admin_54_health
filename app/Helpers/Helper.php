<?php

use App\Helpers\CustomCache;
use App\Helpers\CustomHelper;

/**
 * This function is used to get login user
 * @return \Illuminate\Contracts\Auth\Authenticatable|null
 */
if (! function_exists('currentUser')) {
    function currentUser($guard = 'web'){
        return CustomHelper::currentUser($guard);
    }
}

/**
 * This function is used to send email
 * @param string $to
 * @param string $identifier
 * @param array $params
 * @param array $cc_emails
 * @param array $attachment_path
 */
if (! function_exists('sendMail')) {
    function sendMail($to,$identifier,$params, $cc_emails=[], $attachment_path=[]){
        return CustomHelper::sendMail($to,$identifier,$params, $cc_emails, $attachment_path);
    }
}

/**
 * This function is used to upload single file or multiple files
 * @param string $destination_path
 * @param object|array $file
 * @param null $resize
 * @return bool
 */
if (! function_exists('uploadMedia')) {
    function uploadMedia($destination_path,$file,$resize = NULL){
        return CustomHelper::uploadMedia($destination_path,$file,$resize);
    }
}

/**
 * This function is used to upload single file or multiple files by path
 * @param string $destination_path
 * @param object|array $file
 * @param null $resize
 * @return bool
 */
if (! function_exists('uploadMediaByPath')) {
    function uploadMediaByPath($destination_path,$file,$resize = NULL){
        return CustomHelper::uploadMediaByPath($destination_path,$file,$resize);
    }
}

/**
 * This function is used to resize upload image
 * @param string $destination_path
 * @param string $file
 * @param string $dimension
 */
if (! function_exists('resize')) {
    function resize($destination_path,$file,$dimension){
        return CustomHelper::resize($destination_path,$file,$dimension);
    }
}

/**
 * This function is used to optimize upload image
 * @param string $source_path
 * @param string $destination_path
 * @param integer $quality
 * @return mixed
 */
if (! function_exists('optimizeImage')) {
    function optimizeImage($source_path, $destination_path, $quality){
        return CustomHelper::optimizeImage($source_path, $destination_path, $quality);
    }
}

/**
 * This function is used to generate video thumb
 * @param string $source_path
 * @param string $destination_path
 * @return stiring $thumb
 */
if( ! function_exists('generateVideoThumb') ){
    function generateVideoThumb($source_path, $destination_path){
        return CustomHelper::generateVideoThumb($source_path, $destination_path);
    }
}

/**
 * This function is used to get application by identifier
 * @param string $identifer
 * @param string $meta_key
 * @return array | string
 */
if (! function_exists('appSetting')) {
    function appSetting(string $identifer, string $meta_key = NULL){
        return CustomHelper::appSetting($identifer,$meta_key);
    }
}

/**
 * This function is used to get current route privilege
 * @return object $record
 */
if (! function_exists('modulePermission')) {
    function modulePermission(){
        return CustomHelper::modulePermission();
    }
}

/**
 * getBlurHashImage
 */
if (! function_exists('getBlurHashImage')) {
    function getBlurHashImage(string $image_path, int $components_x=4, int $components_y=4){
        return CustomHelper::getBlurHashImage($image_path, $components_x, $components_y);
    }
}

/**
 * Custom Cache
 */
if( !function_exists('CustomCache') ){
    function CustomCache(){
        return new CustomCache;
    }
}

if( !function_exists('encryptData') ){
    function encryptData($data){
        return CustomHelper::encryptData($data);
    }
}







