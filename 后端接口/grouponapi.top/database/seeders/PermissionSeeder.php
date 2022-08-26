<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //添加权限
        //name建议使用路由名称方便后续判断
        $permissions=[
            /**
             * auth路由
             */
            ['name'=>'auth.me','cn_name'=>'获取登录用户信息'],
            ['name'=>'auth.changeavatar','cn_name'=>'更改头像'],

            ['name'=>'auth.bindemailsend','cn_name'=>'发送绑定邮箱验证码'],
            ['name'=>'auth.bindemail','cn_name'=>'绑定邮箱'],

            ['name'=>'auth.bindphonesend','cn_name'=>'发送绑定手机验证码'],
            ['name'=>'auth.bindphone','cn_name'=>'绑定手机'],

            ['name'=>'oss.token','cn_name'=>'获取oss的token'],

            ['name'=>'auth.logout','cn_name'=>'退出登录'],


            /**
             * api路由
             */
            //物流查询
            ['name'=>'express.serch','cn_name'=>'物流查询'],
            //用户自提点相关
            ['name'=>'userdelivery.list','cn_name'=>'用户保存自提点列表'],
            ['name'=>'userdelivery.add','cn_name'=>'用户保存自提点'],
            ['name'=>'userdelivery.del','cn_name'=>'用户删除保存自提点'],
            //团长相关
            ['name'=>'head.apply','cn_name'=>'团长申请'],
            ['name'=>'head.orderlist','cn_name'=>'自提点订单列表'],
            ['name'=>'head.cargolist','cn_name'=>'自提点货物详情'],
            ['name'=>'head.cargosignfor','cn_name'=>'签收自提点货物'],

            ['name'=>'head.applystatus','cn_name'=>'申请辞去、恢复身份状态'],
            ['name'=>'head.applyresign','cn_name'=>'申请辞去团长身份'],
            // ['name'=>'head.applyrecover','cn_name'=>'申请恢复团长身份'],

            ['name'=>'head.checkpaymentterm','cn_name'=>'是否填写收款方式'],
            ['name'=>'head.fillpaymentterm','cn_name'=>'填写收款方式'],

            ['name'=>'head.forecastearnings','cn_name'=>'团长本月预计收益'],
            ['name'=>'head.pastearnings','cn_name'=>'团长往月收益'],

            ['name'=>'head.infoMessage','cn_name'=>'团长信息'],
            ['name'=>'head.applymodifyheadinfo','cn_name'=>'申请修改团长信息'],

            //用户
            ['name'=>'userorder.index','cn_name'=>'用户订单列表'],
            ['name'=>'userorder.store','cn_name'=>'用户提交订单'],
            ['name'=>'userorder.show','cn_name'=>'用户订单详情'],
            ['name'=>'userorder.pagepreview','cn_name'=>'订单预览'],
            ['name'=>'userorder.serch','cn_name'=>'用户订单搜索'],
            ['name'=>'userorder.signfor','cn_name'=>'用户订单签收'],
            ['name'=>'comment.add','cn_name'=>'用户评价'],
            ['name'=>'shoppingcart.index','cn_name'=>'购物车列表'],
            ['name'=>'shoppingcart.store','cn_name'=>'添加购物车'],
            ['name'=>'shoppingcart.update','cn_name'=>'改变购物车商品数量'],
            ['name'=>'shoppingcart.destroy','cn_name'=>'删除购物车内商品'],
            ['name'=>'pay.alipay','cn_name'=>'支付宝支付'],
            //退货
            ['name'=>'salesreturn.userinfo','cn_name'=>'退货详情'],
            ['name'=>'salesreturn.apply','cn_name'=>'申请退货'],
            ['name'=>'salesreturn.send','cn_name'=>'寄出退货商品'],

            /**
             * admin路由
             */
            ['name'=>'admin.menu','cn_name'=>'管理菜单'],

            //用户管理
            ['name'=>'user.index','cn_name'=>'用户列表'],
            ['name'=>'user.show','cn_name'=>'用户详情'],
            ['name'=>'user.on','cn_name'=>'账号冻结'],

            //团长管理相关
            ['name'=>'head.applylist','cn_name'=>'团长申请列表'],
            ['name'=>'head.serchapply','cn_name'=>'搜索团长申请'],
            ['name'=>'head.applyinfo','cn_name'=>'团长申请信息'],
            ['name'=>'head.add','cn_name'=>'批准团长申请'],
            ['name'=>'head.refuse','cn_name'=>'退回团长申请'],
            ['name'=>'head.list','cn_name'=>'团长列表'],
            ['name'=>'head.serchhead','cn_name'=>'搜索团长'],
            ['name'=>'head.info','cn_name'=>'团长详情'],
            ['name'=>'head.takeback','cn_name'=>'移除团长'],
            ['name'=>'head.recover','cn_name'=>'恢复团长'],

            ['name'=>'head.applyresignlist','cn_name'=>'申请辞去团长列表'],
            ['name'=>'head.applyresignInfo','cn_name'=>'申请辞去团长详情'],
            ['name'=>'head.applyresignrefuse','cn_name'=>'拒绝申请辞去请求'],

            ['name'=>'head.modifyheadinfolist','cn_name'=>'团长申请信息修改列表'],
            ['name'=>'head.modifyheadinfoinfo','cn_name'=>'团长信息修改详情'],
            ['name'=>'head.modifydispose','cn_name'=>'是否同意团长修改'],

            ['name'=>'head.salarylist','cn_name'=>'团长收益列表'],
            ['name'=>'head.payrolllist','cn_name'=>'团长工资条'],
            ['name'=>'head.settlementsalary','cn_name'=>'结算团长收益'],
            ['name'=>'head.payrollchange','cn_name'=>'更改工资条状态为结算'],

            //商品管理相关
            ['name'=>'product.index','cn_name'=>'商品列表'],
            ['name'=>'product.store','cn_name'=>'添加商品'],
            ['name'=>'product.show','cn_name'=>'商品详情'],
            ['name'=>'product.update','cn_name'=>'更新商品'],
            ['name'=>'product.on','cn_name'=>'是否上架商品'],
            ['name'=>'product.recommend','cn_name'=>'是否推荐商品'],

            //订单管理相关
            ['name'=>'order.index','cn_name'=>'管理订单列表'],
            ['name'=>'order.show','cn_name'=>'管理订单详情'],
            ['name'=>'admin.orderserch','cn_name'=>'管理搜索订单'],

            //自提点（发货）管理
            ['name'=>'admin.deliverylist','cn_name'=>'自提点列表'],
            ['name'=>'admin.deliveryinfo','cn_name'=>'自提点详情'],
            ['name'=>'admin.ordersend','cn_name'=>'发货至自提点'],

            //评论管理
            ['name'=>'comment.index','cn_name'=>'管理评论列表'],
            ['name'=>'comment.show','cn_name'=>'管理评论详情'],
            ['name'=>'comment.status','cn_name'=>'是否屏蔽评论'],

            //商品分类管理
            ['name'=>'category.index','cn_name'=>'商品分类列表'],
            ['name'=>'category.store','cn_name'=>'新增商品分类'],
            ['name'=>'category.show','cn_name'=>'商品分类详情'],
            ['name'=>'category.update','cn_name'=>'更新商品分类'],
            ['name'=>'category.status','cn_name'=>'是否启用商品分类'],

            //轮播图管理
            ['name'=>'carousel.index','cn_name'=>'轮播图列表'],
            ['name'=>'carousel.store','cn_name'=>'新增轮播图'],
            ['name'=>'carousel.show','cn_name'=>'轮播图详情'],
            ['name'=>'carousel.update','cn_name'=>'更新轮播图'],
            ['name'=>'carousel.status','cn_name'=>'是否启用轮播图'],

            //退货管理
            ['name'=>'salesreturn.list','cn_name'=>'申请退货列表'],
            ['name'=>'salesreturn.info','cn_name'=>'申请退货详情'],
            ['name'=>'salesreturn.status','cn_name'=>'修改申请退货状态'],
        ];
        foreach($permissions as $p){
            Permission::create($p);
        }
        

        //添加角色
        // $role=
        $admin=Role::create(['name'=>'admin','cn_name'=>'网站管理员']);
        $head=Role::create(['name'=>'head','cn_name'=>'团长']);
        $user=Role::create(['name'=>'user','cn_name'=>'用户']);

        //为角色添加权限

        //管理员权限
        $admin->givePermissionTo(
            //auth路由
            'auth.me',
            'auth.changeavatar',
            'oss.token',
            'auth.logout',

            //管理菜单
            'admin.menu',

            //用户管理
            'user.index',
            'user.show',
            'user.on',

            //团长管理
            'head.applylist',
            'head.serchapply',
            'head.applyinfo',
            'head.add',
            'head.refuse',
            'head.list',
            'head.serchhead',
            'head.info',
            'head.takeback',
            'head.recover',
            'head.applyresignlist',
            'head.applyresignInfo',
            'head.applyresignrefuse',
            'head.modifyheadinfolist',
            'head.modifyheadinfoinfo',
            'head.modifydispose',
            'head.payrolllist',
            'head.salarylist',
            'head.settlementsalary',
            'head.payrollchange',

            //商品管理
            'product.index',
            'product.store',
            'product.show',
            'product.update',
            'product.on',
            'product.recommend',

            //订单管理
            'order.index',
            'order.show',
            'admin.orderserch',

            //自提点（发货）管理
            'admin.deliverylist',
            'admin.deliveryinfo',
            'admin.ordersend',

            //评论管理
            'comment.index',
            'comment.show',
            'comment.status',

            //商品分类管理
            'category.index',
            'category.store',
            'category.show',
            'category.update',
            'category.status',

            //轮播图管理
            'carousel.index',
            'carousel.store',
            'carousel.show',
            'carousel.update',
            'carousel.status',

            //退货管理
            'salesreturn.list',
            'salesreturn.info',
            'salesreturn.status'

        );

       //团长权限
        $head->givePermissionTo(
            'head.orderlist',
            'head.cargolist',
            'head.cargosignfor',
            'head.applystatus',
            'head.applyresign',
            // 'head.applyrecover',
            'head.forecastearnings',
            'head.pastearnings',
            'head.checkpaymentterm',
            'head.fillpaymentterm',
            'head.infoMessage',
            'head.applymodifyheadinfo'
        );

       //普通用户权限
        $user->givePermissionTo(
            //auth路由
            'auth.me',
            'auth.changeavatar',
            'auth.bindemailsend',
            'auth.bindemail',
            'oss.token',
            'auth.logout',
            'auth.bindphonesend',
            'auth.bindphone',

            //物流查询
            'express.serch',

            //用户自提点相关
            'userdelivery.list',
            'userdelivery.add',
            'userdelivery.del',

            //团长相关
            'head.apply',

            //用户订单相关
            'userorder.index',
            'userorder.store',
            'userorder.show',
            'userorder.pagepreview',
            'userorder.serch',
            'userorder.signfor',

            //用户评价
            'comment.add',

            //购物车
            'shoppingcart.index',
            'shoppingcart.store',
            'shoppingcart.update',
            'shoppingcart.destroy',

            //订单支付
            'pay.alipay',

            //退货
            'salesreturn.userinfo',
            'salesreturn.apply',
            'salesreturn.send',

        );

    }
}
