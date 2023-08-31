<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsWidget;
use Illuminate\Support\Facades\Route;
use CustomHelper;

class DashboardController extends Controller
{
    public function index()
    {
        $data['page_title'] = 'Dashboard';
        return $this->__cbAdminView('dashboard.index',$data);
    }

    public function getSmallWidget()
    {
        $data = [];
        $widgets = CmsWidget::getWidgetByType('small_box');
        foreach( $widgets as $widget ){
            $records = \DB::select($widget->sql);
            if( count($records) ){
                foreach( $records[0] as $record ){
                    $value = $record;
                }
            } else {
                $value = 0;
            }
            $data[] = [
                'title'            => $widget->title,
                'icon'             => $widget->icon,
                'color'            => $widget->color,
                'div_column_class' => $widget->div_column_class,
                'link'             => $widget->link,
                'config'           => $widget->config,
                'value'            => $value,
            ];
        }
        return response()->json($data);
    }

    public function getLineChart()
    {
        $data       = [];
        $final_data = [];
        $widgets = CmsWidget::getWidgetByType('line_chart');
        foreach( $widgets as $widget ){
            $records = \DB::select($widget->sql);
            if( count($records) ){
                $data = [];
                foreach( $records as $record ){
                    $data['label'][] = $record->label;
                    $data['value'][] = $record->value;
                }
                $final_data[] = [
                    'title'            => $widget->title,
                    'description'      => $widget->description,
                    'icon'             => $widget->icon,
                    'color'            => $widget->color,
                    'div_column_class' => $widget->div_column_class,
                    'link'             => $widget->link,
                    'config'           => $widget->config,
                    'data'             => $data
                ];
            } else {
                $data = [];
            }
        }
        return response()->json($final_data);
    }
}
