<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Tools\DefaultSimpleTool;
use App\Models\Store;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class StoresController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '门店管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Store);

        $grid->column('id', __('Id'));
        $grid->column('title', __('Title'))->editable();
        $grid->column('sort', __('Sort'))->editable()->sortable();
        $grid->column('thumbnail', __('Thumbnail'))->image();
        $grid->column('address', __('Address'))->editable();
        grid_has_enabled($grid);
//        $grid->column('latitude', __('Latitude'));
//        $grid->column('longitude', __('Longitude'));
//        $grid->column('point', __('Point'));
//        $grid->column('geohash', __('Geohash'));
        $grid->column('created_at', __('Created at'));
//        $grid->column('updated_at', __('Updated at'));
//        $grid->column('deleted_at', __('Deleted at'));

        $grid->tools(function (Grid\Tools $tools) {
            $tools->append(new DefaultSimpleTool(admin_base_path('stores/resetCache'), '刷新缓存', 'right', 'warning', 'refresh'));
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Store::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('title', __('Title'));
        $show->field('sort', __('Sort'))->editable()->sortable();
        $show->field('thumbnail', __('Thumbnail'))->image();
        $show->field('address', __('Address'));
        $show->field('latitude', __('Latitude'));
        $show->field('longitude', __('Longitude'));
//        $show->field('point', __('Point'));
//        $show->field('geohash', __('Geohash'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
//        $show->field('deleted_at', __('Deleted at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Store);

        $form->text('title', __('Store title'));
        $form->image('thumbnail', __('Thumbnail'))->uniqueName();
        $form->amap('latitude', 'longitude', 'address', '位置选择')->default([
            'lat' => 23.021016,
            'lng' => 113.751884,
            'address' => '广东省东莞市南城街道东莞市人民政府',
        ]);
//        $form->text('address', __('Address'));
//        $form->decimal('latitude', __('Latitude'));
//        $form->decimal('longitude', __('Longitude'));
        $form->hidden('point', __('Point'));
        $form->hidden('geohash', __('Geohash'));
        form_sort($form);
        form_has_enabled($form);

        return $form;
    }

    public function resetCache()
    {
        Store::getByCache(true);
        admin_success('刷新缓存成功');
        return redirect(admin_base_path('stores'));
    }
}
