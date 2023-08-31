<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsModule;
use App\Models\CmsRole;
use App\Models\UserGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CmsRoleController extends Controller
{
    private $_data;

    public function __construct()
    {
        $this->_data['page_title'] = 'Cms Role Management';
    }

    public function index()
    {
        return $this->__cbAdminView('cms_roles.index',$this->_data);
    }

    public function ajaxListing(Request $request)
    {
        // search params
        $fields = $request->all();
        $records["data"] = array();
        //get records for datatable
        $dataTableRecord = UserGroup::dataTableRecords($fields);
        // set data grid output
        if(count($dataTableRecord['records']))
        {
            foreach($dataTableRecord['records'] as $record){
                $options  = '<a href="'. route('cms-roles-management.edit',['cms_roles_management' => $record->slug]) .'" title="Edit" class="btn btn-xs btn-primary"><i class="fa fa-pencil"></i></a>';
                $options .= '<a title="Delete" class="btn btn-xs btn-danger _delete_record"><i class="fa fa-trash"></i></a>';
                $records["data"][] = [
                    '<input type="checkbox" name="record_id[]" class="record_id" value="'. $record->slug .'">',
                    $record->title,
                    date(config("constants.ADMIN_DATE_FORMAT") , strtotime($record->created_at)),
                    $options
                ];
            }
        }
        $records["draw"] = (int)$request->input('draw');
        $records["recordsTotal"] = $dataTableRecord['total_record'];
        $records["recordsFiltered"] = $dataTableRecord['total_record'];
        return response()->json($records);

    }

    public function create()
    {
        $this->_data['getModules'] = CmsModule::getModules();
        return $this->__cbAdminView('cms_roles.add',$this->_data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'           => 'required|min:3|max:50|unique:user_groups,title,NULL,deleted_at',
            'is_super_admin' => 'required|in:0,1',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $record = UserGroup::createRole($request->all());
        return redirect()->route('cms-roles-management.edit',['cms_roles_management' => $record->slug])
                         ->with('success',__('app.record_added_msg'));
    }

    public function edit($slug)
    {
        $this->_data['record'] = UserGroup::getRecordBySlug($slug);
        if( !isset($this->_data['record']->id) ){
            return redirect()->route('cms-roles-management.index')->with('error',__('app.invalid_request'));
        }
        $this->_data['getModules'] = CmsModule::getModules($this->_data['record']->id);
        return $this->__cbAdminView('cms_roles.edit',$this->_data);
    }

    public function update(Request $request,$slug)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'min:3',
                'max:50',
                Rule::unique('user_groups','title')->whereNull('deleted_at')->ignore($slug, 'slug')
            ],
            'is_super_admin' => 'required|in:0,1',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $record = UserGroup::updateRole($request->all(),$slug);
        return redirect()->route('cms-roles-management.edit',['cms_roles_management' => $record->slug])
                         ->with('success',__('app.record_updated_msg'));
    }

    public function destroy($slug,Request $request)
    {
        UserGroup::deleteRecord($request['slug']);
        $message = is_array($request['slug']) ? 'Selected records have been deleted successfully.' : 'Record has been deleted successfully';
        return response()->json(['message' => $message]);
    }
}
