<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApplicationSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApplicationSettingController extends Controller
{
    public function index(Request $request)
    {
        if( $request->isMethod('post') )
            return self::_saveApplicationSetting($request);

        return $this->__cbAdminView('application_setting.index');
    }

    private function _saveApplicationSetting($request)
    {
        $validator = Validator::make($request->all(), [
            'application_name' => 'required|min:3|max:100',
            'logo'             => 'image',
            'favicon'          => 'mimes:png,jpg,jpeg,ico',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        ApplicationSetting::saveAppSetting($request->all());
        return redirect()->back()->with('success','Application setting has been saved successfully');
    }
}
