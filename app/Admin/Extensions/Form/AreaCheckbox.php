<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 2019/10/6
 * Time: 19:00
 */

namespace App\Admin\Extensions\Form;
use Encore\Admin\Form\Field;

class AreaCheckbox extends Field
{
    protected $view = 'admin.area-checkbox';

    protected $jsonpath = '/vendor/laravel-admin-ext/area-checkbox/area.json';

    public function render()
    {
        $area = json_decode(file_get_contents(public_path($this->jsonpath)), true);

        $this->script = <<<EOT

    // 全国按钮选中
    $('#all-area').click(function(e){
        if ($(e.target)[0].checked) {
            $("input[name='{$this->column}[]']").prop("checked", true);
        } else {
            $("input[name='{$this->column}[]']").prop("checked", false);
        }
    });
    
    // 选中省级，下级自动选中或取消
    $('.province').click(function(e){
        var code = $(e.target).val();
        if ($(e.target)[0].checked) {
            $('.province-'+code+':not(:checked)').each(function () {
                $(this).prop("checked",true);
            });
            // 所有省没有一个不被选中，全国选中
            if ($('.province:not(:checked)').length == 0) {
                $('#all-area').prop("checked", true);
            }
        } else {
            $('.province-'+code+':checked').each(function () {
                $(this).prop("checked", false);
            });
            $('#all-area').prop("checked", false);
        }
    });
    
    $('.city').click(function(e){
        var pcode = $(e.target).data('pcode');
        
        if (! $(e.target)[0].checked) {
            // 取消选中省+全国
            $('input[name="{$this->column}[]"][value="'+pcode+'"]').prop("checked", false);
            $('#all-area').prop("checked", false);
        } else {
            // 所有市选中，则省选中
            if ($('.province-'+pcode+':not(:checked)').length == 0) {
                $('input[name="{$this->column}[]"][value="'+pcode+'"]').prop("checked", true);
                // 所有省选中，则全国选中
                if ($('.province:not(:checked)').length == 0) {
                    $('#all-area').prop("checked", true);
                }
            }
        }
    });

EOT;

        return parent::render()->with('area', $area);
    }

    protected function makeJson()
    {
        $china = \DB::table('area')->where('id', 1)->first();
        $res = [
            'code' => $china->code,
            'name' => $china->name,
        ];
        $provinces = \DB::table('area')->where('parent_id', $china->id)->get();
        foreach ($provinces as $province) {
            $province_item = [
                'code' => $province->code,
                'name' => $province->name,
                'children' => []
            ];

            $cities = \DB::table('area')->where('parent_id', $province->id)->get();
            foreach ($cities as $city) {
                $city_item = [
                    'code' => $city->code,
                    'name' => $city->name,
                    'children' => []
                ];

                $districts = \DB::table('area')->where('parent_id', $city->id)->get();
                foreach ($districts as $district) {
                    $district_item = [
                        'code' => $district->code,
                        'name' => $district->name,
                        'children' => []
                    ];

                    $city_item['children'][] = $district_item;
                }

                $province_item['children'][] = $city_item;
            }

            $res['children'][] = $province_item;
        }
        file_put_contents(__DIR__.'/area.json', json_encode($res, JSON_UNESCAPED_UNICODE));
    }
}