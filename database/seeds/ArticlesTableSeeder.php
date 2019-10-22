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
        ));
        
        
    }
}