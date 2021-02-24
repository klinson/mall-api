<?php

use Illuminate\Database\Seeder;

class PressesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('presses')->delete();
        
        \DB::table('presses')->insert(array (
            0 => 
            array (
                'id' => 1,
                'title' => '人民出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'title' => '人民文学出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'title' => '高等教育出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'title' => '科学出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'title' => '人民邮电出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'title' => '商务印书馆',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
                'title' => '中华书局',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            7 => 
            array (
                'id' => 8,
                'title' => '机械工业出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            8 => 
            array (
                'id' => 9,
                'title' => '电子工业出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            9 => 
            array (
                'id' => 10,
                'title' => '中国农业出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            10 => 
            array (
                'id' => 11,
                'title' => '中国大百科全书出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            11 => 
            array (
                'id' => 12,
                'title' => '人民卫生出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            12 => 
            array (
                'id' => 13,
                'title' => '中国财政经济出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            13 => 
            array (
                'id' => 14,
                'title' => '化学工业出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            14 => 
            array (
                'id' => 15,
                'title' => '石油工业出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            15 => 
            array (
                'id' => 16,
                'title' => '法律出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            16 => 
            array (
                'id' => 17,
                'title' => '国防工业出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            17 => 
            array (
                'id' => 18,
                'title' => '中国轻工业出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            18 => 
            array (
                'id' => 19,
                'title' => '经济管理出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            19 => 
            array (
                'id' => 20,
                'title' => '中国社会科学出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            20 => 
            array (
                'id' => 21,
                'title' => '中国电力出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            21 => 
            array (
                'id' => 22,
                'title' => '中国建筑工业出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            22 => 
            array (
                'id' => 23,
                'title' => '中国水利水电出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            23 => 
            array (
                'id' => 24,
                'title' => '经济科学出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            24 => 
            array (
                'id' => 25,
                'title' => '三联书店',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            25 => 
            array (
                'id' => 26,
                'title' => '地质出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            26 => 
            array (
                'id' => 27,
                'title' => '海洋出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            27 => 
            array (
                'id' => 28,
                'title' => '气象出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            28 => 
            array (
                'id' => 29,
                'title' => '冶金工业出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            29 => 
            array (
                'id' => 30,
                'title' => '作家出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            30 => 
            array (
                'id' => 31,
                'title' => '人民体育出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            31 => 
            array (
                'id' => 32,
                'title' => '中国文联出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            32 => 
            array (
                'id' => 33,
                'title' => '中国科学技术出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            33 => 
            array (
                'id' => 34,
                'title' => '兵器工业出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            34 => 
            array (
                'id' => 35,
                'title' => '航空工业出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            35 => 
            array (
                'id' => 36,
                'title' => '语文出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            36 => 
            array (
                'id' => 37,
                'title' => '人民音乐出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            37 => 
            array (
                'id' => 38,
                'title' => '中国计量出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            38 => 
            array (
                'id' => 39,
                'title' => '中国环境科学出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            39 => 
            array (
                'id' => 40,
                'title' => '地震出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            40 => 
            array (
                'id' => 41,
                'title' => '军事科学出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            41 => 
            array (
                'id' => 42,
                'title' => '原子能出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            42 => 
            array (
                'id' => 43,
                'title' => '中国文学出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            43 => 
            array (
                'id' => 44,
                'title' => '宇航出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            44 => 
            array (
                'id' => 45,
                'title' => '外国文学出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            45 => 
            array (
                'id' => 46,
                'title' => '外文出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            46 => 
            array (
                'id' => 47,
                'title' => '清华大学出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            47 => 
            array (
                'id' => 48,
                'title' => '外语教学与研究出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            48 => 
            array (
                'id' => 49,
                'title' => '北京大学出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            49 => 
            array (
                'id' => 50,
                'title' => '中国人民大学出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            50 => 
            array (
                'id' => 51,
                'title' => '复旦大学出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            51 => 
            array (
                'id' => 52,
                'title' => '上海交通大学出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            52 => 
            array (
                'id' => 53,
                'title' => '浙江大学出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
            53 => 
            array (
                'id' => 54,
                'title' => '西安电子科技大学出版社',
                'created_at' => '2021-02-24 23:14:36',
                'updated_at' => '2021-02-24 23:14:36',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}