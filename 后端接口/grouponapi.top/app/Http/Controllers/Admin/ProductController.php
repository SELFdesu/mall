<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Transformers\ProductPaginateTransformer;
use App\Transformers\ProductTransformer;
use Illuminate\Http\Request;

class ProductController extends BaseController
{
    /**
     * 商品列表
     */
    public function index(Request $request)
    {
        $request->validate([
            'is_on'=>'In:0,1',
            'is_recommend'=>'In:0,1',
            'time_begin'=>'date',
            'time_end'=>'date',
        ]);
        
        $is_on=$request->input('is_on');
        $is_recommend=$request->input('is_recommend');
        $time_begin=$request->input('time_begin');
        $time_end=$request->input('time_end');

        if(isset($request->time_begin)||isset($request->time_end)){
            if(!isset($request->time_begin)||!isset($request->time_end)){
                return $this->response->errorBadRequest('请输入完整的时间区间');
            }
            if(strtotime($request->time_begin)>strtotime($request->time_end)){
                return $this->response->errorBadRequest('时间范围错误');
            }       
        }

        $product=Product::when(isset($request->is_on),function($query) use($is_on){
            $query->where('is_on',$is_on);
        })
        ->when(isset($request->is_recommend),function($query) use($is_recommend){
            $query->where('is_recommend',$is_recommend);
        })
        ->when(isset($request->time_begin),function($query) use($time_begin,$time_end){
            $query->whereBetween ('created_at',[$time_begin,$time_end]);
        })
        ->latest()
        ->paginate();
        return $this->response->paginator($product,new ProductPaginateTransformer());
    }

    /**
     * 添加新商品
     */
    public function store(ProductRequest $request)
    {
        $picsJson=json_decode($request->input('pics'));
        $attributeJson=json_decode($request->input('attribute'));
        //验证json数据
        $checkPics=check_pics_json($picsJson);
        $checkAttribute=check_attribute_json($attributeJson);
        if($checkPics!==0){
            return $this->response->error($checkPics,422);
        }
        if($checkAttribute!==0){
            return $this->response->error($checkAttribute,422);
        }

        $category_level=Category::find($request->input('category_id'))->level;
        if($category_level<2){
            return $this->response->errorBadRequest('只能添加商品至二级分类');
        }

        Product::create([
            'title'=>$request->input('title'),
            'picture'=>$request->input('picture'),
            'pics'=>$request->input('pics'),
            'category_id'=>$request->input('category_id'),
            'price'=>$request->input('price'),
            'stock'=>$request->input('stock'),
            'description'=>$request->input('description'),
            'attribute'=>$request->input('attribute'),
        ]);

        return $this->response->created();
    }

    /**
     * 商品详情
     */
    public function show(Product $product)
    {
        return $this->item($product,new ProductTransformer());
    }

    /**
     * 更新商品信息
     */
    public function update(ProductRequest $request,Product $product)
    {

        $picsJson=json_decode($request->input('pics'));
        $attributeJson=json_decode($request->input('attribute'));
        //验证json数据
        $checkPics=check_pics_json($picsJson);
        $checkAttribute=check_attribute_json($attributeJson);
        if($checkPics!==0){
            return $this->response->error($checkPics,422);
        }
        if($checkAttribute!==0){
            return $this->response->error($checkAttribute,422);
        }

        $category_level=Category::find($request->input('category_id'))->level;
        if($category_level<2){
            return $this->response->errorBadRequest('只能添加商品至二级分类');
        }
        
        $product->update([
            'title'=>$request->input('title'),
            'picture'=>$request->input('picture'),
            'pics'=>$request->input('pics'),
            'category_id'=>$request->input('category_id'),
            'price'=>$request->input('price'),
            'description'=>$request->input('description'),
            'attribute'=>$request->input('attribute'),
        ]);


        return $this->response->created();
    }

    /**
     * 是否上架
     */
    public function isOn(Product $product)
    {
        $product->is_on=!$product->is_on;
        $product->save();

        return $this->response->created();
    }

    /*
    * 是否推荐
    */
    public function isRecommend(Product $product){
        $product->is_recommend=!$product->is_recommend;
        $product->save();
        
        return $this->response->created();
    }
}
