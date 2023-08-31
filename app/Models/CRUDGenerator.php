<?php
namespace App\Models;

use Illuminate\Support\Facades\Cache;

trait CRUDGenerator
{
    private $_request;

    protected $__api_hook_path = '\App\Models\Hooks\Api\\';

    protected $__admin_hook_path = '\App\Models\Hooks\Admin\\';

    /**
     * This function is used for save record
     *
     * @param  {array} $data
     * @param  {object} $request
     * @return Response
     */
    public function createRecord($request,$data = [])
    {
        $this->_request = $request;
        if(!empty($data)){
            //before create record request hook
            if(method_exists($this->loadHook(),'hook_before_add'))
                $this->loadHook()->hook_before_add($request,$data);
            //filter column
            $data = $this->fill($data);
            //create record
            $record = self::create($data->toArray());
            //after create record request hook
            if(method_exists($this->loadHook(),'hook_after_add'))
                $this->loadHook()->hook_after_add($request,$record);
            //delete old cache data
            if( $this->__is_cache_record === true )
                env('CACHE_DRIVER') == 'file' ? CustomCache()->flashAll($this->table) : Cache::tags([$this->table])->flush();
            //set response
            return $this->getRecordBySlug($request,$record->slug);
        }
    }

    /**
     * This function is used for get record
     *
     * @param {object} $request
     * @param {array} $filterParams (optional)
     * @return Response
     */
    public function getRecords($request)
    {
        $data = [];
        $this->_request = $request;
        if($this->__is_cache_record === true && method_exists($this->loadHook(),'create_cache_signature'))
            $this->__cache_signature = $this->loadHook()->create_cache_signature($request);

        if( env('CACHE_DRIVER') != 'file' && Cache::has($this->__cache_signature) && $this->__is_cache_record === true ){
            $data = Cache::get($this->__cache_signature);
        }
        if( env('CACHE_DRIVER') == 'file' && $this->__is_cache_record === true  ){
            $data = CustomCache()->get($this->table,$this->__cache_signature);
        }
        if( empty($data) ){
            $query = self::select();
            if(method_exists($this->loadHook(),'hook_query_index'))
                $this->loadHook()->hook_query_index($query,$request);

            $limit = $request->input('limit',config('constants.PAGINATION_LIMIT'));
            $data = $query->orderBy($request->input('sort_column',$this->table .'.' .$this->primaryKey),$request->input('sort_order','desc'))->paginate($limit);
            //store record in cache
            if( $this->__is_cache_record === true )
                env('CACHE_DRIVER') == 'file' ? CustomCache()->store($this->table,$this->__cache_signature,$data) : Cache::tags([$this->table])->put($this->__cache_signature,$data,now()->addDays($this->__cache_expire_time));
        }
        return $data;
    }

    /**
     * This function is used for get record by id
     *
     * @param {object} $request
     * @param  {sting} $slug
     * @return Response
     */
    public function getRecordBySlug($request,$slug)
    {
        $data = [];
        $this->_request = $request;
        $this->__cache_signature = $this->loadHook()->create_cache_signature($request);
        if( env('CACHE_DRIVER') != 'file' && Cache::has($this->table .'_' . $slug ) && $this->__is_cache_record === true ){
            $data = Cache::get($this->table .'_' . $slug );
        }
        if( env('CACHE_DRIVER') == 'file' && $this->__is_cache_record === true ){
            $data = CustomCache()->get($this->table,$this->table .'_' . $slug);
        }
        if( empty($data) ){
            $query = self::select();
            if(method_exists($this->loadHook(),'hook_query_index'))
                $this->loadHook()->hook_query_index($query,$request,$slug);

            $data = $query->where($this->table . '.slug',$slug)->first();
            //store record in cache
            if( $this->__is_cache_record === true )
                 env('CACHE_DRIVER') == 'file' ? CustomCache()->store($this->table,$this->table .'_' . $slug,$data) : Cache::tags([$this->table])->put($this->table .'_' . $slug,$data,now()->addDays($this->__cache_expire_time));
        }
        return $data;
    }

    /**
     * This function is used for update record
     *
     * @param {object} $request
     * @param {slug} $slug
     * @param {array} $data
     * @return Response
     */
    public function updateRecord($request, $slug, $data=[])
    {
        $this->_request = $request;
        if(!empty($data)){
            //before update record request hook
            if(method_exists($this->loadHook(),'hook_before_edit'))
                $this->loadHook()->hook_before_edit($request, $slug, $data);
            //filter column
            $data = $this->fill($data);
            //update record
            self::where('slug',$slug)->update($data->toArray());
            //after create record request hook
            if(method_exists($this->loadHook(),'hook_after_edit'))
                $this->loadHook()->hook_after_edit($request, $slug);
            //delete old cache data
            if( $this->__is_cache_record === true )
                env('CACHE_DRIVER') == 'file' ? CustomCache()->flashAll($this->table) : Cache::tags([$this->table])->flush();
            //set response
            return $this->getRecordBySlug($request,$slug);
        }
        return $data;
    }

    /**
     * This function is used for delete record
     *
     * @param  {int} $id
     * @return Response
     */
    public function deleteRecord($request, $slug)
    {
        $this->_request = $request;
        if( !is_array($slug) )
            $slug = [$slug];
        //before request hook
        if(method_exists($this->loadHook(),'hook_before_delete'))
            $this->loadHook()->hook_before_delete($request, $slug );
        //get record
        $records = self::whereIn('slug',$slug )->get();
        //delete record
        self::whereIn('slug',$slug)->delete();
        //after request hook
        if(method_exists($this->loadHook(),'hook_after_delete'))
            $this->loadHook()->hook_after_delete($request, $records);

        //delete old cache data
        if( $this->__is_cache_record === true )
            env('CACHE_DRIVER') == 'file' ? CustomCache()->flashAll($this->table) : Cache::tags([$this->table])->flush();

        return true;
    }

    public function dataTableRecords($request)
    {
        $this->_request = $request;
        $query = self::select('*');
        if(method_exists($this->loadHook(),'hook_query_index'))
            $this->loadHook()->hook_query_index($query,$request);

        $data['total_record'] = count($query->get());
        $query = $query->take($request['length'])->skip($request['start'])->orderBy('id','desc');
        $query = $query->get();
        $data['records'] = $query;
        return $data;
    }

    /**
     *  This function is used load hook
     * @return class instance
     */
    public function loadHook()
    {
        $className = explode('\\',get_class($this));
        $className = end($className) . 'Hook';
        if( $this->_request->is('api/*') )
            $hook = $this->__api_hook_path . $className;
        else
            $hook = $this->__admin_hook_path . $className;
        return new $hook($this);
    }
}
