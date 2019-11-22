<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Actions\AjaxWithFormButton;
use App\Admin\Extensions\Actions\GetButton;
use App\Models\Express;
use App\Models\LotteryRecord;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;
use Illuminate\Http\Request;

class LotteryRecordsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '中奖记录管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new LotteryRecord);

        $grid->column('id', __('Id'));
        $grid->column('prize_snapshot', __('Prize id'))->display(function ($item) {
            return "{$item['title']}";
        });
        grid_display_relation($grid, 'owner', 'nickname');

        $grid->column('address_snapshot', __('Address'))->display(function ($item) {
            if (empty($item)) return '';
            return "{$item['name']}|{$item['mobile']}<br>{$item['city_name']}-{$item['address']}";
        });

        grid_display_relation($grid, 'express', 'name');
        $grid->column('express_number', __('Express number'));

        $grid->column('expressed_at', __('Expressed at'));
        $grid->column('status', __('Status'))->using(LotteryRecord::status_text)->filter(LotteryRecord::status_text);
        $grid->column('created_at', __('Created at'));

        $grid->actions(function (Grid\Displayers\Actions $actions) {
            $actions->disableEdit();
        });
        $grid->actions(function (Grid\Displayers\Actions $actions) {
            $actions->disableEdit();
            if ($this->row->status === 1 && $this->row->address_id != 0) {
                $actions->append(new AjaxWithFormButton(
                    $actions->getResource() . '/' . $actions->getKey() . '/express',
                    '发货',
                    [
                        'title' => '发货',
                        'footer' => '上门自提或其他非快递配送，可选择无需物流',
                    ],
                    [
                        [
                            'title' => '物流公司',
                            'name' => 'express_id',
                            'input' => 'select',
                            'inputOptions' => array_merge(['无需物流'], Express::all(['id', 'name'])->pluck('name', 'id')->toArray()),
                            'inputValue' => config('system.express_company_id', 1)
                        ],
                        [
                            'title' => '物流单号',
                            'name' => 'express_number',
                            'input' => 'text',
                            'text' => '请输入',
                            'inputPlaceholder' => '无需物流可不填'
                        ]
                    ]
                ));
            }
            if ($this->row->status > 1 && $this->row->express_id) {
                $actions->append(new GetButton(
                    $actions->getResource() . '/' . $actions->getKey() . '/logistics',
                    '物流查询'
                ));
            }
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
        $show = new Show(LotteryRecord::findOrFail($id));

        $show->field('id', __('Id'));
        show_display_relation($show, 'prize');
        $show->field('prize_snapshot', __('Prize snapshot'))->unescape()->array2json();
        show_display_relation($show, 'owner', 'nickname');
        show_display_relation($show, 'express', 'name');

        $show->field('express_number', __('Express number'));
        $show->field('address_snapshot', __('Address snapshot'))->unescape()->as(function ($item) {
            if (empty($item)) return '';

            return "{$item['name']}|{$item['mobile']}<br>{$item['city_name']}-{$item['address']}";
        });
        $show->field('expressed_at', __('Expressed at'));
        $show->field('status', __('Status'))->using(LotteryRecord::status_text);
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
        $form = new Form(new LotteryRecord);

        return $form;
    }

    public function express(LotteryRecord $record, Request $request)
    {
        if ($record->status !== 1) {
            $data = [
                'status'  => false,
                'message' => '订单状态异常',
            ];
            return response()->json($data);
        }

        if (! empty($request->express_id)) {
            if (empty($request->express_number)) {
                $data = [
                    'status'  => false,
                    'message' => '请输入快递单号',
                ];
                return response()->json($data);
            }
        }

        $record->expressing($request->express_number, $request->express_id ?: 0);

        $data = [
            'status'  => true,
            'message' => '操作成功',
        ];
        return response()->json($data);
    }

    public function logistics(LotteryRecord $record, Content $content)
    {
        try {
            $res = $record->getLogistics();
            $content->title($this->title);

            $header = [
                'id', '内容', '时间'
            ];
            $body = [];
            foreach ($res['data'] as $key => $data) {
                $body[] = [
                    $key+1,
                    $data['context'],
                    $data['time'],
                ];
            }
            $box = new Box("【{$res['com_name']}：{$record->express_number}】物流信息（最终状态：".LotteryRecord::express_status_text[$res['state']]."）", new Table($header, $body));
            $content->body($box);

            return $content;
        } catch (\Exception $exception) {
            admin_toastr($exception->getMessage(), 'error');
            return redirect()->back();
        }
    }
}
