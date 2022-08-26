<template>
  <div>
    <div class="all">
      <!-- 手机端 -->
      <!-- 底部导航 start -->
      <MobileMenu selectLi="1"></MobileMenu>
      <!-- 底部导航 stop -->
      <!-- 顶部显示 开始 -->
      <div class="hidden-sm-and-up  xs_top_container">
        <div class="topTxt">
          <el-row>
            <el-col :span="7" :offset="1">
              <div class="name">鲜橙团购</div>
            </el-col>
            <router-link :to="to_pickup" style="text-decoration: none;">
              <el-col :span="13" :offset="1">
                <div class="ZTD">
                  <i class="fa fa-map-marker"></i>
                    自提点：{{ pickup }}
                </div>
              </el-col>
              <el-col :span="1">
                <i class="el-icon-arrow-right"></i>
              </el-col>
            </router-link>
          </el-row>
        </div>
        <!-- 顶部显示 结束 -->
        <!-- 搜索框 start -->
        <div class="xs_search">
          <el-row>
            <el-col>
              <i class="fa fa-search"></i>
              <div>
                <input v-on:keyup.enter="submit" v-model.trim="phoneVal" type="text" name="" class="xs_searchTxt" value="" />
              </div>
            </el-col>
          </el-row>
        </div>
      </div>
      <!-- 搜索框 stop -->
      

      <!-- PC端 and 手机 -->
      <!-- 顶部导航 start -->
      <Header selectLi="1"></Header>
      <!-- 顶部导航 stop -->
      <!-- 搜索框 start -->
      <div class="header hidden-xs-only">
        <el-row>
          <el-col :md="{ span: 6 }" :lg="{ offset: 1 }" class="hidden-sm-and-down">
            <div class="logo">
              <img src="../../assets/images/home/logo.png" alt="" />
            </div>
          </el-col>
          <el-col :md="{ span: 17, offset: 1 }" :lg="{ span: 15 }">
            <div class="search">
              <i class="fa fa-search"></i>
              <input type="text" name="" class="searchTxt" v-model.trim="searchVal" />
              <a href="#" onclick='return false'>
                  <button type="button" v-on:click="searchBtn()" class="searchBtn">搜索</button>
              </a>
            </div>
          </el-col>
        </el-row>
      </div>
      <!-- 搜索框 stop -->
      <div style="margin-top:10px" class="hidden-sm-and-up"></div>
      <div class="main">
        <!-- 轮播图区 start -->
        <div class="slideshow">
          <el-row>
            <el-col :xs="{ span: 24 }" :sm="{ span: 17 }">
              <el-carousel :height="carousel_height" style="border-radius: 5px;">
                <el-carousel-item class="slideImg" v-for="slideImg in slideImgBox" :key="slideImg.name">
                    <a :href="slideImg.url">
                      <img class="slideHeight" ref="slideHeight" :src="slideImg.img_url" alt="" />
                    </a>
                </el-carousel-item>
              </el-carousel>
            </el-col>
            <!-- 菜单 -->
            <el-col style="margin-top: 10px" class="hidden-sm-and-up"></el-col>
            <el-col :xs="{ span: 24 }" :sm="{ span: 7 }" class="menu_background">
              <el-row>
                <el-col class="menu" :xs="{ span: 8, offset: 0 }" :sm="{ span: 11, offset: 1 }" v-for="menu in menuBox.slice(0,5)" :key="menu.name">
                  <div>
                    <router-link :to="'/classify?id='+menu.id">
                      <img :src="menu.pic_url" alt=""/>
                      <p>{{menu.name}}</p>
                    </router-link>
                  </div>
                </el-col>
                <el-col class="menu" :xs="{ span: 8, offset: 0 }" :sm="{ span: 11, offset: 1 }">
                  <div>
                    <router-link to="/classify">
                      <img src="../../assets/images/home/menu_more.png" alt=""/>
                      <p>更多</p>
                    </router-link>
                  </div>
                </el-col>
              </el-row>
            </el-col>
          </el-row>
        </div>
        <!-- 轮播图区 stop -->
        <!-- 展示区 start -->
        <div class="show">
          <!-- 推荐区 start -->
          <div class="show1">
            <div class="show1Title">
              <div class="showTxtBox">
                <div class="smallBot hidden-xs-only"></div>
                <div class="bigBot hidden-xs-only"></div>
                <div class="show1Txt">鲜橙推荐</div>
                <div class="bigBot hidden-xs-only"></div>
                <div class="smallBot hidden-xs-only"></div>
                <div class="clear"></div>
              </div>
            </div>

            <div class="show1Content">
              <el-row style="white-space: nowrap;">
                <el-scrollbar style="height: 100%;">
                  <div class="show1ConInner" v-for="recommend in recommendBox" :key="recommend.id">
                    <router-link :to="'/product/detail?productId='+recommend.id">
                      <img :src="recommend.picture_url" alt="" />
                      <div class="parciText">
                        <p class="proName">
                          {{ recommend.title }}
                        </p>
                        <p class="message">
                          <el-row class="hidden-xs-only">
                            <el-col :sm="12" :md="10" :lg="8">
                                评价&nbsp;{{ recommend.commentnum }}
                            </el-col>
                            <el-col :sm="12" :md="10" :lg="8">
                                销量&nbsp;{{ recommend.sales }}
                            </el-col>
                          </el-row>
                        </p>
                        <p class="price">&yen;{{ recommend.price }}</p>
                      </div>
                    </router-link>
                  </div>
                  <div class="show1ConInner show1ConInner_more">
                    <router-link to="/recommend/list">
                      <div class="show1ConInnerPlaceholder">&nbsp;</div>
                      <div class="parciText">
                        <p class="proName">&nbsp;</p>
                        <p class="message">
                          <el-row class="hidden-xs-only">
                            <el-col :sm="12" :md="10" :lg="8"></el-col>
                            <el-col :sm="12" :md="10" :lg="8"></el-col>
                          </el-row>
                        </p>
                        <p class="price">&nbsp;</p>
                      </div>
                    </router-link>
                  </div>
                </el-scrollbar>
              </el-row>
            </div>
          </div>
          <!-- 推荐区 stop -->
          <!-- 热卖单品 start -->
          <div class="show1">
            <div class="show1Title">
              <div class="showTxtBox">
                <div class="smallBot hidden-xs-only"></div>
                <div class="bigBot hidden-xs-only"></div>
                <div class="show1Txt">热卖单品</div>
                <div class="bigBot hidden-xs-only"></div>
                <div class="smallBot hidden-xs-only"></div>
              </div>
            </div>

            <div class="show1Content">
              <el-row>
                <el-col :xs="{ span: 24 }" :sm="{ span: '4-8' }" class="show2ConInner" v-for="hotsale in hotsaleBox" :key="hotsale.id">
                  <router-link :to="'/product/detail?productId='+hotsale.id">
                    <img :src="hotsale.picture_url" alt="" />
                    <div class="parciText">
                      <p class="proName">
                        {{ hotsale.title }}
                      </p>
                      <p class="message">
                        <el-row>
                          <el-col :xs="24" :sm="12" :md="10" :lg="8">
                              评价&nbsp;{{ hotsale.commentnum }}
                          </el-col>
                          <el-col :xs="24" :sm="12" :md="10" :lg="8">
                              销量&nbsp;{{ hotsale.sales }}
                          </el-col>
                        </el-row>
                      </p>
                      <p class="price">&yen;{{ hotsale.price }}</p>
                      <a href="#" onclick='return false'>
                        <div v-on:click="phoneCart(hotsale.id)" class="hidden-sm-and-up show2ConInner_shoppingCart">
                          加入购物车
                        </div>
                      </a>
                    </div>
                  </router-link>
                </el-col>
              </el-row>
            </div>
          </div>
          <!-- 热卖单品 stop -->
        </div>
        <!-- 展示区 stop -->
      </div>
      <!-- PC端 结束 -->
    </div>
  </div>
</template>

<script>
import Header from '../conn/header/Header'
import MobileMenu from '../conn/mobileMenu/menu'

export default {
  name: "Home",
  components: {
      Header,
      MobileMenu
  },
  data() {
    return {
      to_pickup:'',
      pickup:'',
      phoneVal:'',
      searchVal:'',
      carousel_height:'',
      slideImgBox:[],
      menuBox:[],
      recommendBox:[],
      hotsaleBox:[],
      hotsale:'',
      page:1,
      total_page:'',
    };
  },
  mounted () {

      let that=this;
      //自提点
      if(localStorage.getItem('token') != null){
        this.to_pickup='/pickup/list';
      }else{
        this.to_pickup='/login';
      }
      if(localStorage.getItem('pickupName') != null){
          this.pickup=localStorage.getItem('pickupName');
        }else{
          this.pickup='请选择自提点...';
        }
      

      // 轮播图
      this.axios.get('https://grouponapi.top/api/basic/carousel/list')
      .then(res => {
        that.slideImgBox=res.data.data;

        // 动态获取高度
        this.$nextTick(() => {
          // 轮播图初始
          var slideHeight = this.$refs.slideHeight[0].offsetHeight;

          that.carousel_height=slideHeight+'px';

          window.addEventListener('resize', () => {
            // 轮播图窗口变化
          var slideHeight = this.$refs.slideHeight[0].offsetHeight;
          that.carousel_height=slideHeight+'px';
          })
        })
      }).catch(function(error){
        console.log(error);
      })
      
      // 菜单
      this.axios.get('https://grouponapi.top/api/basic/category/list',{
        onlyOne:1
      }).then(res => {
          that.menuBox=res.data;
      }).catch(function(error){
        console.log(error);
      })

      // 鲜橙推荐
      this.axios.get('https://grouponapi.top/api/basic/product/list?recommend=1')
      .then(res => {
        that.recommendBox=res.data.data;
      }).catch(function(error){
        console.log(error);
      })

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
    // 手机 搜索框
    submit(){
      if(this.phoneVal != ''){
        this.$router.push('/search/list?searchTitle='+ this.phoneVal);
      }else{
        this.$message({
          message: '搜索框不能为空！',
          type: 'error'
        })
      }
    },
    // phoneCart
    phoneCart(product_id){
      var that=this;
      if(localStorage.getItem('token')){
        this.axios.post('https://grouponapi.top/api/shoppingcart',{
          product_id: product_id,
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


    // PC 搜索框
    searchBtn(){
      if(this.searchVal != ''){
        this.$router.push('/search/list?searchTitle='+ this.searchVal);
      }else{
        this.$message({
          message: '搜索框不能为空！',
          type: 'error'
        })
      }
    },

    //分页
    show(page){
      let that=this;
      this.axios.get("https://grouponapi.top/api/basic/product/list?recommend=0&page="+ page)
      .then(res => {
        for(var i=0; i<res.data.data.length; i++){
          that.hotsaleBox.push(res.data.data[i]);
        }
        if(page==1){
          that.total_page=res.data.meta.pagination.total_pages;
        }
      }).catch(function(error){
        console.log(error);
      });
    }
  },
  created(){
    this.show(1);
  }
};


</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
@import url("../../assets/css/home.css");
</style>
