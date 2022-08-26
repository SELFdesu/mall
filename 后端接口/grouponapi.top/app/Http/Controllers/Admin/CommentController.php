<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Models\Comment;
use App\Transformers\CommentTransformer;
use Illuminate\Http\Request;

class CommentController extends BaseController
{
    /**
     * 评价列表
     */
    public function index(Request $request)
    {
        $request->validate([
            'status'=>'In:0,1',
            'time_begin'=>'date',
            'time_end'=>'date',
        ]);

        if(isset($request->time_begin)||isset($request->time_end)){

            if(!isset($request->time_begin)||!isset($request->time_end)){
                return $this->response->errorBadRequest('请输入完整的时间区间');
            }
            if(strtotime($request->time_begin)>strtotime($request->time_end)){
                return $this->response->errorBadRequest('时间范围错误');
            }       
        }
        $status=$request->input('status');
        $time_begin=$request->input('time_begin');
        $time_end=$request->input('time_end');


        $comments=Comment::when(isset($request->status),function($query) use($status){
            $query->where('status',$status);
        })
        ->when(isset($request->time_begin),function($query) use($time_begin,$time_end){
            $query->whereBetween ('created_at',[$time_begin,$time_end]);
        })
        ->latest()
        ->paginate(15);
        return $this->response->paginator($comments,new CommentTransformer());
    }



    /**
     * 评价详情
     */
    public function show(Comment $comment)
    {
        return $this->response->item($comment,new CommentTransformer());
    }

    /**
     * 禁用评价
     */
    public function is_on(Comment $comment)
    {
        $comment->status=!$comment->status;
        $comment->save();
        return $this->response->created();
    }



}
