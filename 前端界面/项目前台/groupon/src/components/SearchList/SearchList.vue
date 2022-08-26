<!-- 商品列表 -->
<template>
  <div>
    <div class="searchList_main">
      <Header></Header>
      <div class="search_fixed">
        <el-row class="secrchBox" type="flex" justify='center'>
          <i class="fa fa-search"></i>
          <el-col :sm='{span: 24}' class="hidden-xs-only">
            <input type="text" name="" class="searchTxt" v-model.trim="searchVal" :placeholder="searchTxt" />
            <button type="button" class="searchBtn" v-on:click="PcBtn">搜索</button>
          </el-col>
          <!-- 手机 -->
          <el-col :xs='{span: 2}' class="hidden-sm-and-up">
            <i class="fa fa-chevron-left chevronLeft" aria-hidden="true" v-on:click="back()"></i>
          </el-col>
          <el-col :xs='{span: 22}' class="hidden-sm-and-up">
            <input type="text" name="" class="searchTxt" v-on:keyup.enter="submit" v-model.trim="searchVal" :placeholder="searchTxt" />
          </el-col>
        </el-row>
      </div>
      <div class="ProductListBox" v-show="productShow">
        <el-row>
          <el-col :xs='{ span: 24}' :sm="{ span: '4-8' }" class="ProductEveryOne" v-for="product in productBox" :key='product.id'>
            <router-link :to="'/product/detail?productId='+product.id">
              <el-row class="productImg">
                  <img :src="product.picture_url" alt="">
              </el-row>
              <el-row class="proTxt">
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
      <div class="promptTxt" v-show="promptTxtShow">
        没有搜索到该商品！
      </div>
    </div>
  </div>
</template>

<script>
import Header from '../conn/header/Header'
export default {
  name: 'ProductList',
  components: {
    Header
  },
  data() {
    return {
      searchVal:'',
      searchTxt:'',
      productShow:true,
      promptTxtShow:false,
      productBox:[],
      page:1,
      total_page:''
    };
  },
  mounted() {
    document.querySelector('body').setAttribute('style', 'background-color:#e7f0e6');

    let that=this;

    // 分页
    this.$nextTick(() => {
      let pg=false;
      document.title = this.$route.query.searchTitle+"_鲜橙搜索";
      window.addEventListener('scroll', function(){
        let aa=parseInt(document.body.scrollHeight-window.innerHeight);
        let bb=parseInt(document.documentElement.scrollTop);
        if(aa-bb>=0&&aa-bb<=20){
          pg=true;
        }
        if(pg && that.page < that.total_page){
          pg=false;
          that.page++;
          that.show(that.page);
        }
      });
    })
  },
  methods: {
    show(page){
      let that=this;
      var searchValue = this.$route.query.searchTitle;
      this.searchTxt=searchValue;
      this.axios.get('https://grouponapi.top/api/basic/product/serch?title='+ searchValue +'&page='+ page)
      .then(res => {
        for(var i=0; i<res.data.data.length; i++){
          that.productBox.push(res.data.data[i]);
        }
        if(page==1){
          that.total_page=res.data.meta.pagination.total_pages;
        }
        if(that.productBox.length == 0){
          this.productShow=false;
          this.promptTxtShow=true;
        }else{
          this.productShow=true;
          this.promptTxtShow=false;
        }
      }).catch(function(error){
        console.log(error);
      });
    },
    // Pc 搜素
    PcBtn(){
      if(this.searchVal != ''){
        this.$router.push('/productlist?searchTitle='+ this.searchVal);
        this.$router.go();
      }else{
        this.$message({
          message: '搜索框不能为空！',
          type: 'error'
        })
      }
    },

    // 手机 搜索
    submit(){
      if(this.searchVal != ''){
        this.$router.push('/productlist?searchTitle='+ this.searchVal);
        this.$router.go();
      }else{
        this.$message({
          message: '搜索框不能为空！',
          type: 'error'
        })
      }
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
  },
  created(){
    this.show(1);
  }
};
</script>
<style scoped>
/* @import url(); 引入css类 */
@import url('../../assets/css/searchlist.css');
</style>
