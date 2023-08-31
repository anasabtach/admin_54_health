<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CustomHelper;
use App\Http\Controllers\Controller;

class CRUDCrontroller extends Controller
{
    protected $__indexView,
              $__createView,
              $__editView,
              $__model,
              $__detailView,
              $__success_store_message,
              $__success_update_message,
              $__success_delete_message,
              $__request,
              $__is_error = false,
              $__data = [];

    public function __construct($model)
    {   
        $this->__model = $model;
        $this->__success_store_message   = __('app.success_store_message');
        $this->__success_update_message  = __('app.success_update_message');
        $this->__success_delete_message  = __('app.success_delete_message');
    }

    public function index()
    {   
        //check permission
        if( currentUser()->user_group_id != 1 && modulePermission()->is_view != 1)
            return redirect()->route('admin.dashboard')->with('error',__('app.permission_denied'));

        //before render index view hook
        if(method_exists($this,'beforeRenderIndexView')){
            $response = $this->beforeRenderIndexView();
            if(  $this->__is_error ){
                return $response;
            }
        }
        return $this->__cbAdminView($this->__indexView,$this->__data);
    }

    public function create()
    {   
        //check permission 
        if( currentUser()->user_group_id != 1 && modulePermission()->is_add != 1)
            return redirect()->route('admin.dashboard')->with('error',__('app.permission_denied'));

        //before render create view hook
        if(method_exists($this,'beforeRenderCreateView')){
            $response = $this->beforeRenderCreateView();
            if(  $this->__is_error ){
                return $response;
            }
        }
        return $this->__cbAdminView($this->__createView,$this->__data);
    }

    public function store()
    {   
        //check permission
        if( currentUser()->user_group_id != 1 && modulePermission()->is_add != 1)
            return redirect()->route('admin.dashboard')->with('error',__('app.permission_denied'));

        if(method_exists($this,'validation')){
            $validator = $this->validation('POST');
            if (!empty($validator) && $validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();;
            }
        }

        //before load modal hook
        if(method_exists($this,'beforeStoreLoadModel')){
            $response = $this->beforeStoreLoadModel();
            if(  $this->__is_error ){
                return $response;
            }
        }
        $data   = $this->__request->all();
        $record = $this->loadModel()->createRecord($this->__request,$data);
        $current_route = explode('.',\Route::currentRouteName());
        return redirect()
            ->route($current_route[0] . '.index')
            ->with('success',$this->__success_store_message);
        
        // return redirect()
        //     ->route($current_route[0] . '.edit',[ str_replace('-','_',$current_route[0]) => $record->slug ])
        //     ->with('success',$this->__success_store_message);
    }

    public function edit($slug)
    {
        //check permission
        if( currentUser()->user_group_id != 1 && modulePermission()->is_update != 1)
            return redirect()->route('admin.dashboard')->with('error',__('app.permission_denied'));

        //before render create view hook
        if(method_exists($this,'beforeRenderEditView')){
            $response = $this->beforeRenderEditView($slug);
            if(  $this->__is_error ){
                return $response;
            }
        }
        $this->__data['record'] = $this->loadModel()->getRecordBySlug($this->__request,$slug);
        if( !isset($this->__data['record']->id) )
            return redirect()->back()->with('error',__('app.invalid_request'));
        return $this->__cbAdminView($this->__editView,$this->__data);
    }

    public function update($slug)
    {
        //check permission
        if( currentUser()->user_group_id != 1 && modulePermission()->is_update != 1)
            return redirect()->route('admin.dashboard')->with('error',__('app.permission_denied'));

        if(method_exists($this,'validation')){
            $validator = $this->validation('PUT',$slug);
            if (!empty($validator) && $validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();;
            }
        }
        //before load modal hook
        if(method_exists($this,'beforeStoreLoadModel')){
            $response = $this->beforeUpdateLoadModel();
            if(  $this->__is_error ){
                return $response;
            }
        }
        $data = $this->__request->all();
        $this->loadModel()->updateRecord($this->__request,$slug,$data);
        return redirect()->back()->with('success',$this->__success_update_message);
    }

    public function show($slug)
    {   
        //check permission
        if( currentUser()->user_group_id != 1 && modulePermission()->is_view != 1)
            return redirect()->route('admin.dashboard')->with('error',__('app.permission_denied'));

        //before render index view hook
        if(method_exists($this,'beforeRenderDetailView')){
            $response = $this->beforeRenderDetailView();
            if(  $this->__is_error ){
                return $response;
            }
        }
        //get record by slug
        $this->__data['record'] = $this->loadModel()->getRecordBySlug($this->__request,$slug);
        if( !isset($this->__data['record']->id) )
            return redirect()->back()->with('error',__('app.invalid_request'));
        
        return $this->__cbAdminView($this->__detailView,$this->__data);
    }

    public function destroy($slug)
    {   
        //check permission
        if( currentUser()->user_group_id != 1 && modulePermission()->is_delete != 1){
            if( $this->__request->ajax() )
                return response()->json(['code' => 400,'message' => __('app.permission_denied')],400);
            else
                return redirect()->route('admin.dashboard')->with('error',__('app.permission_denied'));
        }
    
        //before render create view hook
        if(method_exists($this,'beforeUpdateLoadModel')){
            $response = $this->beforeDeleteLoadModel($slug);
            if(  $this->__is_error ){
                return $response;
            }
        }

        $this->loadModel()->deleteRecord($this->__request,$this->__request['slug']);
        if( $this->__request->ajax() )
            return response()->json(['code' => 200,'message' => $this->__success_delete_message],200);
        else
            return redirect()->back()->with('success',$this->__success_delete_message);
    }

    public function ajaxListing()
    {
        $records["data"] = array();
        //get records for datatable
        $dataTableRecord = $this->loadModel()->dataTableRecords($this->__request);;
        // set data grid output
        if(count($dataTableRecord['records']))
        {
            foreach($dataTableRecord['records'] as $record){
                $records["data"][] = $this->dataTableRecords($record);
            }
        }
        $records["draw"] = (int)$this->__request->input('draw');
        $records["recordsTotal"] = $dataTableRecord['total_record'];
        $records["recordsFiltered"] = $dataTableRecord['total_record'];
        return response()->json($records);
    }

    /**
     * This function is used for load model
     * return object
     */
    public function loadModel()
    {
        $model = '\App\Models\\' . $this->__model;
        return new $model;
    }
}
