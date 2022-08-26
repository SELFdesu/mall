<!--  -->
<template>
  <div>
      <div class="classofy_main">
        <Header></Header>
        <MobileMenu selectLi="2"></MobileMenu>
        <div class="search_fixed hidden-sm-and-up">
          <el-row class="titleBox">
            <el-col :xs='{span: 2}'>
                <i class="fa fa-chevron-left" aria-hidden="true" v-on:click="back()"></i>
            </el-col>
            <el-col :xs='{span: 20}'>
                <p>商品分类</p>
            </el-col>
          </el-row>
        </div>
        <el-tabs v-model="classifyName" @tab-click="handleClick" tab-position="top" class="marginTop">
            <el-tab-pane :label="classify.name" :name="classify.id+'_big'" v-for="classify in classifyBox" :key="classify.id">
                <el-tabs v-model="childrenName" @tab-click="handleClick" tab-position="left">
                    <el-tab-pane label="全部" :name="classify.id+'_all'">
                        <div class="ProductBox" v-show="productBoxShow" :style="'height:'+scrollHeight">
                            <div class="ProductBo" style="height:100%">
                                <el-row v-infinite-scroll="load" infinite-scroll-immediate='false'>
                                    <el-col :xs='{ span: 24}' :sm="{ span: '4-8' }" class="ProductEveryOne" v-for="product in productBox" :key='product.id'>
                                        <router-link :to="'/product/detail?productId='+product.id">
                                        <el-row class="productImg">
                                            <img :src="product.picture_url" alt="">
                                        </el-row>
                                        <el-row class="productTxt">
                                            <el-row>
                                            <p class="productName">{{ product.title }}</p>
                                            </el-row>
                                            <el-row class="productMsg">
                                            <el-col :xs='{span: 24}' :sm='{span: 10}'>
                                                <p class="productMsg1">评价&nbsp;{{ product.commentnum }}</p>
                                            </el-col>
                                            <el-col :xs='{span: 24}' :sm='{span: 10}'>
                                                <p class="productMsg2">销量&nbsp;{{ product.sales }}</p>
                                            </el-col>
                                            </el-row>
                                            <el-row class="productPrBu">
                                            <el-col :xs='{span: 13}'>
                                                <p class="productPrice">&yen;{{ product.price }}</p>
                                            </el-col>
                                            <el-col :xs='{span: 11}' class="hidden-sm-and-up">
                                                <a href="#" onclick="return false">
                                                <button type="button" v-on:click="shoppingCart(product.id)" class="shoppingCart">加入购物车</button>
                                                </a>
                                            </el-col>
                                            </el-row>
                                        </el-row>
                                        </router-link>
                                        <div class="clear"></div>
                                    </el-col>
                                </el-row>
                            </div>
                        </div>
                        <div class="promptTxt" v-show="moreproductBoxShow">
                            暂无商品！
                        </div>
                    </el-tab-pane>
                    <el-tab-pane :label="children.name" :name="classify.id+'_'+children.name" v-for="children in classify.children" :key="children.id">
                        <div class="ProductBox"  v-show="productBoxShow" :style="'height:'+scrollHeight">
                            <div class="ProductBo" style="height:100%">
                                <el-row  v-infinite-scroll="load" infinite-scroll-immediate='false'>
                                    <el-col :xs='{ span: 24}' :sm="{ span: '4-8' }"  class="ProductEveryOne" v-for="product in productBox" :key='product.id'>
                                        <router-link :to="'/product/detail?productId='+product.id">
                                        <el-row class="productImg">
                                            <img :src="product.picture_url" alt="">
                                        </el-row>
                                        <el-row class="productTxt">
                                            <el-row>
                                            <p class="productName">{{ product.title }}</p>
                                            </el-row>
                                            <el-row class="productMsg">
                                            <el-col :xs='{span: 24}' :sm='{span: 10}'>
                                                <p>评价&nbsp;{{ product.commentnum }}</p>
                                            </el-col>
                                            <el-col :xs='{span: 24}' :sm='{span: 10}'>
                                                <p>销量&nbsp;{{ product.sales }}</p>
                                            </el-col>
                                            </el-row>
                                            <el-row class="productPrBu">
                                            <el-col :xs='{span: 13}'>
                                                <p class="productPrice">&yen;{{ product.price }}</p>
                                            </el-col>
                                            <el-col :xs='{span: 11}' class="hidden-sm-and-up">
                                                <a href="#" onclick="return false">
                                                <button type="button" v-on:click="shoppingCart(product.id)" class="shoppingCart">加入购物车</button>
                                                </a>
                                            </el-col>
                                            </el-row>
                                        </el-row>
                                        </router-link>
                                        <div class="clear"></div>
                                    </el-col>
                                </el-row>
                            </div>
                        </div>
                        <div class="promptTxt" v-show="moreproductBoxShow">
                            该分类暂无更多商品！
                        </div>
                    </el-tab-pane>
                </el-tabs>
            </el-tab-pane>
        </el-tabs>
      </div>
  </div>
</template>

<script>
import Header from '../conn/header/Header'
import MobileMenu from '../conn/mobileMenu/menu'
export default {
  name:'Classify',
  components: {
    Header,
    MobileMenu
  },
  data() {
    return {
        productBoxShow: true,
        moreproductBoxShow: false,
        classifyName: '',
        childrenName:'',
        classifyBox:[],
        childrenId:'',
        productBox:[],
        scrollHeight:'',
        page:1,
        total_page:''
    };
  },
  mounted() {
    document.querySelector('body').setAttribute('style', 'background-color:#e7f0e6');

    // 进入页面自动加载
    let that=this;
    this.axios.get('https://grouponapi.top/api/basic/category/list?onlyOne=0')
     .then(res => {
        that.classifyBox=res.data;
        if(that.$route.query.id===undefined){
            that.classifyName=res.data[0].id+'_big';
            that.childrenName=res.data[0].id+'_all';
            this.axios.get('https://grouponapi.top/api/basic/product/category?category='+this.classifyBox[0].id+ '&page='+ that.page)
            .then(res => {
                that.productBox=res.data.data;
                that.childrenId=this.classifyBox[0].id;
                that.total_page=res.data.meta.pagination.total_pages;
                if(that.productBox.length == 0){
                    that.productBoxShow=false;
                    that.moreproductBoxShow=true;
                }else{
                    that.productBoxShow=true;
                    that.moreproductBoxShow=false;
                }
            }).catch(function(error){
                console.log(error);
            })
        }else{
            that.classifyName=that.$route.query.id+'_big';
            that.childrenName=that.$route.query.id+'_all';
            this.axios.get('https://grouponapi.top/api/basic/product/category?category='+that.$route.query.id+ '&page='+ that.page)
            .then(res => {
                that.productBox=res.data.data;
                that.childrenId=that.$route.query.id;
                that.total_page=res.data.meta.pagination.total_pages;
                if(that.productBox.length == 0){
                    that.productBoxShow=false;
                    that.moreproductBoxShow=true;
                }else{
                    that.productBoxShow=true;
                    that.moreproductBoxShow=false;
                }
            }).catch(function(error){
                console.log(error);
            })
        }
        
        
     }).catch(function (error) { 
        console.log(error);
    })
    
    // 动态为ProductBox设置高度
    this.$nextTick(() => {
        var slideHeight = window.innerHeight-105;
        that.scrollHeight=slideHeight+'px';

        window.addEventListener('resize', () => {
            var slideHeight = window.innerHeight-105;
            that.scrollHeight=slideHeight+'px';
        })
    })
  },
  methods: {
      // 无限滚动
      load () {
          if(this.page < this.total_page){
              this.show();
          }
      },

      // tab标签页自带点击事件
      handleClick(tab, event){
        let that=this;
        let all=tab.name.split('_');
        if(all[1] == 'all'){
            this.childrenId=all[0];
        }else if(all[1] == 'big'){
            this.childrenId=all[0];
            this.childrenName=all[0]+'_all';
        }else{
            for(var i=0; i < this.classifyBox.length; i++){
              for(var j=0; j < this.classifyBox[i].children.length; j++){
                  var children_name= this.classifyBox[i].id+'_'+this.classifyBox[i].children[j].name;
                if(tab.name == children_name){
                    this.childrenId=this.classifyBox[i].children[j].id;
                    break;
                }
                
              }
            }
        }
        
        // 加载商品数据
        that.page=1;
        this.axios.get('https://grouponapi.top/api/basic/product/category?category='+this.childrenId+ '&page='+that.page)
         .then(res => {
            that.productBox=res.data.data;
            that.total_page=res.data.meta.pagination.total_pages;
            if(that.productBox.length == 0){
                this.productBoxShow=false;
                this.moreproductBoxShow=true;
            }else{
                this.productBoxShow=true;
                this.moreproductBoxShow=false;
            }
        }).catch(function(error){
            console.log(error);
        })
      },

      // 分页
      show(){
        let that=this;
        this.page++;
        this.axios.get('https://grouponapi.top/api/basic/product/category?category='+this.childrenId+ '&page=' +that.page)
        .then(res => {
            for(var i=0; i<res.data.data.length; i++){
                that.productBox.push(res.data.data[i]);
            }
        }).catch(function(error){
            console.log(error);
        });
      },

      shoppingCart(productId){
      var that=this;
      if(localStorage.getItem('token')){
        this.axios.post('https://grouponapi.top/api/shoppingcart',{
          product_id: productId,
          quantity: 1
        }).then(res => {
          that.$message({
            message: '添加成功！',
            type: 'success'
          })
        }).catch(function(error){
          that.$message({
            message: '添加失败！',
            type: 'error'
          })
        })
      }else{
          this.$router.push('/login');
      }
    },
    back(){
      this.$router.back();
    }
  }
};
</script>
<style scoped>
/* @import url(); 引入css类 */
@import url('../../assets/css/classify.css');
.ProductBo{
    overflow-y: scroll;
}

</style>
