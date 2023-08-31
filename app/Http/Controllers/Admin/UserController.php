<?php

namespace App\Http\Controllers\Admin;

use App\Mail\UserRegistration;
use App\Models\Category;
use App\Models\User;
use App\Models\UserInvite;
use App\Models\UserPackage;
use App\Models\UserRating;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Subscription;
use Stripe\PaymentMethod;

class UserController extends CRUDCrontroller
{
    public function __construct(Request $request)
    {   
        parent::__construct('User');
        $this->__request    = $request;
        $this->__data['page_title'] = 'Users';
        $this->__indexView  = 'users.index';
        $this->__createView = 'users.add';
        $this->__editView   = 'users.edit';
        $this->__detailView   = 'users.detail';
        $this->addBusinessAccount = 'users.add_business_account';
    }

    /**
     * This function is used for validate data
     * @param string $action
     * @param string $slug
     * @return array|\Illuminate\Contracts\Validation\Validator
     */
    public function validation(string $action, string $slug=NULL)
    {
        $validator = [];
        switch ($action){
            case 'POST':
                $validator = Validator::make($this->__request->all(), [
                    'attribute' => 'required',
                ]);
                break;
            case 'PUT':
                $validator = Validator::make($this->__request->all(), [
                    '_method' => 'required|in:PUT',
                    'status'  => 'required|in:1,0',
                ]);
                break;
        }
        return $validator;
    }

    /**
     * This function is used for before the index view render
     * data pass on view eg: $this->__data['title'] = 'Title';
     */
    public function beforeRenderIndexView()
    {

    }

    /**
     * This function is used to add data in datatable
     * @param object $record
     * @return array
     */
    public function dataTableRecords($record)
    {
        

        $userSubscription = UserPackage::getActiveSubscription($record->id);

        $options  = '<a href="'. route('app-users.edit',['app_user' => $record->slug]) .'" title="Edit" class="btn btn-xs btn-primary"><i class="fa fa-pencil"></i></a>';
        $options  .= '<a style="margin-left:10px" href="'. route('app-users.show',['app_user' => $record->slug ]) .'" title="Edit" class="btn btn-xs btn-primary"><i class="fa fa-search"></i></a>';

        // <form method="post" action="{{ route('app-users.update',['app_user' => $record->slug]) }}" enctype="multipart/form-data">
        // {{ csrf_field() }}
        // <input type="hidden" name="_method" value="PUT">
        // </form>
        // $options  .= ' <a href="'. route('app-users.destroy',['app_user' => $record->slug]) .'" title="Edit" class="btn btn-xs btn-primary"><i class="fa fa-pencil"></i></a>';

        $options .= '<form method="post" action="' . route('app-users.destroy', ['app_user' => $record->slug]) . '">';
        $options .= csrf_field();
        $options .= '<input type="hidden" name="_method" value="DELETE">';
        $options .= '<input type="hidden" name="slug" value="'.$record->slug.'">';

        $options .= '<button type="submit" class="btn btn-xs btn-danger" onclick="return confirm(\'Are you sure you want to delete this record?\')"><i class="fa fa-trash"></i></button>';
        $options .= '</form>';

        if ($userSubscription != null) {
            $options .= '<a style="margin-left:10px" href="' . route('app-users.subscriptions', ['app_user' => $record->slug]) . '" title="Edit" class="btn btn-xs btn-primary">
                      <i class="fa fa-user"></i></a>';
        }
        $userInvite = UserInvite::where('user_id',$record->id)->first();
        if ($userInvite){
            $invitedBy = User::find($userInvite->invite_user_id)->email;
        }else{
            $invitedBy = 'None';
        }		
        return [
            $record->name. '<br/> <b>Invited By: </b>'.$invitedBy. '<br/> <b>Member Type: </b>'.$record->member_type,
            $record->email,
            $record->mobile_no,            
            $record->is_email_verify == 1 ? '<span class="btn btn-xs btn-success">Verified</span>' : '<span class="btn btn-xs btn-danger">not verified</span>',
            $record->account_approved == 1 ? '<span class="btn btn-xs btn-success">Approved</span>' : '<span class="btn btn-xs btn-warning">Pending</span>',
            $record->status == 1 ? '<span class="btn btn-xs btn-success">Active</span>' : '<span class="btn btn-xs btn-danger">Disabled</span>',
            date(config("constants.ADMIN_DATE_FORMAT") , strtotime($record->created_at)),
            $options
        ];
    }

    /**
     * This function is used for before the create view render
     * data pass on view eg: $this->__data['title'] = 'Title';
     */
    public function beforeRenderCreateView()
    {

    }

    /**
     * This function is called before a model load
     */
    public function beforeStoreLoadModel()
    {

    }

    /**
     * This function is used for before the edit view render
     * data pass on view eg: $this->__data['title'] = 'Title';
     * @param string @slug
     */
    public function beforeRenderEditView($slug)
    {

    }

    /**
     * This function is called before a model load
     */
    public function beforeUpdateLoadModel()
    {

    }

    /**
     * This function is called before a model load
     */
    public function beforeDeleteLoadModel()
    {

    }

    public function subscriptions(Request $request){

        $user = $this->loadModel()->getRecordBySlug($request,$request->app_user);
        $userSubscription = UserPackage::getActiveSubscription($user->id);
        if ($userSubscription == null){ return redirect()->back()->with('error',__('No Active subscription found')); }
//        $userSubscription = [ $userSubscription ];
//        dd($userSubscription->package->title);
        return view('admin.users.subscription',compact('userSubscription','user'));
    }

    public function subscriptionUpdate(Request $request){
        if ($request->months === ''){ return redirect()->back()->with('error',__('Invalid month')); }
        $months = $request->months;
        $userSlug = $request->user_slug;
        $user = $this->loadModel()->getRecordBySlug($request,$userSlug);
        $userSubscription = $request->subscription_id;
        $userSubscription = UserPackage::find($userSubscription);
        $expiryDate = $userSubscription->expiry_date;
        $expiryDate = Carbon::parse($expiryDate);
        $newExpiryDate = $expiryDate->addMonth($months);
        $userSubscription->expiry_date = $newExpiryDate;
        $user->subscription_expiry_date = $newExpiryDate;
        $userSubscription->save();
        $user->save();
        return redirect()->back()->with('info',__('Subscription updated'));

    }

    public function userRatingList(){

        $ratings = UserRating::select('*')->leftJoin('users AS U',function($leftJoin) {
            $leftJoin->on('U.id','=','user_rating.user_id');
        })->where('module','deals')->get();

        return view('admin.userRatingList',compact('ratings'));
    }
    public function userRatingDelete($id){
        $rating = UserRating::find($id)->delete();
        return redirect()->back()->with('info',__('Review Removed'));
    }

    public function newUser(){
        
        $request        = $this->__request;
        $params         = $request->all();
        $params['user_group_id'] = 2;
        $attachments = [];
        if( !empty($request['id_card']) ){
            $image_url      = fopen($request['id_card']->getPathName(), 'r');
            $image_name     = $request['id_card']->getClientOriginalName();
            $attachments[]  = [ 'key' => 'id_card' ,'file' => $image_url, 'name' => $image_name ];
        }

        if($params['action'] == 'registration'){
            $params['mobile_no'] = '+1-'.$params['mobile_no'];
            $params['user_group_id'] = 3;
            $attachments = [];
            if( !empty($request['id_card']) ){
                $image_url      = fopen($request['id_card']->getPathName(), 'r');
                $image_name     = $request['id_card']->getClientOriginalName();
                $attachments[]  = [ 'key' => 'id_card' ,'file' => $image_url, 'name' => $image_name ];
            }
        }else{
            $params['user_group_id'] = 4;
            $params['name'] = $params['business_name'];
            if( !empty($params['open_time']) && !empty($params['close_time']) ){
                $params['open_time']  = date('H:i:s',strtotime($params['open_time']));
                $params['close_time'] = date('H:i:s',strtotime($params['close_time']));
            }
            $attachments = [];
            if( !empty($request['image_url']) ){
         //       dd($request['image_url']->getPathName());
                $image_url      = fopen($request['image_url']->getPathName(), 'r');
                $image_name     = $request['image_url']->getClientOriginalName();
                $attachments[]  = [ 'key' => 'image_url' ,'file' => $image_url, 'name' => $image_name ];
            }
        }
        
        $response       = $this->__httpApiPostRequest('user',$params,$attachments);
        
        if($response->code == 400 ){
            return redirect()->back()->withErrors($response->data)->withInput();
        }
        return redirect()
        ->back()
        ->with('success',$this->__success_store_message);
    }

    public function addBusinessAccount(Request $req){
        return $this->__cbAdminView($this->addBusinessAccount, ['promote_categories'=>Category::where('type', 'promote')->get()]);

    }

    public function stripeTest(){
        // Stripe::setApiKey(config('services.stripe.secret'));
        // $customer = Customer::create([
        //     'email' => 'testuser03@yopmail.com', // Replace with actual user's email
        // ]);
        // $customerId = $customer->id;

        // $planId = 'plan_OSjvuIYxNc1ZOf';

        // // Create the subscription
        // $subscription = Subscription::create([
        //     'customer' => $customerId,
        //     'items' => [
        //         [
        //             'plan' => $planId,
        //         ],
        //     ],
        // ]);
        // dd($subscription);
        return view('test_stripe', ['intent'=>$this->createSetupIntent()]);
    }
    
    public function stripeTestpost(Request $req){
           // Set your Stripe API key
    Stripe::setApiKey('sk_test_51NfnBdGkONwmLpxCh5LAZMG3POkvXax3wOkHq6T1WLqsIckYXmfU1RG34oUST3EU9MUdV1QCkdzM3YD0GBrHQKLS00Ud96xPYz');

    // Create a new customer with the provided email
    $customer = Customer::create([
        'email' => 'testuser033@yopmail.com', // Replace with actual user's email
        'payment_method' => $req->payment_method,
    ]);

    // $paymentMethod = PaymentMethod::attach($req->payment_method, ['customer' => $customer->id]);
    // Update the subscription plan
    $planId = 'plan_OSjvuIYxNc1ZOf';

$subscription = Subscription::update(
    $planId, // Replace with the subscription ID
    [
        'items' => [
            [
                'id' => $planId, // Replace with the subscription item ID
                'price' => $planId,
            ],
        ],
    ]
);


    // Replace 'plan_ID' with the actual ID of the plan you want to subscribe to

    // Create the subscription using the specified plan and customer
    $subscription = Subscription::create([
        'customer' => $customer->id,
        'items' => [
            [
                'plan' => $planId,
            ],
        ],
    ]);

    dd($subscription);
    }

    public function createSetupIntent()
    {
        $response = Http::withBasicAuth(
            'sk_test_51NfnBdGkONwmLpxCh5LAZMG3POkvXax3wOkHq6T1WLqsIckYXmfU1RG34oUST3EU9MUdV1QCkdzM3YD0GBrHQKLS00Ud96xPYz',
            ''
        )->asForm()->post('https://api.stripe.com/v1/setup_intents', [
            'usage' => 'on_session',
            // You can add more parameters here as needed
        ]);

        return $response->json()['client_secret'];
    }
}
