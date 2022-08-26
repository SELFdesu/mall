<!--  -->
<template>
  <div>
    <div class="productdetails_main">
      <Header></Header>
      <div class="pdtdetail_fixed hidden-sm-and-up" v-on:click="back()">
        <el-row class="pdtdetail_titleBox">
          <el-col>
              <i class="fa fa-chevron-left" aria-hidden="true"></i>
          </el-col>
        </el-row>
      </div>

      <div class="pdtdetailOneBox">
        <div>
          <el-row class="pdtdetail_Row1Box">
            <el-col :sm='{ span: 10}' class="pdtdetailImg">
              <img :src="productdetailBox.picture_url" alt="">
            </el-col>
            <el-col :sm='{ span: 14}' class="productdetai_info">
              <el-row class="productdetai_name">
                <el-col>
                  <p>{{ productdetailBox.title }}</p>
                </el-col>
              </el-row>
              <el-row class="productdetai_price">
                <el-col>
                  <p>&yen;{{ productdetailBox.price }}</p>
                </el-col>
              </el-row>
              <el-row class="sales_comment hidden-xs-only">
                <el-col>
                  <p>销量&nbsp;<span>{{ productdetailBox.sales }}</span></p>
                  <p>评价数量&nbsp;<span>{{ productdetailBox.commentnum }}</span></p>
                </el-col>
              </el-row>
              <el-row class="buy_num hidden-xs-only">
                <el-col>
                  <p>数量&nbsp;&nbsp;&nbsp;<el-input-number v-model="value" size="mini" :min="1" label="购买数量"></el-input-number></p>
                  <p>库存&nbsp;<span>{{ productdetailBox.stock }}</span>&nbsp;件</p>
                </el-col>
              </el-row>
              <el-row class="productdetai_btn">
                <el-col :xs="{ span: 3 }" class="home_i hidden-sm-and-up">
                  <router-link to="/">
                    <i class="fa fa-home" aria-hidden="true"></i>
                    <p>首页</p>
                  </router-link>
                </el-col>
                <el-col :xs="{ span: 4 }" class="cart_i hidden-sm-and-up">
                  <router-link to="/shopping/cart">
                    <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                    <p>购物车</p>
                  </router-link>
                </el-col>
                <el-col :xs="{ span: 17}" class="button">
                  <button type="button" v-on:click="addCart(productdetailBox.stock)">加入购物车</button>
                  <button type="button" v-on:click="addBuy(productdetailBox.stock)">立即购买</button>
                </el-col>
              </el-row>
            </el-col>
          </el-row>
          <el-row v-if="XR" class="pdtdetail_Row2Box">
            <el-col>
              <el-tabs type="card" v-model="activeName" @tab-click="handleClick" class="el_tabs">
                <el-tab-pane label="商品详情" name="first">
                  <el-row class="small_info_row">
                    <el-col>
                      <el-row class="small_info">规格信息</el-row>
                      <el-row class="small_info_info">
                        <el-col :xs='{ span:6}' :sm='{ span:4}'><p>品牌</p></el-col>
                        <el-col :xs='{ span:18}' :sm='{ span:8}'><p>{{ productdetailBox.attribute.brand }}</p></el-col>
                        <el-col :xs='{ span:6}' :sm='{ span:4}'><p>产地</p></el-col>
                        <el-col :xs='{ span:18}' :sm='{ span:8}'><p>{{ productdetailBox.attribute.place }}</p></el-col>
                        <el-col :xs='{ span:6}' :sm='{ span:4}'><p>规格</p></el-col>
                        <el-col :xs='{ span:18}' :sm='{ span:8}'><p>{{ productdetailBox.attribute.size }}</p></el-col>
                        <el-col :xs='{ span:6}' :sm='{ span:4}'><p>生产日期</p></el-col>
                        <el-col :xs='{ span:18}' :sm='{ span:8}'><p>{{ productdetailBox.attribute.production_date }}</p></el-col>
                        <el-col :xs='{ span:6}' :sm='{ span:4}'><p>保质期</p></el-col>
                        <el-col :xs='{ span:18}' :sm='{ span:8}'><p>{{ productdetailBox.attribute.shelf_life }}</p></el-col>
                        <el-col :xs='{ span:6}' :sm='{ span:4}'><p>储存方式</p></el-col>
                        <el-col :xs='{ span:18}' :sm='{ span:8}'><p>{{ productdetailBox.attribute.storage_method }}</p></el-col>
                      </el-row>
                    </el-col>
                  </el-row>
                  <el-row class="smallComment_rowBox">
                    <el-col>
                      <el-row class="smallComment">商品详情</el-row>
                      <el-row class="commentMsg">
                        <p>{{ productdetailBox.description }}</p>
                      </el-row>
                      <el-row class="comment_Img">
                        <div v-for="detail in productdetailBox.pics_url" :key="detail">
                          <img :src="detail" alt="">
                        </div>
                      </el-row>
                    </el-col>
                  </el-row>
                </el-tab-pane>
                <el-tab-pane :label="'用户评价 '+productdetailBox.commentnum" name="second">
                  <el-row>
                    <el-col>
                      <el-row class="commentBox" v-for="comment in detailCommentBox" :key="comment.id">
                        <el-col :sm="{ span: 7 }" :lg="{ span: 5 }" class="comment_user">
                          <el-row>
                            <el-col :xs='{ span:3}' :sm="{ span: 4}" :lg="{ span: 5 }">
                              <img :src="comment.user.data.avatar_url" alt="">
                            </el-col>
                            <el-col :xs='{ span:20, offset:1}' :sm="{ span: 19, offset:1 }" :lg="{ span: 17, offset:2 }">
                              <el-row>
                                <p>{{ comment.user.data.username }}</p>
                              </el-row>
                              <el-row class="comment_star">
                                <el-rate :value="comment.grade" disabled></el-rate>
                              </el-row>
                            </el-col>
                          </el-row>
                        </el-col>
                        <el-col class="comment_content" :sm="{ span: 12 }"  :lg="{ span: 14 }">
                          <p>{{ comment.content }}</p>
                        </el-col>
                        <el-col class="comment_created_at" :sm="{ span: 5 }" :lg="{ span: 5 }">
                          <p>{{ comment.created_at }}</p>
                        </el-col>
                      </el-row>
                    </el-col>
                  </el-row>
                </el-tab-pane>
              </el-tabs>
            </el-col>
          </el-row>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Header from '../conn/header/Header';
export default {
  name: 'Productdetails',
  components: {
    Header
  },
  data() {
    return {
      XR:false,
      activeName: 'first',
      value:1,
      productdetailBox:'',
      detailCommentBox:'',
      productId:'',
      page:1,
      total_page:''
    };
  },
  mounted() {
    document.querySelector('body').setAttribute('style', 'background-color:#e7f0e6');

    let that=this;
    this.productId=this.$route.query.productId;
    this.axios.get('https://grouponapi.top/api/basic/product/info/'+this.productId)
     .then(res => {
       that.productdetailBox=res.data.data;
     }).catch(function(error){
       console.log(error);
     })
    this.show(1);

  },
  methods: {
    handleClick(tab, event){
      let that=this;
      if(tab.name == 'second'){
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
      }
    },
    show(page){
      let that=this;
     
      this.axios.get("https://grouponapi.top/api/basic/product/comment/"+this.productId+'?page='+ page+ '&include=user')
      .then(res => {
          
          that.detailCommentBox=res.data.data;
          
          that.total_page=res.data.meta.pagination.total_pages;
         
          that.XR=true;
      }).catch(function(error){
        console.log(error);
      });
    },
    addCart(stock){
      if(localStorage.getItem('token') != null){
        if(this.value > stock){
          this.$message({
            message: '选择数量已超出库存量！',
            type: 'error'
          })
        }else{
          let that=this;
          this.axios.post('https://grouponapi.top/api/shoppingcart',{
            product_id: this.productId,
            quantity: this.value
          }).then(res => {
            that.$message({
              message: '加入购物车成功！',
              type: 'success'
            })
          }).catch(function(error){
            that.$message({
              message: '加入购物车失败！',
              type: 'error'
            })
          })
        }
      }else{
        this.$router.push('/login');
      }
    },
    addBuy(stock){
      if(localStorage.getItem('token') != null){
        if(this.value > stock){
          this.$message({
            message: '选择数量已超出库存量！',
            type: 'error'
          })
        }else{
          let products = {data:[
            {product_id:this.productId, quantity:this.value}
          ]};
          this.$router.push({
            path:'/order/view',
            query: {
                products: JSON.stringify(products),
                type:'normal'
            }
          })
        }
      }else{
        this.$router.push('/login');
      }
    },
    back(){
      this.$router.back();
    }
  },
};
</script>
<style scoped>
/* @import url(); 引入css类 */
@import url('../../assets/css/Product_details.css');
</style>
