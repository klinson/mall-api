<?php

use Illuminate\Database\Seeder;

class ArticlesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('articles')->delete();
        
        \DB::table('articles')->insert(array (
            0 => 
            array (
                'id' => 1,
                'title' => '关于我们',
                'content' => '<p style="text-align: center;"><br/></p><p style="text-align: center;">本小程序归东莞市韩苡琳科技有限公司所有</p><p><br/></p><p style="text-align: center;">技术支持：klinson.com<br/></p>',
                'created_at' => '2019-10-23 00:48:21',
                'updated_at' => '2019-10-23 00:48:21',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'title' => '申请入驻',
                'content' => '<p style="text-align: center;">欢迎入驻本平台，您可以将您的产品挂在本平台上进行售卖！</p><p style="text-align: center;">请直接联系我们0769-81181223</p>',
                'created_at' => '2019-10-23 00:49:36',
                'updated_at' => '2019-10-23 00:49:36',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'title' => '抽奖公告',
                'content' => '<p><span style="font-size: 20px;"><strong>抽奖机会获得方式：</strong></span></p><ol class=" list-paddingleft-2" style="list-style-type: decimal;"><li><p>&nbsp;关注3个商品获得一次抽奖机会<br/></p></li><li><p>邀请宝妈好友扫码注册并关注2个商品，还可以再得一次抽奖机会，最多可以获得三次抽奖机会</p></li></ol><p><br/></p><p><span style="font-size: 20px;"><strong>活动说明：</strong></span></p><ol class=" list-paddingleft-2" style="list-style-type: decimal;"><li><p></p>抽奖100%中奖</li><li><p>中奖后请填写快递地址</p></li></ol><p><br/></p><p>感谢您对我们的支持</p><p><br/></p><p><br/></p><p><br/></p><p style="text-align: center;">技术支持 klinson.com</p>',
                'created_at' => '2019-11-06 12:56:19',
                'updated_at' => '2019-11-06 12:56:19',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}