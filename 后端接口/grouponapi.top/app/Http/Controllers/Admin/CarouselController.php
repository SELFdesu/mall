<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Models\Carousel;
use App\Transformers\CarouselTransformer;
use Illuminate\Http\Request;

class CarouselController extends BaseController
{
    /**
     * 轮播图列表
     *
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

        $carousel=Carousel::when(isset($request->status),function($query)use($status){
            $query->where('status',$status);
        })
        ->when(isset($request->time_begin),function($query) use($time_begin,$time_end){
            $query->whereBetween ('created_at',[$time_begin,$time_end]);
        })
        ->latest()
        ->paginate(15);
        return $this->response->paginator($carousel,new CarouselTransformer());


    }

    /**
     * 新增轮播图
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'url'=>'required',
            'img'=>'required',
        ]);
        Carousel::create([
            'name'=>$request->input('name'),
            'url'=>$request->input('url'),
            'img'=>$request->input('img'),
        ]);
        return $this->response->created();
    }

    /**
     * 轮播图详情
     */
    public function show(Carousel $carousel)
    {
        return $this->response->item($carousel,new CarouselTransformer());
    }

    /**
     * 更新轮播图信息
     */
    public function update(Request $request, Carousel $carousel)
    {
        $request->validate([
            'name'=>'required',
            'url'=>'required',
            'img'=>'required',
        ]);
        $carousel->update([
            'name'=>$request->input('name'),
            'url'=>$request->input('url'),
            'img'=>$request->input('img'),
        ]);
        return $this->response->created();
    }

    /**
     * 是否启用轮播图
     */
    public function status(Carousel $carousel){
        $carousel->status=!$carousel->status;
        $carousel->save();
        return $this->response->created();
    }
}
