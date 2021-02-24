<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Actions\GetButton;
use App\Models\Author;
use App\Models\Category;
use App\Models\Goods;
use App\Models\Press;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\Admin\Extensions\Actions\CopyInfoButton;


class GoodsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '商品管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Goods);

        $grid->model()->with(['specifications'])->recent();

        $grid->column('id', __('Id'));
        grid_display_relation($grid, 'category');
        $grid->column('title', __('Title'));
        $grid->column('thumbnail', __('Thumbnail'))->image();
        $grid->column('max_price', __('Max price'))->currency();
        $grid->column('min_price', __('Min price'))->currency();

        $states = [
            'on'  => ['value' => 1, 'text' => '打开', 'color' => 'primary'],
            'off' => ['value' => 0, 'text' => '关闭', 'color' => 'default'],
        ];
        $grid->column('has_enabled', __('Has enabled'))->switch($states)->filter(HAS_ENABLED2TEXT);
        $grid->column('has_recommended', __('Has recommended'))->filter(YN2TEXT)->switch($states);
        $grid->column('sort', __('Sort'));
        $grid->column('created_at', __('Created at'))->sortable()->filter('range', 'datetime');
        $grid->column('updated_at', __('Updated at'))->sortable()->filter('range', 'datetime');

        $grid->column('specifications', '商品规格')->display(function () {
            if (empty($this->specifications)) {
                $specificationList = [];
            } else if (is_array($this->specifications)) {
                $specificationList = $this->specifications;
            } else {
                $specificationList = $this->specifications->toArray();
            }
            $specifications = array_map(function ($item) {
                return [
                    '规格名称' => $item['title'],
                    '售价' => '￥'.strval($item['price'] * 0.01),
                    '库存' => $item['quantity'],
                    '销量' => $item['sold_quantity'],
                    '重量' => $item['weight'],
                    '启用？' => HAS_ENABLED2TEXT[$item['has_enabled']],
                ];
            }, $specificationList);
            return $specifications;
        })->table();

        $grid->actions(function (Grid\Displayers\Actions $actions) {
            $actions->append(new CopyInfoButton(
                '复制代码',
                $this->row->ad_code
            ));
        });

        $grid->filter(function ($filter) {
            $filter->like('title', '名称');
            $filter->equal('category_id', '所属分类')->select(Category::all()->pluck('title', 'id')->toArray());

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
        $show = new Show(Goods::findOrFail($id));

        $show->field('id', __('Id'));
        show_display_relation($show, 'category');
        $show->field('title', __('Title'));
        $show->field('thumbnail', __('Thumbnail'))->image();
        $show->field('images', __('Images'))->image();
        $show->field('detail', __('Detail'))->unescape();
        $show->field('max_price', __('Max price'))->as(function ($item) {
            return '￥'.$item*0.01;
        });
        $show->field('min_price', __('Min price'))->as(function ($item) {
            return '￥'.$item*0.01;
        });
        $show->field('has_enabled', __('Has enabled'))->using(HAS_ENABLED2TEXT);
        $show->field('has_recommended', __('Has recommended'))->using(YN2TEXT);
        $show->field('sort', __('Sort'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        $show->specifications('规格', function (Grid $grid) {
            $grid->column('id', __('Id'));
            $grid->column('title', __('Title'));
            $grid->column('thumbnail', __('Thumbnail'))->image();
            $grid->column('price', __('Price'))->currency();
            $grid->column('quantity', __('Quantity'));
            $grid->column('sold_quantity', __('Sold quantity'));
            $grid->column('weight', __('Weight'));
            $grid->column('has_enabled', __('Has enabled'))->using(HAS_ENABLED2TEXT);
            $grid->column('sort', __('Sort'));
            $grid->column('created_at', __('Created at'));
            $grid->column('updated_at', __('Updated at'));

            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->disableFilter();
            $grid->disableRowSelector();
            $grid->actions(function (Grid\Displayers\Actions $actions) {
                 $actions->disableView();
                 $actions->disableEdit();
                 $actions->disableDelete();
                 $actions->append(new GetButton('/admin/discountGoods/create?goods_specification_id='.$actions->getKey(), '设置折扣'));
            });
        });

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Goods);

        Category::form_display_select($form, 'category_id')->required();
        $form->text('title', __('Title'))->required();
        $form->text('isbn', __('Isbn'))->required();
        $form->text('barcode', __('Barcode'))->required();
        $form->image('thumbnail', __('Thumbnail'))->uniqueName();
        $form->multipleImage('images', __('Images'))->uniqueName()->removable();
        $form->editor('detail', __('Detail'));

        $form->date('publish_date', __('Publish date'))->format('YYYY-MM');
        Press::form_display_select($form, 'press_id')->default(1);
        Author::form_display_select($form, 'authors', 'name', __('Authors'), false, 'name', 'multipleSelect');
//        $form->multipleSelect('authors', __('Authors'))->options(Author::all()->pluck('name', 'id'));

        $form->switch('has_enabled', __('Has enabled'))->default(1);
        $form->switch('has_recommended', __('Has recommended'))->default(1);
        $form->number('sort', __('Sort'))->default(0);

        $form->hasMany('specifications', '商品规格',  function (Form\NestedForm $form) {
            $form->text('title', __('Title'))->rules('required');
            $form->image('thumbnail', __('Thumbnail'))->uniqueName()->removable()->addElementClass('specifications_'.random_string(10));
            $form->currency('price', __('Price'))->rules(['required', 'min:1', 'numeric']);
            $form->number('quantity', __('Quantity'))->default(1)->rules(['required', 'integer', 'min:0']);
            $form->number('sold_quantity', __('Sold quantity'))->default(0)->rules(['required', 'integer', 'min:0']);
            $form->weight('weight', __('Weight'))->default(0)->rules(['required', 'min:0']);
            $form->number('sort', __('Sort'))->default(0);
            $form->switch('has_enabled', __('Has enabled'))->default(1);
        });

        $form->saving(function ($form) {
            if (is_null($form->images)) {
                if ($form->thumbnail) {
                    $form->images = [$form->thumbnail];
                } else {
                    $form->images = [];
                }
            };
//            $form->authors = array_filter($form->authors);

//            $form->authors = [[1], [2]];
        });

        return $form;
    }
}
