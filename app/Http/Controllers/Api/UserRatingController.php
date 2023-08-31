<?php

namespace App\Http\Controllers\Api;

use App\Models\Deal;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Http\Controllers\RestController;
use App\Models\UserRating;

class UserRatingController extends RestController
{
    public function __construct(Request $request)
    {
        parent::__construct('UserRating');
        $this->__request     = $request;
        $this->__apiResource = 'UserRating';
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
        switch ($action){
            case 'INDEX':
                $validator = Validator::make($this->__request->all(), [
                    'module'    => 'required|in:users',
                    'module_id' => 'required|numeric',
                ]);
                break;
            case 'POST':
                $validator = Validator::make($this->__request->all(), [
                    'module'    => 'required',
                    'module_id' => 'required|numeric',
                    'rating'    => 'required|between:1,5',
                    'review'    => 'required|max:1000',
                ]);
                break;
            case 'PUT':
                $validator = Validator::make($this->__request->all(), [
                    'rating'    => 'required|between:1,5',
                    'review'    => 'required|max:1000',
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
        $record = UserRating::checkRecord(
            $request['user']->id,
            $request['module'],
            $request['module_id']
        );
        if( !empty($record->id) ){
            $this->__is_error = true;
            return $this->__sendError('Validation Message',['message' => 'You have already given a review'],400);
        }
    }

    /**
     * @param $request
     * @param $record
     */
    public function afterStoreLoadModel($request,$record)
    {
            if ($request->module == 'deals'){
                $deal = Deal::find($record->module_id);
                $deal->total_review = $deal->total_review + 1;
                $deal->total_rating = $deal->total_rating + $record->rating;
                $deal->save();
            }
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
        $record = UserRating::getRatingBySlug($slug);
        if( $request['user']->id != $record->user_id ){
            $this->__is_error = false;
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
        $record = UserRating::getRatingBySlug($slug);
        if( $request['user']->id != $record->user_id ){
            $this->__is_error = false;
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
}
