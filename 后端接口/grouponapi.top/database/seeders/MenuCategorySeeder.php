<?php

namespace Database\Seeders;

use App\Models\AdminMenu;
use Illuminate\Database\Seeder;

class MenuCategorySeeder extends Seeder
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
                'name'=>'用户管理',
                'pid'=>'0',
                'level'=>'1',
                'children'=>[
                    [
                        'name'=>'用户列表',
                        'level'=>'2',
                    ],
                    [
                        'name'=>'团长列表',
                        'level'=>'2',
                    ],
                    [
                        'name'=>'团长收益列表',
                        'level'=>'2',
                    ],
                    [
                        'name'=>'团长申请列表',
                        'level'=>'2',
                    ],
                    [
                        'name'=>'团长申请辞职列表',
                        'level'=>'2',
                    ],
                    [
                        'name'=>'团长修改信息请求列表',
                        'level'=>'2',
                    ],
                ],
            ],

            [
                'name'=>'商品管理',
                'pid'=>'0',
                'level'=>'1',
                'children'=>[
                    [
                        'name'=>'商品列表',
                        'level'=>'2',
                    ],
                    [
                        'name'=>'添加商品',
                        'level'=>'2',
                    ],
                ],
            ],

            [
                'name'=>'订单管理',
                'pid'=>'0',
                'level'=>'1',
                'children'=>[
                    [
                        'name'=>'订单列表',
                        'level'=>'2',
                    ],
                ],
            ],

            [
                'name'=>'发货管理',
                'pid'=>'0',
                'level'=>'1',
                'children'=>[
                    [
                        'name'=>'自提点列表',
                        'level'=>'2',
                    ],
                ],
            ],

            [
                'name'=>'退货管理',
                'pid'=>'0',
                'level'=>'1',
                'children'=>[
                    [
                        'name'=>'申请退货列表',
                        'level'=>'2',
                    ]
                ],
            ],

            [
                'name'=>'评价管理',
                'pid'=>'0',
                'level'=>'1',
                'children'=>[
                    [
                        'name'=>'评价列表',
                        'level'=>'2',
                    ],
                ],
            ],

            [
                'name'=>'轮播图管理',
                'pid'=>'0',
                'level'=>'1',
                'children'=>[
                    [
                        'name'=>'轮播图列表',
                        'level'=>'2',
                    ],
                    [
                        'name'=>'添加轮播图',
                        'level'=>'2',
                    ],
                ],
            ],

            [
                'name'=>'分类管理',
                'pid'=>'0',
                'level'=>'1',
                'children'=>[
                    [
                        'name'=>'分类列表',
                        'level'=>'2',
                    ],
                    [
                        'name'=>'添加分类',
                        'level'=>'2',
                    ],
                ],
            ]
            
        ];


        foreach($menus as $val){
            $children=$val['children'];
            unset($val['children']);
            $menu=AdminMenu::create($val);
            $menu->children()->createMany($children);
        }
    }
}
