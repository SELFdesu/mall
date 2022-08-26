<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends BaseController
{
      /**
     * 商品分类列表
     */
    public function index(Request $request)
    {
        $request->validate([
            'queryall'=>'In:0,1',
            'onlyOne'=>'In:0,1',
        ]);
        $queryall=$request->input('queryall',1);
        $onlyOne=$request->input('onlyOne',0);
        
        if($onlyOne==1) $queryall=0;

        if($queryall){
            return cache_category_all();
        }else{
            if($onlyOne){
                return cache_category_onlyOne();
            }
            return cache_category();
        }
        
    }

    /**
     * 添加商品分类
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required|max:16',
            'pid'=>'required'
        ]);
        
        $level=$request->pid==0?1:Category::find($request->input('pid'))->level+1;
        if($level>2){
            return $this->response->errorBadRequest('分类等级最大为2级');
        }
        Category::create([
            'name'=>$request->input('name'),
            'pic'=>$request->input('pic','default.jpg'),
            'pid'=>$request->input('pid',0),
            'level'=>$level,
        ]);

        return $this->response->created();
    }

    /**
     * 展示分类详情
     */
    public function show(Category $category)
    {
        $category['pic_url']=oss_url($category['pic'],'category');
        return $category;
    }

    /**
     * 更新分类
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name'=>'required|max:16',
        ]);
        $level=$request->pid==0?1:Category::find($request->input('pid'))->level+1;
        if($level>2){
            return $this->response->errorBadRequest('分类等级最大为2级');
        }
        $category->update([
            'name'=>$request->input('name'),
            'pic'=>$request->input('pic','default.jpg'),
            'pid'=>$request->input('pid',0),
            'level'=>$level,
        ]);

        return $this->response->created();
    }

    /**
     * 是否启用分类
     */
    public function status(Category $category){
        $category->status=!$category->status;
        $category->save();

        return $this->response->created();
    }
}
