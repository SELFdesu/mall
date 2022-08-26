<?php

namespace App\Http\Controllers\Api;

use App\Facades\Express\Facade\Express;

use App\Http\Controllers\BaseController;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\PackageDeliver;
use App\Models\Product;
use App\Transformers\OrderDetailTransformer;
use App\Transformers\OrderTransformer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class OrderController extends BaseController
{
    /**
     * 该用户的订单列表
     */
    public function index(Request $request)
    {
        $request->validate([
            'type'=>'required|In:all,unpaid,waitdeliver,waitpick,waitcomment,salesreturn'
        ]);
        $type=$request->input('type');
        $user=auth('api')->user();
        $orders=$user->order()
            ->when($type=='unpaid',function($query){
                $query->where('status',1);
            })
            ->when($type=='waitdeliver',function($query){
                $query->where('status',2);
            })
            ->when($type=='waitpick',function($query){
                $query->whereIn('status',[3,4]);
            })
            ->when($type=='waitcomment',function($query){
                $query->where('status',5);
            })
            ->when($type=='salesreturn',function($query){
                $query->where('status',0);
            })
            ->whereNotIn('status',[99])
            ->latest()
            ->paginate();
        return $this->response->paginator($orders,new OrderTransformer());

    }

    //处理订单中的商品
    protected function disposeOrderProduct($products_json,$type='normal'){
        $orderAmount=0;
        $products_arr=[];

        foreach($products_json->data as $val){
            $oneProduct=Product::find($val->product_id);
            // if($oneProduct->is_on==0){
            //     return $this->response->errorBadRequest("商品: $oneProduct->title 已下架！");
            // }
            // if($oneProduct->stock<$val->quantity){
            //     return $this->response->errorBadRequest("商品: $oneProduct->title 库存不足!");
            // }

            //将订单中的商品信息存入数组
            if(!isset($products_arr[$val->product_id])){
                $products_arr[$val->product_id]['product_id']=$val->product_id;

                if($type=='preview'){
                    $products_arr[$val->product_id]['title']=$oneProduct->title;
                    $products_arr[$val->product_id]['picture']=oss_url($oneProduct->picture,'product');
                }

                $products_arr[$val->product_id]['unit_price']=$oneProduct->price;
                $products_arr[$val->product_id]['quantity']=$val->quantity;
                $products_arr[$val->product_id]['total_amount']=round($val->quantity*$oneProduct->price,2);
            }else{
                $products_arr[$val->product_id]['quantity']+=$val->quantity;
                $products_arr[$val->product_id]['total_amount']+=$val->quantity*$oneProduct->price;
            }

            //统计订单总金额
            $orderAmount+=$products_arr[$val->product_id]['total_amount'];
        }
        $disposeResult=[];
        foreach($products_arr as $val){
            array_push($disposeResult,$val);
        }
        return [$disposeResult,$orderAmount];
    }

    /**
     * 购物车判断
     */
    protected function disposeCartProduct($products_arr,$shoppingCart){
        //判断提交的商品信息中是否存在购物车内没有的商品
        foreach($products_arr as $val){
            $flag=0;
            foreach($shoppingCart->get() as $innerVal){
                if($val['product_id']==$innerVal->product_id){

                    // if($val['quantity']!=$innerVal->quantity){
                    //     return $this->response->errorBadRequest('提交商品数量错误！');
                    // }
                    $flag=1;
                    break;
                }
            } 
            if(!$flag){
                return $this->response->errorBadRequest('提交商品信息错误！');
            } 
        }
    }

    /**
     * 订单预览
     */
    public function orderPreview(Request $request){
        $request->validate([
            'products'=>'required|json',//商品json
            'type'=>'required|In:normal,cart'
        ]);
        $products_json=json_decode($request->input('products'));
        //处理传来的json字符串
        $checkResult=check_orderProduct_json($products_json);
        if($checkResult!==1){
            return $this->response->errorBadRequest($checkResult);
        }

        //处理订单中的商品
        $disposeOrderResult=$this->disposeOrderProduct($products_json,'preview');
        $products_arr=$disposeOrderResult[0];
        $orderAmount=round($disposeOrderResult[1],2);

        $user=auth('api')->user();
        //处理购物车发起的提交订单请求
        if($request->input('type')=='cart'){
            $shoppingCart=$user->shoppingCart();
            $this->disposeCartProduct($products_arr,$shoppingCart);
        }
        return collect(['products'=>$products_arr,'orderAmount'=>$orderAmount]);

    }

    /**
     * 提交订单
     */
    public function store(Request $request)
    {
        $request->validate([
            'package_deliver_id'=>'required|exists:package_delivers,id',//自提点id
            'products'=>'required|json',//商品json
            'type'=>'required|In:normal,cart'
        ]);
        
        $checkPackageDeliver=PackageDeliver::find($request->input('package_deliver_id'));
        if($checkPackageDeliver->status==0){
            return $this->response->errorBadRequest('该自提点已停止使用！');
        }

        //处理传来的json字符串
        $products_json=json_decode($request->input('products'));

        $checkResult=check_orderProduct_json($products_json);
        if($checkResult!==1){
            return $this->response->errorBadRequest($checkResult);
        }

        //处理订单中的商品
        $disposeOrderResult=$this->disposeOrderProduct($products_json);
        $products_arr=$disposeOrderResult[0];
        $orderAmount=$disposeOrderResult[1];
        
        $user=auth('api')->user();
        //处理购物车发起的提交订单请求
        if($request->input('type')=='cart'){
            $shoppingCart=$user->shoppingCart();
            $this->disposeCartProduct($products_arr,$shoppingCart);
        }


        DB::beginTransaction();
        try{

            //处理购物车发起的提交订单请求
            if($request->input('type')=='cart'){
                foreach($products_arr as $val){
                    foreach($shoppingCart->get() as $innerVal){
                        if($val['product_id']==$innerVal->product_id){
                            $innerVal->delete();
                        }
                    }  
                }
            }
            
            //创建订单和订单详情
            $order=$user->order()->create([
                'package_deliver_id'=>$request->input('package_deliver_id'),
                'status'=>1,
                'amount'=>$orderAmount,
            ]);
            $order->orderDetail()->createMany($products_arr);
            
            Redis::setex('ORDER_CONFIRM:'.$order->id,900,$order->id);
            
            //减少商品的库存量
            foreach($products_arr as $val){
                Product::where('id',$val['product_id'])->decrement('stock',$val['quantity']);
            }

            DB::commit();

        } catch (Exception $e) {

            DB::rollback();
            return $this->response->errorInternal('数据库操作失败！请联系管理员。');
        }

        return $this->response->array(['id'=>$order->id])->setStatusCode(201);
    }

    /**
     * 订单详情
     * 
     */
    public function show(Order $userorder)
    {
        $user=auth('api')->user();
        if($userorder->user_id!=$user->id){
            return $this->response->errorBadRequest('该用户无权访问该订单信息！');
        }
        if($userorder->status==99){
            return $this->response->errorBadRequest('该订单已过期！');
        }
        $orderDetail=$userorder->orderDetail;
        return $this->response->collection($orderDetail,new OrderDetailTransformer());
    }

    /**
     * 订单搜索(订单号、商品名)
     */
    public function serch(Request $request){
        $request->validate([
            'type'=>'required|In:id,product_name',
            'serch_info'=>'required'
        ]);

        $user=auth('api')->user();

        $type=$request->input('type');
        $serch_info=$request->input('serch_info');

        $order=$user->order()
        ->whereNotIn('status',[99])
        ->when($type=='id',function($query)use($serch_info){
            $query->where('id',$serch_info);
        });


        if($type=='product_name'){
            $orderArr=[];
            $orderId=[];

            //查出带有关键字的商品id
            $serchProduct=Product::select('id')->where('title','like',"%$serch_info%")->get();

            // 将用户所有订单id存入数组
            foreach($order->get() as $val){
                array_push($orderArr,$val->id);
            }
            // 通过用户订单id查出订单的详情
            $orderDetail=OrderDetail::select('product_id','order_id')->whereIn('order_id',$orderArr)->get();

            for($i=0;$i<count($orderDetail);$i++){
                for($j=0;$j<count($serchProduct);$j++){
                    if($orderDetail[$i]->product_id==$serchProduct[$j]->id){
                        array_push($orderId,$orderDetail[$i]->order_id);
                    }
                }
            }
            $serchOrder=$order->whereIn('id',$orderId)->latest()->paginate();
            return $this->response->paginator($serchOrder,new OrderTransformer());
            
        }

        return $this->response->paginator($order->latest()->paginate(),new OrderTransformer());
    }

    /**
     * 订单签收
     */
    public function signfor(Order $order){
        $user=auth('api')->user();
        if($order->user_id!=$user->id){
            return $this->response->errorBadRequest('该用户无权签收该订单！');
        }
        if($order->status!=4){
            return $this->response->errorBadRequest('订单状态异常！');
        }
        $order->status=5;
        $order->signfor_time=date('Y-m-d H:i:s',time());
        $order->save();
        return $this->response->created();
    }

    /**
     * 物流查询
     * 
     */
    public function expressSerch(Order $order)
    {
        $user=auth('api')->user();
        if($order->user_id!=$user->id){
            return $this->response->errorBadRequest('该用户无权查看该订单物流信息！');
        }
        
        if(!in_array($order->status,[3,4])){
            return $this->response->errorBadRequest('订单状态异常');
        }
        $result=Express::traces($order->express_type,$order->express_no);

        if(isset($result->Success)&&$result->Sucess==false){
            return $this->response->errorBadRequest($result->ResponseData);
        }
        return $this->response->array($result);
    }


}
