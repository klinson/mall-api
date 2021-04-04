<?php

namespace App\Admin\Controllers;

use App\Models\AgencyConfig;
use App\Models\CofferLog;
use App\Models\IntegralLog;
use App\Models\User;
use App\Models\UserHasCoupon;
use App\Models\WalletLog;
use App\Rules\CnMobile;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class UsersController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '用户管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new User);
        $grid->model()->with(['agency', 'wallet', 'integral', 'score.memberLevel'])->orderBy('created_at', 'desc');

        $grid->column('id', __('Id'));
        $grid->column('nickname', __('Nickname'));
        $grid->column('avatar', __('Avatar'))->image();
//        grid_display_relation($grid, 'agency', 'title');
        $grid->column('sex', __('Sex'))->using(User::SEX2TEXT)->filter(User::SEX2TEXT);;
        $grid->column('mobile', __('Mobile'));
        $grid->column('wallet', __('Wallet'))->display(function ($item) {
            return strval($this->wallet->balance * 0.01);
        });
        $grid->column('integral', __('Integral'))->display(function ($item) {
            return $this->integral->balance;
        });
        $grid->column('memberLevel', __('Member level id'))->display(function ($item) {
            return $this->score->memberLevel->title;
        });
        $grid->column('score', __('Score'))->display(function ($item) {
            return $this->score->balance;
        });
//        $grid->column('coffer', '金库(已结算/待结算)')->display(function ($item) {
//            if (empty($this->coffer)) return '';
//            return strval($this->coffer->balance * 0.01).'/'.strval($this->coffer->unsettle_balance * 0.01);
//        });
        $grid->column('max_discount', '会员折扣')->display(function () {
            $discount = $this->getBestMemberDiscount();
            return $discount >= 100 ? '无' : $discount*0.1 . '折';
        });
        $grid->column('has_fee_freight', '包邮？')->display(function () {
            return $this->hasFeeFreight();
        })->using(YN2TEXT);

        $states = [
            'on'  => ['value' => 1, 'text' => '职员', 'color' => 'primary'],
            'off' => ['value' => 0, 'text' => '用户', 'color' => 'default'],
        ];
        $grid->column('is_staff', __('Is staff'))->switch($states);

        $grid->column('created_at', __('Created at'))->sortable()->filter('range', 'datetime');

        $grid->disableCreateButton();

        $grid->filter(function(Grid\Filter $filter){
            $filter->like('nickname', __('Nickname'));
            $filter->like('mobile', __('Mobile'));
            $filter->like('wxapp_openid', __('Wxapp openid'));
            $filter->equal('agency_id', __('Agency id'))->select(AgencyConfig::all(['id', 'title'])->pluck('title', 'id')->toArray());
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
        $show = new Show(User::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('avatar', __('Avatar'))->image();
        $show->field('nickname', __('Nickname'));
        $show->field('wxapp_openid', __('Wxapp openid'));
        $show->field('sex', __('Sex'))->using(User::SEX2TEXT);
        $show->field('mobile', __('Mobile'));
        $show->field('wxapp_userinfo', __('Wxapp userinfo'))->unescape()->array2json();
        $show->field('wallet', __('Wallet'))->as(function ($item) {
            return strval($this->wallet->balance * 0.01);
        });
        $show->field('integral', __('Integral'))->as(function ($item) {
            return $this->integral->balance;
        });
        $show->field('memberLevel', __('Member level id'))->as(function ($item) {
            return $this->score->memberLevel->title;
        });
        $show->field('score', __('Score'))->as(function ($item) {
            return $this->score->balance;
        });
        $show->field('max_discount', '会员折扣')->as(function () {
            $discount = $this->getBestMemberDiscount();
            return $discount >= 100 ? '无' : $discount*0.1 . '折';
        });
        $show->field('has_fee_freight', '包邮？')->as(function () {
            return $this->hasFeeFreight();
        })->using(YN2TEXT);

        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        /*
        $show->memberLevels('会员记录', function (Grid $grid) {
            $grid->column('id', __('Id'));
            grid_display_relation($grid, 'memberLevel');
            grid_display_relation($grid, 'order', 'order_number');
            $grid->column('discount', __('Discount'))->display(function () {
                $discount = $this->member_level_snapshot['discount'];
                return $discount >= 100 ? '无' : $discount*0.1 . '折';
            });
            $grid->column('is_fee_freight', '包邮？')->display(function () {
                return $this->member_level_snapshot['is_fee_freight'];
            })->using(YN2TEXT);
            $grid->column('validity_started_at', __('Validity started at'));
            $grid->column('validity_ended_at', __('Validity ended at'));

            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->disableFilter();
            $grid->disableRowSelector();
            $grid->disableActions();
        });
        */

        $show->coupons('优惠券', function (Grid $grid) {
            $grid->model()->recent();
            $grid->column('id', __('Id'));
            grid_display_relation($grid, 'coupon');
            $grid->column('coupon_snapshot', __('Coupon snapshot'))->display(function ($item) {
                return $item['title'];
            });
            $grid->column('discount_money', __('Discount money'))->currency();
            $grid->column('has_enabled', __('Has enabled'))->using(HAS_ENABLED2TEXT);
            $grid->column('status', __('Status'))->using(UserHasCoupon::status_text);
            $grid->column('used_at', __('Used at'));
            $grid->column('description', __('Description'));
            $grid->column('created_at', __('Created at'));

            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->disableFilter();
            $grid->disableRowSelector();
            $grid->disableActions();
        });

        /*
        $show->cofferLogs('金库日志', function (Grid $grid) {
            $grid->model()->recent();
            $grid->column('id', __('Id'));
            $grid->column('balance', __('Balance'))->currency();
            $grid->column('type', __('Type'))->using(CofferLog::type_text);
            $grid->column('description', __('Description'));
            $grid->column('ip', __('Ip'));
            $grid->column('created_at', __('Created at'));
            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->disableFilter();
            $grid->disableRowSelector();
            $grid->disableActions();
        });
        */

        $show->walletLogs('钱包日志', function (Grid $grid) {
            $grid->model()->recent();
            $grid->column('id', __('Id'));
            $grid->column('balance', __('Balance'))->currency();
            $grid->column('type', __('Type'))->using(WalletLog::type_text);
            $grid->column('description', __('Description'));
            $grid->column('ip', __('Ip'));
            $grid->column('created_at', __('Created at'));
            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->disableFilter();
            $grid->disableRowSelector();
            $grid->disableActions();
        });

        $show->integralLogs('积分日志', function (Grid $grid) {
            $grid->model()->recent();
            $grid->column('id', __('Id'));
            $grid->column('balance', __('Balance'));
            $grid->column('type', __('Type'))->using(IntegralLog::type_text);
            $grid->column('description', __('Description'));
            $grid->column('ip', __('Ip'));
            $grid->column('created_at', __('Created at'));
            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->disableFilter();
            $grid->disableRowSelector();
            $grid->disableActions();
        });

        $show->scoreLogs('会员经验日志', function (Grid $grid) {
            $grid->model()->recent();
            $grid->column('id', __('Id'));
            $grid->column('balance', __('Balance'));
            $grid->column('type', __('Type'))->using(IntegralLog::type_text);
            $grid->column('description', __('Description'));
            $grid->column('ip', __('Ip'));
            $grid->column('created_at', __('Created at'));
            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->disableFilter();
            $grid->disableRowSelector();
            $grid->disableActions();
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
        $form = new Form(new User);

        $form->text('wxapp_openid', __('Wxapp openid'))->required()->rules(['max:28']);
        $form->text('nickname', __('Nickname'))->required()->rules(['max:30']);
        $form->select('sex', __('Sex'))->options(User::SEX2TEXT)->required();
        $form->mobile('mobile', __('Mobile'))->rules([new CnMobile()]);
        $states = [
            'on'  => ['value' => 1, 'text' => '职员', 'color' => 'primary'],
            'off' => ['value' => 0, 'text' => '用户', 'color' => 'default'],
        ];
        $form->switch('is_staff', __('Is staff'))->default(0)->options($states);
        $form->switch('has_enabled', __('Has enabled'))->default(1);

        return $form;
    }
}
