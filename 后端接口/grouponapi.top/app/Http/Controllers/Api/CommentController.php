<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\Comment;
use App\Models\Order;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommentController extends BaseController
{
    public function toComment(Request $request,Order $order){

        $request->validate([
            'comments'=>'required|json'
        ]);
        $check_comments=check_comment_json(json_decode($request->input('comments')));
        if($check_comments!==0){
            return $this->response->errorBadRequest($check_comments);
        };

        $userId=auth('api')->user()->id;
        if($order->user_id!=$userId){
            return $this->response->errorBadRequest('用户身份错误!');
        }
        if($order->status!=5){
            return $this->response->errorBadRequest('订单状态错误！');
        }

        $comments=json_decode($request->input('comments'))->data;

        $orderDetail=$order->orderDetail()->select('product_id')->get();
        if(count($comments)!=count($orderDetail)){
            return $this->response->errorBadRequest('评价商品数与订单内商品数量不一致！');
        }
        $flag=0;
        foreach($comments as $val){
            foreach($orderDetail as $innerVal){
                if($innerVal->product_id==$val->product_id){
                    $flag=1;
                    break;
                }
            }
            if(!$flag){
                return $this->response->errorBadRequest('评价商品不在本订单中！');
            }
            $flag=0;
        }

        DB::beginTransaction();
        try{
            $order->status=6;
            $order->save();

            foreach($comments as $val){
                Comment::create([
                    'user_id'=>$userId,
                    'product_id'=>$val->product_id,
                    'content'=>$val->content,
                    'grade'=>$val->grade
                ]);

                Product::where('id',$val->product_id)->increment('commentnum');
            }
            DB::commit();

        } catch (Exception $e) {

            DB::rollback();
            return $this->response->errorInternal('数据库操作失败！请联系管理员。');
        }

        return $this->response->created();
    }

}
