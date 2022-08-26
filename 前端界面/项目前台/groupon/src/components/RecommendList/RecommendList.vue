<!-- 商品列表 -->
<template>
  <div>
    <div class="recommendList_main">
      <Header></Header>
      <!-- 手机 -->
      <div class="search_fixed hidden-sm-and-up">
        <el-row class="titleBox">
          <el-col :xs='{span: 2}'>
            <i class="fa fa-chevron-left" aria-hidden="true" v-on:click="back()"></i>
          </el-col>
          <el-col :xs='{span: 20}'>
            <p>更多推荐</p>
          </el-col>
        </el-row>
      </div>
      <div class="RemendListBox" v-show="remendShow">
        <el-row>
          <el-col :xs='{ span: 24}' :sm="{ span: '4-8' }" class="RemendEveryOne" v-for="remend in remendBox" :key='remend.id'>
            <router-link :to="'/product/detail?productId='+remend.id">
              <el-row class="remendImg">
                  <img :src="remend.picture_url" alt="">
              </el-row>
              <el-row class="remendTxt">
                <el-row>
                  <p class="remendName">{{ remend.title }}</p>
                </el-row>
                <el-row class="remendMsg">
                  <el-col :xs='{span: 24}' :sm='{span: 10}'>
                    <p class="remendMsg1">评价&nbsp;{{ remend.commentnum }}</p>
                  </el-col>
                  <el-col :xs='{span: 24}' :sm='{span: 10}'>
                    <p class="remendMsg2">销量&nbsp;{{ remend.sales }}</p>
                  </el-col>
                </el-row>
                <el-row class="remendPrBu">
                  <el-col :xs='{span: 13}'>
                    <p class="remendPrice">&yen;{{ remend.price }}</p>
                  </el-col>
                  <el-col :xs='{span: 11}' class="hidden-sm-and-up">
                    <a href="#" onclick="return false">
                      <button type="button" v-on:click="shoppingCart(remend.id)" class="shoppingCart">加入购物车</button>
                    </a>
                  </el-col>
                </el-row>
              </el-row>
            </router-link>
            <div class="clear"></div>
          </el-col>
        </el-row>
      </div>
      <div class="promptTxt" v-show="moreRemendShow">
        暂无更多推荐商品！
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
      remendShow:true,
      moreRemendShow:false,
      remendBox:[],
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
      this.axios.get('https://grouponapi.top/api/basic/product/list?recommend=1&page='+ page)
      .then(res => {
        for(var i=0; i<res.data.data.length; i++){
          that.remendBox.push(res.data.data[i]);
        }
        if(page==1){
          that.total_page=res.data.meta.pagination.total_pages;
        }
        if(that.remendBox.length == 0){
          this.remendShow=false;
          this.moreRemendShow=true;
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
  },
  created(){
    this.show(1);
  }
};
</script>
<style scoped>
/* @import url(); 引入css类 */
@import url('../../assets/css/recommendlist.css');
</style>