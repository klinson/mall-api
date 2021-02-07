<?php

use Illuminate\Database\Seeder;

class CarouselAdItemsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('carousel_ad_items')->delete();
        
        \DB::table('carousel_ad_items')->insert(array (
            0 => 
            array (
                'id' => 1,
                'sort' => 0,
                'carousel_ad_id' => 1,
                'picture' => 'images/ga8out1bXRtZ9jJf9BrSIxFQmO0LtXEKmE5DsNYL.jpeg',
                'item_title' => '图书分类',
                'url' => 'goods-1',
            ),
            1 => 
            array (
                'id' => 2,
                'sort' => 0,
                'carousel_ad_id' => 1,
                'picture' => 'images/OFazRSTdMXzr946mKKs61qB7al6d6kvDzxXYG4n1.jpeg',
                'item_title' => '1号商品',
                'url' => 'goods-1',
            ),
            2 => 
            array (
                'id' => 3,
                'sort' => 0,
                'carousel_ad_id' => 1,
                'picture' => 'images/nK5RxPoAcxC6DCY3DGGq6kQpAYehzJwfJmMJtwj8.jpeg',
                'item_title' => '关于我们文章',
                'url' => 'article-1',
            ),
            3 => 
            array (
                'id' => 4,
                'sort' => 0,
                'carousel_ad_id' => 1,
                'picture' => 'images/sGWJZb1IU2dSQo9TBQXgGKtnOdWrb4RLCuxIE0K5.jpeg',
                'item_title' => '跳转外链',
                'url' => 'https://klinson.com',
            ),
            4 => 
            array (
                'id' => 5,
                'sort' => 0,
                'carousel_ad_id' => 2,
                'picture' => 'images/pLgHzc3nlFk61x0eBV1vKDR3hNevXtHO6tCxRL1C.png',
                'item_title' => '分类1',
                'url' => 'category-1',
            ),
            5 => 
            array (
                'id' => 6,
                'sort' => 0,
                'carousel_ad_id' => 2,
                'picture' => 'images/cLu62RtlBtt1sePkDcvsBmtR9jdqROv1pOLEDhsq.png',
                'item_title' => '分类2',
                'url' => 'category-2',
            ),
            6 => 
            array (
                'id' => 7,
                'sort' => 0,
                'carousel_ad_id' => 2,
                'picture' => 'images/Uv03Ja6cHxKTeYWV0tEUCvJSm3BfcPz6NVeGpFug.png',
                'item_title' => '分类3',
                'url' => 'category-3',
            ),
            7 => 
            array (
                'id' => 8,
                'sort' => 0,
                'carousel_ad_id' => 2,
                'picture' => 'images/Oxd3Sen1kQC73zUUvIOEduNeYrM5WAyWbGuOMCHw.png',
                'item_title' => '分类4',
                'url' => 'category-4',
            ),
        ));
        
        
    }
}