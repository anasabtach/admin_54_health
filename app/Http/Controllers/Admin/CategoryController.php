<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CategoryController extends CRUDCrontroller
{
    public function __construct(Request $request)
    {   
        parent::__construct('Category');
        $this->__request    = $request;
        $this->__data['page_title'] = 'Category';
        $this->__indexView  = 'category.index';
        $this->__createView = 'category.add';
        $this->__editView   = 'category.edit';
        $this->__detailView = 'category.detail';
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
                    'title' => 'required|unique:categories,title,NULL,deleted_at',
                ]);
                break;
            case 'PUT':
                $validator = Validator::make($this->__request->all(), [
                    'title' => [
                        'required',
                        Rule::unique('categories')->ignore($slug,'slug')->whereNull('deleted_at')
                    ],
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
        $route = \Route::currentRouteName() == 'business-category.ajax-listing' ? route('business-category.edit',['business_category' => $record->slug]) : route('promote-category.edit',['promote_category' => $record->slug]);
        //$options  = '<a href="'. $route .'" title="Edit" class="btn btn-xs btn-primary"><i class="fa fa-pencil"></i></a>';
        $options = '<a title="Delete" class="btn btn-xs btn-danger _delete_record"><i class="fa fa-trash"></i></a>';
        return [
            '<input type="checkbox" name="record_id[]" class="record_id" value="'. $record->slug .'">',
            $record->title,
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
    public function beforeRenderDetailView()
    {

    }

    /**
     * This function is called before a model load
     */
    public function beforeDeleteLoadModel()
    {

    }
}
