<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\Product;
use App\Models\ShoppingCart;
use App\Transformers\ShoppingCartTransformer;
use Illuminate\Http\Request;

class ShoppingCartController extends BaseController
{
    /**
     * 展示购物车内容
     */
    public function index()
    {
        $user=auth('api')->user();
        $myCart=$user->shoppingCart()->get();
        return $this->response->collection($myCart,new ShoppingCartTransformer());
    }

    /**
     * 添加商品至购物车
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id'=>'required|exists:products,id',
            'quantity'=>'required'
        ]);

        $product_id=$request->input('product_id');
        $quantity=$request->input('quantity');

        $product=Product::where('id',$product_id)->first();
        if($product->stock<$quantity){
            return $this->response->errorBadRequest('库存不足!');
        }

        $user=auth('api')->user();
        $myCart=$user->shoppingCart();
        if($myCart->exists()){
            foreach($myCart->get() as $val){
                if($val->product_id==$product_id){
                    $val->quantity=$val->quantity+$quantity;
                    $val->save();
                    return $this->response->created();
                }
            }

        }

        $myCart->create([
            'product_id'=>$product_id,
            'quantity'=>$quantity,
        ]);
        return $this->response->created();
    }

    /**
     * 更改购物车信息
     */
    public function update(Request $request, ShoppingCart $shoppingcart)
    {
        //
        $request->validate([
            'quantity'=>'required|integer'
        ]);
        if($request->input('quantity')<1){
            return $this->response->errorBadRequest('商品数必须大于1');
        }
        $product_num=$shoppingcart->product()->first()->stock;

        if($product_num<$request->input('quantity')){
            return $this->response->errorBadRequest('库存不足!'.'_'.$shoppingcart->quantity);
        }

        $shoppingcart->update([
            'quantity'=>$request->input('quantity')
        ]);

        return $this->response->created();
    }

    /**
     * 删除购物车内商品
     */
    public function delCart(Request $request)
    {
        $request->validate([
            'carts'=>'required|json'
        ]);
        $carts_json=json_decode($request->input('carts'));
        if(!isset($carts_json->data)){
            return '缺少data项';
        }
        $carts_arr=$carts_json->data;
        $user=auth('api')->user();
        $myCart=ShoppingCart::whereIn('id',$carts_arr);
        $myCart_get=$myCart->get();
        foreach($myCart_get as $val){
            if($val->user_id!=$user->id){
                return $this->response->errorBadRequest('信息错误！');
            }
        }
        $myCart->delete();
        return $this->response->noContent();
    }

}
