<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $menus=[

            [
                'name'=>'食品',
                'pic'=>'food.png',
                'pid'=>'0',
                'level'=>'1',
                'children'=>[
                    [
                        'name'=>'熟食',
                        'level'=>'2',
                    ],
                    [
                        'name'=>'食材',
                        'level'=>'2',
                    ],
                    [
                        'name'=>'速冻食品',
                        'level'=>'2',
                    ],
                    [
                        'name'=>'乳制品',
                        'level'=>'2',
                    ],
                    [
                        'name'=>'其他',
                        'level'=>'2',
                    ],
                ],
            ],

            [
                'name'=>'生鲜',
                'pic'=>'fresh.png',
                'pid'=>'0',
                'level'=>'1',
                'children'=>[
                    [
                        'name'=>'蔬菜',
                        'level'=>'2',
                    ],
                    [
                        'name'=>'水果',
                        'level'=>'2',
                    ],
                    [
                        'name'=>'海鲜',
                        'level'=>'2',
                    ],
                    [
                        'name'=>'肉禽蛋',
                        'level'=>'2',
                    ],
                ],
            ],

            [
                'name'=>'日用品',
                'pic'=>'dailynecessities.png',
                'pid'=>'0',
                'level'=>'1',
                'children'=>[
                    [
                        'name'=>'调味料',
                        'level'=>'2',
                    ],
                    [
                        'name'=>'纸品清洁',
                        'level'=>'2',
                    ],
                    [
                        'name'=>'厨卫',
                        'level'=>'2',
                    ],
                    [
                        'name'=>'餐具',
                        'level'=>'2',
                    ],
                ],
            ],

            [
                'name'=>'零食',
                'pic'=>'snaks.png',
                'pid'=>'0',
                'level'=>'1',
                'children'=>[
                    [
                        'name'=>'糖果',
                        'level'=>'2',
                    ],
                    [
                        'name'=>'坚果',
                        'level'=>'2',
                    ],
                    [
                        'name'=>'膨化食品',
                        'level'=>'2',
                    ],
                    [
                        'name'=>'熟食',
                        'level'=>'2',
                    ],
                    [
                        'name'=>'饮料',
                        'level'=>'2',
                    ],
                    [
                        'name'=>'其他',
                        'level'=>'2',
                    ],
                ],
            ],

            [
                'name'=>'百货',
                'pic'=>'sundries.png',
                'pid'=>'0',
                'level'=>'1',
                'children'=>[
                    [
                        'name'=>'一次性用品',
                        'level'=>'2',
                    ],
                    [
                        'name'=>'文娱办公',
                        'level'=>'2',
                    ],
                    [
                        'name'=>'家居用品',
                        'level'=>'2',
                    ],
                ],
            ],

            [
                'name'=>'数码',
                'pic'=>'digital.png',
                'pid'=>'0',
                'level'=>'1',
                'children'=>[
                    [
                        'name'=>'电脑周边',
                        'level'=>'2',
                    ],
                    [
                        'name'=>'手机配件',
                        'level'=>'2',
                    ],
                ],
            ],
            
        ];


        foreach($menus as $val){
            $children=$val['children'];
            unset($val['children']);
            $menu=Category::create($val);
            $menu->children()->createMany($children);
        }
    }
}
