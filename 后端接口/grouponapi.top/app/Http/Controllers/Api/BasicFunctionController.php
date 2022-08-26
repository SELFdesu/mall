<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\Carousel;
use App\Models\Category;
use App\Models\PackageDeliver;
use App\Models\Product;
use App\Transformers\BasicDeliveryListTransformer;
use App\Transformers\CarouselTransformer;
use App\Transformers\CommentTransformer;
use App\Transformers\ProductPaginateTransformer;
use App\Transformers\ProductTransformer;
use Illuminate\Http\Request;
use Cblink\Region\Region;

class BasicFunctionController extends BaseController
{
    /**
     * 轮播图列表
     */
    public function carouselList(){
        $carousel=Carousel::where('status',1)->get();
        return $this->response->collection($carousel,new CarouselTransformer());
    }
    /**
     * 商品列表（所有商品||推荐商品）
     */
    public function productList(Request $request){
        $request->validate([
            'recommend'=>'In:0,1'
        ]);

        $recommend=$request->input('recommend',0);
        if($recommend){
            $product=Product::where('is_recommend',1)->where('is_on',1)->latest()->paginate();
            return $this->response->paginator($product,new ProductPaginateTransformer);
        }

        $product=Product::where('is_on',1)->latest()->paginate();
        return $this->response->paginator($product,new ProductPaginateTransformer());
    }

    /**
     * 商品详情
     */
    public function productInfo(Product $product){
        if($product->is_on==0){
            return $this->response->errorNotFound();
        }
        return $this->response->item($product,new ProductTransformer());
    }

    /**
     * 商品的评价列表
     */
    public function productComment(Product $product){

        if($product->is_on==0){
            return $this->response->errorNotFound();
        }
        
        $comments=$product->comment()
            ->where('status',1)
            ->latest()
            ->paginate(15);
        return $this->response->paginator($comments,new CommentTransformer());
    }

    /**
     * 搜索商品
     */
    public function productSerch(Request $request){
        $request->validate([
            'title'=>'required|max:255'
        ]);
        $title=$request->input('title');
        $serchPaginate=Product::where('is_on',1)
            ->where('title','like',"%$title%")
            ->latest()
            ->paginate();
        return $this->response->paginator($serchPaginate,new ProductPaginateTransformer());
    }

    /**
     * 对应分类商品
     */
    public function productCategory(Request $request){
        $request->validate([
            'category'=>'required|exists:categories,id'
        ]);
        $categoryid=$request->input('category');
        $category=Category::find($categoryid);
        $level=$category->level;
        if($level==1){
            $categoryChildren=$category->children->map(function($category){
                return $category->id;
            });
            $product=Product::whereIn('category_id',$categoryChildren)->where('is_on',1)->latest()->paginate();
            return $this->response->paginator($product,new ProductPaginateTransformer());
        }

        $product=Product::where('category_id',$categoryid)->where('is_on',1)->latest()->paginate();
        return $this->response->paginator($product,new ProductPaginateTransformer());
    }

    /**
     * 分类列表
     */
    public function categoryList(Request $request){
        $request->validate([
            'onlyOne'=>'In:0,1'
        ]);
        $onlyOne=$request->input('onlyOne',0);
        if($onlyOne){
            return cache_category_onlyOne();
        }
        return cache_category();
    }

    //城市列表
    public function regionList(Request $request){
        $request->validate([
            'pid'=>'notIn:0'
        ]);
        $region = new Region();
        if(!isset($request->pid)){
            return $region->allProvinces();
        }
    
        return $region->nest($request->input('pid'));
    }

    //自提点列表
    public function deliveryList(Request $request){
        $request->validate([
            'address_id'=>'required|exists:areas,id'
        ]);
        $address_id=$request->input('address_id');
        $packageDeliver=PackageDeliver::where('status',1)->where('address_id',$address_id)->get();
        return $this->response->collection($packageDeliver,new BasicDeliveryListTransformer());

    }

}
