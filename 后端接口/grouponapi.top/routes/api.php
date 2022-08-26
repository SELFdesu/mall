<?php

use App\Http\Controllers\Api\BasicFunctionController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\DeliveryController;
use App\Http\Controllers\Api\HeadController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PayController;
use App\Http\Controllers\Api\SalesReturnController;
use App\Http\Controllers\Api\ShoppingCartController;

$api = app('Dingo\Api\Routing\Router');


$api->version('v1',function ($api) {
    //支付宝回调地址
    $api->any('pay/notify/alipay',[PayController::class,'notifyAlipay']);


    $params=[
        'middleware'=>[
            'api.throttle',
            'bindings',
        ],
        'limit'=>120,
        'expires'=>1,
    ];

    //节流路由
    $api->group($params,function($api){
        /**
         * 基础功能
         */

        $api->group(['prefix'=>'basic'],function($api){
            //获取轮播图
            $api->get('carousel/list',[BasicFunctionController::class,'carouselList']);

            //获取商品列表（所有商品||推荐商品）
            $api->get('product/list',[BasicFunctionController::class,'productList']);

            //返回商品详情
            $api->get('product/info/{product}',[BasicFunctionController::class,'productInfo']);

            //商品的评价列表
            $api->get('product/comment/{product}',[BasicFunctionController::class,'productComment']);

            //搜索商品
            $api->get('product/serch',[BasicFunctionController::class,'productSerch']);

            //返回对应分类商品
            $api->get('product/category',[BasicFunctionController::class,'productCategory']);

            //返回分类列表
            $api->get('category/list',[BasicFunctionController::class,'categoryList']);

            //返回城市列表
            $api->get('region/list',[BasicFunctionController::class,'regionList']);

            //自提点列表
            $api->get('delivery/list',[BasicFunctionController::class,'deliveryList']);

        });


        //需要登录的路由
        $api->group(['middleware'=>['api.auth','check.permission']],function($api){

            /**
             * 物流查询
             */
            $api->get('express/{order}/serch',[OrderController::class,'expressSerch'])->name('express.serch');


            /**
             * 自提点相关
             */
            $api->group(['prefix'=>'user/delivery'],function($api){
                $api->get('list',[DeliveryController::class,'UserDeliveryList'])->name('userdelivery.list');
                $api->post('add',[DeliveryController::class,'AddUserDelivery'])->name('userdelivery.add');
                $api->delete('{packageDeliver}/del',[DeliveryController::class,'DelUserDelivery'])->name('userdelivery.del');
            });
            /**
             * 团长相关
             */
            $api->group(['prefix'=>'head'],function($api){
                //申请成为团长
                $api->post('apply',[HeadController::class,'applyHead'])->name('head.apply');
                //用户未签收订单列表  
                $api->get('order/list',[HeadController::class,'orderList'])->name('head.orderlist');
                //货物信息列表
                $api->get('cargo/list',[HeadController::class,'cargoList'])->name('head.cargolist');
                //确认签收货物
                $api->patch('cargo/signfor',[HeadController::class,'cargoSignFor'])->name('head.cargosignfor');

                //申请辞职、恢复状态
                $api->get('apply/status',[HeadController::class,'applyStatus'])->name('head.applystatus');
                //申请辞去团长身份
                $api->post('resign',[HeadController::class,'applyResign'])->name('head.applyresign');
                //申请恢复团长身份
                //$api->post('recover',[HeadController::class,'applyRecover'])->name('head.applyrecover');

                //是否填写收款方式
                $api->get('check/payment/term',[HeadController::class,'check_payment_term'])->name('head.checkpaymentterm');
                //填写收款方式
                $api->post('fill/payment/term',[HeadController::class,'fill_payment_term'])->name('head.fillpaymentterm');
                //团长本月预估工资
                $api->get('forecast/earnings',[HeadController::class,'forecast_earnings'])->name('head.forecastearnings');
                //往月收益
                $api->get('past/earnings',[HeadController::class,'past_earnings'])->name('head.pastearnings');

                //团长信息
                $api->get('headinfo/message',[HeadController::class,'headInfoMessage'])->name('head.infoMessage');
                //申请修改团长信息
                $api->post('apply/modify/headinfo',[HeadController::class,'applyModifyHeadInfo'])->name('head.applymodifyheadinfo');
            });

            /**
             * 订单相关
             */
            $api->resource('userorder',OrderController::class,[
                'except'=>['update','destroy']
            ]);
            //订单预览
            $api->post('userorder/page/preview',[OrderController::class,'orderPreview'])->name('userorder.pagepreview');
            //订单搜索
            $api->get('userorder/record/serch',[OrderController::class,'serch'])->name('userorder.serch');
            //订单签收
            $api->post('userorder/{order}/signfor',[OrderController::class,'signfor'])->name('userorder.signfor');

            /**
             * 发表评论
             */

            $api->post('comment/add/{order}',[CommentController::class,'toComment'])->name('comment.add');

            /**
             * 购物车
             */
            $api->resource('shoppingcart',ShoppingCartController::class,[
                'except'=>['show','destroy']
            ]);
            $api->delete('shoppingcart/del',[ShoppingCartController::class,'delCart'])->name('shoppingcart.destroy');

            /**
             * 订单支付
             */
            // 支付宝支付
            $api->get('order/alipay/{order}',[PayController::class,'aliPay'])->name('pay.alipay');

            /**
             * 退货
             */
            $api->get('sales/return/{order}/info',[SalesReturnController::class,'salesReturnInfo'])->name('salesreturn.userinfo');
            $api->post('sales/return/{order}/apply',[SalesReturnController::class,'salesReturnApply'])->name('salesreturn.apply');
            $api->patch('sales/return/{order}/send',[SalesReturnController::class,'salesReturnSend'])->name('salesreturn.send');
            
            
        });
    });

});