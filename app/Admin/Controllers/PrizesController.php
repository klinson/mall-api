<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Actions\AjaxWithInputButton;
use App\Models\Prize;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Http\Request;

class PrizesController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '奖品管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Prize);

        $grid->header(function ($query) {
            return "<b>当前【谢谢参与】的权值：". Prize::getNonPrizeRate() . '，实际概率：'.Prize::getNonPrizeRealRate() . '</b> <a href="/admin/system?active=lottery" target="_blank" class="btn btn-primary btn-xs">点击修改</a>';
        });
        $grid->footer(function () {
            return '<b>概率算法：概率=当前奖品权值/（所有奖品总权值+【谢谢参与】权值）</b>';

        });

        $grid->column('id', __('Id'));
        $grid->column('title', __('Title'));
        $grid->column('thumbnail', __('Thumbnail'))->image();
        $grid->column('origin_quantity', __('Origin quantity'));
        $grid->column('quantity', __('Quantity'))->display(function () {
            return $this->real_quantity;
        });
        $grid->column('price', __('Price'))->currency();
        $grid->column('level', __('Level'));
        $grid->column('rate', __('Rate'));
        $grid->column('real_rate', '实际概率')->display(function () {
            return $this->real_rate;
        });
        grid_has_enabled($grid);

        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        $grid->actions(function (Grid\Displayers\Actions $actions) {
            $actions->append(new AjaxWithInputButton(
                $actions->getResource() . '/' . $actions->getKey() . '/updateQuantity',
                '加减奖品数量',
                'quantity',
                '加减奖品数量, 减10请输入"-10",加1请输入"10"：'
            ));
        });
        return $grid;
    }

    public function updateQuantity(Prize $prize, Request $request)
    {
        $quantity = intval($request->quantity);
        if ($quantity !== 0) {
            $prize->updateQuantity($quantity);
        }
        $data = [
            'status'  => true,
            'message' => '操作成功',
        ];
        return response()->json($data);
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Prize::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('title', __('Title'));
        $show->field('thumbnail', __('Thumbnail'))->image();
        $show->field('origin_quantity', __('Origin quantity'));
        $show->field('quantity', __('Quantity'))->as(function () {
            return $this->real_quantity;
        });
        $show->field('price', __('Price'));
        $show->field('level', __('Level'))->currency();
        $show->field('rate', __('Rate'));
        $show->field('real_rate', '实际概率')->as(function () {
            return $this->real_rate;
        });
        $show->field('has_enabled', __('Has enabled'))->using(HAS_ENABLED2TEXT);
        $show->field('created_at', __('Created at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Prize);

        $form->text('title', __('Title'))->required();
        $form->image('thumbnail', __('Thumbnail'))->uniqueName();
        $form->number('origin_quantity', __('Origin quantity'))->default(1)->required();
        if ($form->isCreating()) {
            $form->number('quantity', __('Quantity'))->default(1)->required();
        }
        $form->currency('price', __('Price'))->required();
        $form->number('level', __('Level'))->default(1)->required();
        $form->number('rate', __('Rate'))->required()->min(0)->help('中奖概率=当前奖品权值/（所有奖品总权值+【谢谢参与】权值('.Prize::getNonPrizeRate().')）');
        $form->switch('has_enabled', __('Has enabled'))->default(1);

        return $form;
    }
}
