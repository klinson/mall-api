<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Actions\CopyInfoButton;
use App\Models\DiscountGoods;
use App\Models\GoodsSpecification;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
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
        $grid->column('tags_array', __('Tags'))->display(function () {
            return $this->tags_array;
        })->label();
        $grid->column('created_at', __('Created at'));

        $grid->disableCreateButton();
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
        $show->field('tags_array', __('Tags'))->label();
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    public function create(Content $content)
    {
        $specification_id = request('goods_specification_id', 0);
        $specification = GoodsSpecification::find($specification_id);
        if (empty($specification)) {
            admin_toastr('商品不存在', 'error');
            return back();
        }

        $form = new Form(new DiscountGoods);

        $form->hidden('goods_id')->value($specification->goods_id);
        $form->hidden('goods_specification_id')->value($specification->id);
        $form->display('goods', '商品信息')->value($specification->goods->title);
        $form->display('goods_specification', '规格信息')->value($specification->title);

        $form->text('title', __('Title'))->required();
        $form->image('thumbnail', __('Thumbnail'))->removable()->uniqueName()->value($specification->thumbnail ?: $specification->goods->thumbnail);
        $form->multipleImage('images', __('Images'))->uniqueName()->removable()->value($specification->goods->images);
        $form->tagsinput('tags', __('Tags'))->help('输入标签后回车');

        $form->currency('price', __('Price'))->rules(['required', 'min:1', 'numeric'])->help('当前原价：￥'.strval($specification->price * 0.01))->default($specification->price);
        $form->number('quantity', __('Quantity'))->default(1)->rules(['required', 'integer', 'min:0']);
        $form->number('sold_quantity', __('Sold quantity'))->default(0)->rules(['required', 'integer', 'min:0']);
        $form->decimal('weight', __('Weight'))->default($specification->weight)->rules(['required', 'min:0']);
        $form->switch('has_enabled', __('Has enabled'))->default(1);
        $form->number('sort', __('Sort'))->default(0);


        $form->editor('detail', __('Detail'))->value($specification->goods->detail);

        return $content
            ->title($this->title())
            ->description($this->description['create'] ?? trans('admin.create'))
            ->body($form);
    }

    public function edit($id, Content $content)
    {
        $discountGoods = DiscountGoods::find($id);
        if (empty($discountGoods) || empty($discountGoods->goods) || empty($discountGoods->specification)) {
            admin_toastr('商品不存在', 'error');
            return back();
        }

        $form = new Form(new DiscountGoods);

        $form->hidden('goods_id')->value($discountGoods->goods_id);
        $form->hidden('goods_specification_id')->value($discountGoods->goods_specification_id);

        $form->display('_goods', '商品信息')->default($discountGoods->goods->title);
        $form->display('_goods_specification', '规格信息')->default($discountGoods->specification->title);

        $form->text('title', __('Title'))->required();
        $form->image('thumbnail', __('Thumbnail'))->removable()->uniqueName();
        $form->multipleImage('images', __('Images'))->uniqueName()->removable();
        $form->tagsinput('tags', __('Tags'))->help('输入标签后回车');

        $form->currency('price', __('Price'))->rules(['required', 'min:1', 'numeric'])->help('当前原价：￥'.strval($discountGoods->specification->price * 0.01));
        $form->number('quantity', __('Quantity'))->default(1)->rules(['required', 'integer', 'min:0']);
        $form->number('sold_quantity', __('Sold quantity'))->default(0)->rules(['required', 'integer', 'min:0']);
        $form->decimal('weight', __('Weight'))->rules(['required', 'min:0']);
        $form->switch('has_enabled', __('Has enabled'))->default(1);
        $form->number('sort', __('Sort'))->default(0);
        $form->editor('detail', __('Detail'));

        return $content
            ->title($this->title())
            ->description($this->description['edit'] ?? trans('admin.edit'))
            ->body($form->edit($id));
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new DiscountGoods);

        $form->hidden('goods_id');
        $form->hidden('goods_specification_id');

        $form->text('title', __('Title'))->required();
        $form->currency('price', __('Price'))->rules(['required', 'min:1', 'numeric']);
        $form->number('quantity', __('Quantity'))->default(1)->rules(['required', 'integer', 'min:0']);
        $form->number('sold_quantity', __('Sold quantity'))->default(0)->rules(['required', 'integer', 'min:0']);
        $form->decimal('weight', __('Weight'))->rules(['required', 'min:0']);
        $form->switch('has_enabled', __('Has enabled'))->default(1);
        $form->number('sort', __('Sort'));
        $form->image('thumbnail', __('Thumbnail'))->removable()->uniqueName();
        $form->multipleImage('images', __('Images'))->uniqueName()->removable();
        $form->editor('detail', __('Detail'));
        $form->tagsinput('tags', __('Tags'));

        $form->saving(function ($form) {
            if (is_null($form->images)) {
                if ($form->thumbnail) {
                    $form->images = [$form->thumbnail];
                } else {
                    $form->images = [];
                }
            };
        });

        return $form;
    }
}
