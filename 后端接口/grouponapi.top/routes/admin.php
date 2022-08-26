<?php

use App\Http\Controllers\Admin\AdminMenuController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\HeadController;
use App\Http\Controllers\Admin\CarouselController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\DeliveryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\SalesReturnController;
use App\Http\Controllers\Admin\UserController;

$api = app('Dingo\Api\Routing\Router');
$params=[
    'middleware'=>[
        'api.throttle',
        'bindings',
    ],
    'limit'=>120,
    'expires'=>1,
];

$api->version('v1',$params, function ($api) {
    $api->group(['prefix'=>'admin'],function($api){

        //需要登录的路由
        $api->group(['middleware'=>['api.auth','check.permission']],function($api){

             /**
             * 获取后台管理菜单列表
             */
            $api->get('menu/list',[AdminMenuController::class,'index'])->name('admin.menu');

            /**
             * 用户管理
             */
            $api->resource('user',UserController::class,[
                'except'=>['store','update','destroy']
            ]);
            //是否启用用户
            $api->patch('user/{user}/on',[UserController::class,'isOn'])->name('user.on');

            /**
             * 团长相关
             */
            $api->group(['prefix'=>'head'],function($api){
                //获取提交成为团长的申请列表
                $api->get('applylist',[HeadController::class,'applyList'])->name('head.applylist');
                //团长申请搜索(用户id、申请编号)
                $api->get('serch/apply',[HeadController::class,'serchApply'])->name('head.serchapply');
                //获取申请人的申请信息
                $api->get('applyinfo/{applyHeadInfo}',[HeadController::class,'applyInfo'])->name('head.applyinfo');            
                //通过用户申请批准成为团长
                $api->patch('{applyHead}/addhead',[HeadController::class,'addHead'])->name('head.add');
                //拒绝用户申请团长请求
                $api->patch('{applyHead}/refusehead',[HeadController::class,'refuseHead'])->name('head.refuse');

                //获取团长列表
                $api->get('headlist',[HeadController::class,'headList'])->name('head.list');
                //团长搜索(团长id、用户id、姓名、电话)
                $api->get('serch/head',[HeadController::class,'serchHead'])->name('head.serchhead');
                //获取团长详情
                $api->get('headinfo/{headInfo}',[HeadController::class,'headInfo'])->name('head.info');
                //撤销团长身份
                $api->patch('{user}/takeback',[HeadController::class,'takeBackHead'])->name('head.takeback');
                 //恢复团长身份
                $api->patch('{user}/recover',[HeadController::class,'recoverHead'])->name('head.recover');

                //申请辞去团长列表
                $api->get('apply/resign/list',[HeadController::class,'applyResignList'])->name('head.applyresignlist');
                //申请辞去团长详情
                $api->get('apply/resign/{headResign}/info',[HeadController::class,'applyResignInfo'])->name('head.applyresignInfo');
                //拒绝辞去请求
                $api->patch('apply/resign/{headResign}/refuse',[HeadController::class,'applyResignRefuse'])->name('head.applyresignrefuse');


                //团长申请信息修改列表
                $api->get('modify/headinfo/list',[HeadController::class,'headModifyList'])->name('head.modifyheadinfolist');
                // 信息修改详情
                $api->get('modify/headinfo/info/{headApplyModify}',[HeadController::class,'headModifyInfo'])->name('head.modifyheadinfoinfo');
                //是否同意修改
                $api->patch('modify/headinfo/{headApplyModify}/dispose',[HeadController::class,'headModifyDispose'])->name('head.modifydispose');

                //团长收益列表
                $api->get('salary/list',[HeadController::class,'headSalary'])->name('head.salarylist');
                //工资条
                $api->get('payroll/{salary}/list',[HeadController::class,'headPayroll'])->name('head.payrolllist');
                //结算团长收益
                $api->patch('salary/{salary}/settlement',[HeadController::class,'changeHeadSalaryStatus'])->name('head.settlementsalary');
                //更改工资条状态为结算
                $api->patch('payroll/{payroll}/change',[HeadController::class,'changePayrollStatus'])->name('head.payrollchange');

            });

            /**
             * 商品相关
             */
            //获取商品列表、以及添加、更新商品、展示商品详情
            $api->resource('product',ProductController::class,[
                'except'=>['destroy']
            ]);
            //是否禁用
            $api->patch('product/{product}/on',[ProductController::class,'isOn'])->name('product.on');
            // 是否推荐
            $api->patch('product/{product}/recommend',[ProductController::class,'isRecommend'])->name('product.recommend');

            /**
             * 订单相关
             */
            $api->resource('order',OrderController::class,[
                'except'=>['store','update','destroy']
            ]);
            //订单搜索(订单号、快递单号、支付单号)
            $api->get('order/serch/number',[OrderController::class,'serch'])->name('admin.orderserch');

            /**
             * 自提点订单(每天19点前的订单)发货
             */
            $api->group(['prefix'=>'delivery'],function($api){
                $api->get('list',[DeliveryController::class,'deliveryList'])->name('admin.deliverylist');
                $api->get('info/{packageDeliver}',[DeliveryController::class,'deliveryInfo'])->name('admin.deliveryinfo');
                $api->patch('{packageDeliver}/order',[DeliveryController::class,'deliveryOrder'])->name('admin.ordersend');
            });

            /**
             * 评论相关
             */
            $api->resource('comment',CommentController::class,[
                'except'=>['store','update','destroy']
            ]);
            //是否封禁评论
            $api->patch('comment/status/{comment}',[CommentController::class,'is_on'])->name('comment.status');

            /**
             * 商品分类相关
             */
             //获取商品分类列表、以及添加、展示详情、更新分类
            $api->resource('category',CategoryController::class,[
                'except'=>['destroy']
            ]);
            //是否启用分类
            $api->patch('category/{category}/status',[CategoryController::class,'status'])->name('category.status');

            /**
             * 轮播图
             */
            $api->resource('carousel',CarouselController::class,[
                'except'=>['destroy']
            ]);
            $api->patch('carousel/{carousel}/status',[CarouselController::class,'status'])->name('carousel.status');


            /**
             * 退货相关
             */
            $api->get('sales/return',[SalesReturnController::class,'salesReturnList'])->name('salesreturn.list');
            $api->get('sales/return/{salesReturnApply}',[SalesReturnController::class,'salesReturnInfo'])->name('salesreturn.info');
            $api->patch('sales/return/{salesReturnApply}/status',[SalesReturnController::class,'status'])->name('salesreturn.status');


        });

    });
});