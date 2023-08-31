<?php

namespace App\Http\Controllers\Admin;

use App\Models\CmsRole;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CmsUserController extends CRUDCrontroller
{
    public function __construct(Request $request)
    {
        parent::__construct('User');
        $this->__request    = $request;
        $this->__data['page_title'] = 'Cms User Management';
        $this->__indexView  = 'cms_users.index';
        $this->__createView = 'cms_users.add';
        $this->__editView   = 'cms_users.edit';
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
        $custom_messages = [
            'password.regex' => __('app.password_regex'),
        ];
        switch ($action){
            case 'POST':
                $validator = Validator::make($this->__request->all(), [
                    'name'             => 'required|min:2|max:50',
                    'email'            => 'required|email|max:100|unique:users,email,NULL,deleted_at',
                    'image_url'        => 'required|image',
                    'mobile_no'        => [
                        'required',
                        'unique:users,mobile_no,NULL,deleted_at',
                        'regex:/^(\+?\d{1,3}[-])\d{9,11}$/'
                    ],
                    'password'         => [
                        'required',
                        'regex:/^(?=.*[A-Z])(?=.*[!@#$&*])(?=.*[0-9])(?=.*[a-z]).{8,150}$/'
                    ],
                    'confirm_password' => 'required|same:password',
                    'user_group_id'      => 'required|exists:user_groups,id,deleted_at,NULL',
                ],$custom_messages);
                break;
            case 'PUT':
                $validator = Validator::make($this->__request->all(), [
                    '_method'   => 'required|in:PUT',
                    'name'      => 'required|min:2|max:50',
                    'image_url' => 'image',
                    'email'   => [
                        'required',
                        'email',
                        'max:100',
                        Rule::unique('users')->whereNull('deleted_at')->ignore($slug,'slug')
                    ],
                    'mobile_no' => [
                        'required',
                        'regex:/^(\+?\d{1,3}[-])\d{9,11}$/',
                        Rule::unique('users')->whereNull('deleted_at')->ignore($slug,'slug')
                    ],
                    'user_group_id' => 'required|exists:user_groups,id,deleted_at,NULL',
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
        $options  = '<a href="'. route('cms-users-management.edit',['cms_users_management' => $record->username]) .'" title="Edit" class="btn btn-xs btn-primary"><i class="fa fa-pencil"></i></a>';
        $options .= '<a title="Delete" class="btn btn-xs btn-danger _delete_record"><i class="fa fa-trash"></i></a>';
        return [
            '<input type="checkbox" name="record_id[]" class="record_id" value="'. $record->slug .'">',
            $record->user_group,
            $record->name,
            $record->email,
            $record->mobile_no,
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
        $this->__data['getCmsRole'] = UserGroup::getCmsRole();
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
     * @param string $slug
     */
    public function beforeRenderEditView($slug)
    {
        $this->__data['getCmsRole'] = UserGroup::getCmsRole();
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

    /**
     * This function is used to render profile view
     * @param Request $request
     * @return view
     */
    public function profile(Request $request)
    {
        if( $request->isMethod('post') )
            return self::_submitProfile($request);

        $data['page_title'] = 'Profile';
        return $this->__cbAdminView('cms_users.profile',$data);
    }

    /**
     * This function is used to submit profile data
     * @param Request $request
     * @return redirect
     */
    private function _submitProfile($request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required|min:3|max:50',
            'email'     => [
                'required',
                'email',
                Rule::unique('users')->whereNull('deleted_at')->ignore(currentUser()->id),
            ],
            'mobile_no' => [
                'min:8',
                'max:15',
                Rule::unique('users')->whereNull('deleted_at')->ignore(currentUser()->id),
            ],
            'image_url' => 'image|max:5120', //2mb
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        User::updateProfile($request->all());
        return redirect()->back()->with('success','Profile has been updated successfully');
    }

    /**
     * This function is used to render change password view
     * @param Request $request
     * @return view
     */
    public function changePassword(Request $request)
    {
        if( $request->isMethod('post') )
            return self::_submitChangePassword($request);

        $data['page_title'] = 'Change Password';
        return $this->__cbAdminView('cms_users.change-password',$data);
    }

    /**
     * This function is used to submit change password data
     * @param Request $request
     * @return redirect
     */
    private function _submitChangePassword($request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password'     => 'required|min:6|max:255',
            'confirm_password' => 'required|same:new_password',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        if( !Hash::check($request['current_password'],currentUser()->password) )
            return redirect()->back()->with('error','Invalid current password');

        User::updatePassword(currentUser()->id,$request['new_password']);
        return redirect()->back()->with('success','Password has been updated successfully');
    }

    /**
     * This is used to logout current user
     * @return redirect
     */
    public function logout()
    {
        session()->flush();
        Auth::logout();
        $login_url = route('admin.login') . '?auth_token=' . config('constants.ADMIN_AUTH_TOKEN');
        return redirect($login_url)->with('success','You have logged out successfully');
    }
}
