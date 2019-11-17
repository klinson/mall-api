<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Actions\CopyInfoButton;
use App\Models\DiscountGoods;
use App\Models\GoodsSpecification;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class DiscountGoodsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '秒杀折扣商品管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new DiscountGoods);

        $grid->column('id', __('Id'));
        $grid->column('title', __('Title'));
        grid_display_relation($grid, 'goods');
        grid_display_relation($grid, 'specification');
        $grid->column('price', __('Price'))->currency();
        $grid->column('quantity', __('Quantity'));
        $grid->column('sold_quantity', __('Sold quantity'));
        grid_has_enabled($grid);
        $grid->column('sort', __('Sort'));
        $grid->column('thumbnail', __('Thumbnail'))->image();
        $grid->column('tags', __('Tags'))->label();
        $grid->column('created_at', __('Created at'));

        $grid->actions(function (Grid\Displayers\Actions $actions) {
            $actions->append(new CopyInfoButton(
                '复制代码',
                $this->row->ad_code
            ));
        });

        $grid->filter(function ($filter) {
            $filter->like('title', '名称');

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
        $show = new Show(DiscountGoods::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('title', __('Title'));

        show_display_relation($show, 'goods');
        show_display_relation($show, 'specification');

        $show->field('price', __('Price'))->currency();
        $show->field('quantity', __('Quantity'));
        $show->field('sold_quantity', __('Sold quantity'));
        $show->field('weight', __('Weight'));
        $show->field('has_enabled', __('Has enabled'))->using(HAS_ENABLED2TEXT);
        $show->field('sort', __('Sort'));
        $show->field('thumbnail', __('Thumbnail'))->image();
        $show->field('images', __('Images'))->image();
        $show->field('detail', __('Detail'))->unescape();
        $show->field('tags', __('Tags'))->label();
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new DiscountGoods);

        if ($form->isCreating()) {
            $specification_id = request('goods_specification_id', 0);
            $specification = GoodsSpecification::find($specification_id);
            if (empty($specification)) {
                admin_toastr('商品不存在', 'error');
                return back();
            }
        } else {
            $specification = $form->model()->specification;
        }


        $form->hidden('goods_id')->value($specification->goods_id);
        $form->hidden('goods_specification_id')->value($specification->id);
        $form->display('goods', '商品信息')->value($specification->goods->title);
        $form->display('goods_specification', '规格信息')->value($specification->title);

        $form->text('title', __('Title'))->required();
        $form->currency('price', __('Price'))->rules(['required', 'min:1', 'numeric'])->help('当前原价：￥'.strval($specification->price * 0.01));
        $form->number('quantity', __('Quantity'))->default(1)->rules(['required', 'integer', 'min:0']);
        $form->number('sold_quantity', __('Sold quantity'))->default(0)->rules(['required', 'integer', 'min:0']);
        $form->decimal('weight', __('Weight'))->default($specification->weight)->rules(['required', 'min:0']);
        $form->switch('has_enabled', __('Has enabled'))->default(1);
        $form->switch('sort', __('Sort'));
        $form->image('thumbnail', __('Thumbnail'))->removable()->uniqueName();
        $form->multipleImage('images', __('Images'))->removable()->uniqueName();
        $form->editor('detail', __('Detail'));
        $form->tagsinput('tags', __('Tags'));

        return $form;
    }
}
