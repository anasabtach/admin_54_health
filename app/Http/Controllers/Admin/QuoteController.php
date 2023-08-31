<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Quote;
use Carbon\Carbon;

class QuoteController extends CRUDCrontroller
{
    public function __construct(Request $request)
    {
        parent::__construct('Quote');
        $this->__request    = $request;
        $this->__data['page_title'] = 'Quote';
        $this->__indexView  = 'quote.add';
        $this->__createView = 'quote.add';
        //$this->__editView   = 'quote.edit';
        //$this->__detailView = 'quote.detail';
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
                    'description' => 'required',
                ]);
                break;
            case 'PUT':
                $validator = Validator::make($this->__request->all(), [
                    '_method'   => 'required|in:PUT',
                    'attribute' => 'required',
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
        $this->__data['record'] = Quote::first();
    }

    /**
     * This function is used to add data in datatable
     * @param object $record
     * @return array
     */
    public function dataTableRecords($record)
    {
        return [];
    }

    /**
     * This function is used for before the create view render
     * data pass on view eg: $this->__data['title'] = 'Title';
     */
    public function beforeRenderCreateView()
    {
        $this->__data['record'] = Quote::first();
    }

    /**
     * This function is called before a model load
     */
    public function beforeStoreLoadModel()
    {
        \DB::table('quotes')->delete();
    }

    /**
     * This function is used for before the edit view render
     * data pass on view eg: $this->__data['title'] = 'Title';
     * @param string @slug
     */
    public function beforeRenderEditView($slug)
    {
        return redirect()->route('quote.index');
    }

    /**
     * This function is called before a model load
     */
    public function beforeUpdateLoadModel()
    {
        return redirect()->route('quote.index');
    }

    /**
     * This function is called before a model load
     */
    public function beforeRenderDetailView()
    {
        return redirect()->route('quote.index');
    }

    /**
     * This function is called before a model load
     */
    public function beforeDeleteLoadModel()
    {
        return redirect()->route('quote.index');
    }

    public function store()
    {
        $validator = Validator::make($this->__request->all(), [
            'description' => 'required',
        ]);
        if ( $validator->fails() ) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        //delete old quote
        \DB::table('quotes')->delete();
        //add new quote
        Quote::create([
            'slug' => uniqid(time()),
            'description' => $this->__request['description'],
            'created_at' => Carbon::now()
        ]);
        return redirect()->back()->with('success','Quote has been updated successfully');
    }
}
