<template>
  <div class="all">
    <el-container style="width:100% ;height: 100%; position: absolute; min-width: 960px;">

      <el-header>
        <el-row>
          <el-col :span="10">
            <img id="avatar" height="50px" :src="avatar_url" >
            <p id="username">
              欢迎您：{{user_name}}
            </p>
          </el-col>
          <el-col :offset="12" :span="2">
            <span @click="logout" id="logout">
              退出登录
            </span>
          </el-col>
        </el-row>
      </el-header>

      <el-container>

        <el-aside width="250px" id="aside_height">
          <el-scrollbar style="height: 100%">
            <el-menu active-text-color="#67C23A" :default-active="active_menu" mode="vertical" unique-opened >
              <el-menu-item class="admin_center" @click="menu_click('管理中心')" index="管理中心">管理中心</el-menu-item>
              <el-submenu v-for="item in menu_list" :index="item.id+''" :key="item.id">
                <template slot="title">{{item.name}}</template>
                <el-menu-item @click="menu_click(inner_item.name)" v-for="inner_item in item.children" :index="inner_item.id+''" :key="inner_item.id">{{inner_item.name}}</el-menu-item>
              </el-submenu>
            </el-menu>
          </el-scrollbar>
        </el-aside>

        <el-main>
          <!-- 管理中心 -->
          <AdminCenter v-if="now_menu=='管理中心'"></AdminCenter>

          <!-- 用户管理 -->
          <UserList v-if="now_menu=='用户列表'"></UserList>
          <HeadList v-else-if="now_menu=='团长列表'"></HeadList>
          <HeadEarnings v-else-if="now_menu=='团长收益列表'"></HeadEarnings>
          <HeadApply v-else-if="now_menu=='团长申请列表'"></HeadApply>
          <HeadResign v-else-if="now_menu=='团长申请辞职列表'"></HeadResign>
          <HeadModify v-else-if="now_menu=='团长修改信息请求列表'"></HeadModify>

          <!-- 商品管理 -->
          <ProductList v-else-if="now_menu=='商品列表'"></ProductList>
          <AddProduct v-else-if="now_menu=='添加商品'"></AddProduct>

          <!-- 订单管理 -->
          <OrderList v-else-if="now_menu=='订单列表'"></OrderList>

          <!-- 发货管理 -->
          <DeliveryList v-else-if="now_menu=='自提点列表'"></DeliveryList>

          <!-- 退货管理 -->
          <SalesReturnList v-else-if="now_menu=='申请退货列表'"></SalesReturnList>

          <!-- 评价管理 -->
          <CommentList v-else-if="now_menu=='评价列表'"></CommentList>

          <!-- 轮播图管理 -->
          <CarouselList v-else-if="now_menu=='轮播图列表'"></CarouselList>
          <AddCarousel v-else-if="now_menu=='添加轮播图'"></AddCarousel>

          <!-- 分类管理 -->
          <CategoryList v-else-if="now_menu=='分类列表'"></CategoryList>
          <AddCategory v-else-if="now_menu=='添加分类'"></AddCategory>


        </el-main>

      </el-container>

    </el-container>
  </div>
</template>

<script>
  //管理中心
  import AdminCenter from '../ChildrenPage/AdminCenter.vue'
  //用户管理
  import UserList from '../ChildrenPage/UserAdmin/user_list.vue'
  import HeadList from '../ChildrenPage/UserAdmin/head_list.vue'
  import HeadEarnings from '../ChildrenPage/UserAdmin/head_earnings.vue'
  import HeadApply from '../ChildrenPage/UserAdmin/head_apply.vue'
  import HeadResign from '../ChildrenPage/UserAdmin/head_resign.vue'
  import HeadModify from '../ChildrenPage/UserAdmin/head_modify.vue'

  //商品管理
  import ProductList from '../ChildrenPage/ProductAdmin/product_list.vue'
  import AddProduct from '../ChildrenPage/ProductAdmin/add_product.vue'

  //订单管理
  import OrderList from '../ChildrenPage/OrderAdmin/order_list.vue'

  //发货管理
  import DeliveryList from '../ChildrenPage/DeliveryAdmin/delivery_list.vue'

  // 退货管理
  import SalesReturnList from '../ChildrenPage/SalesReturnAdmin/salesreturn_list.vue'

  //评价管理
  import CommentList from '../ChildrenPage/CommentAdmin/CommentList.vue'

  //轮播图管理
  import CarouselList from '../ChildrenPage/CarouselAdmin/CarouselList.vue'
  import AddCarousel from '../ChildrenPage/CarouselAdmin/AddCarousel.vue'

  //分类管理
  import CategoryList from '../ChildrenPage/CategoryAdmin/CategoryList.vue'
  import AddCategory from '../ChildrenPage/CategoryAdmin/AddCategory.vue'

  export default {
    name: 'AdminPanel',
    components:{
      //管理中心
      AdminCenter,

      //用户管理
      UserList,
      HeadList,
      HeadEarnings,
      HeadApply,
      HeadResign,
      HeadModify,

      //商品管理
      ProductList,
      AddProduct,

      //订单管理
      OrderList,

      //发货管理
      DeliveryList,

      //退货管理
      SalesReturnList,

      //评价管理
      CommentList,

      //轮播图管理
      CarouselList,
      AddCarousel,

      //分类管理
      CategoryList,
      AddCategory


    },
    data(){
      return {
        menu_list:[],
        active_menu:'管理中心',
        user_name:'',
        avatar_url:'',
        now_menu:'管理中心'
      };
    },
    mounted(){
      var that=this;

      //定时刷新token
      setInterval(function(){

        that.axios.post('https://grouponapi.top/api/auth/refresh')
        .then(res => {

          console.log('刷新token');

          sessionStorage.setItem('admin_token',res.data.token_type+' '+res.data.access_token);
          sessionStorage.setItem('admin_expires_time',+new Date() + res.data.expires_in);

        })
      },1800000)

      //动态给列表赋高
      document.getElementById('aside_height').style.height=document.documentElement.clientHeight-62+'px';

      //获取列表
      this.axios.get('https://grouponapi.top/api/admin/menu/list').then(res=>{
        that.menu_list=res.data;
      }).catch(function(error){
        that.$message({
          message: error.response.data.message,
          type:'error'
        });
      });

      //动态给列表赋高
      window.onresize = function(){
         document.getElementById('aside_height').style.height=document.documentElement.clientHeight-62+'px';
      }

      //获取登陆人信息
      this.axios.get('https://grouponapi.top/api/auth/me').then(res=>{
        that.user_name=res.data.data.username;
        that.avatar_url=res.data.data.avatar_url;
      }).catch(function(error){
        that.$message({
          message: error.response.data.message,
          type:'error'
        });
      });

    },
    methods: {
      menu_click(menu_name){
        this.now_menu=menu_name
      },

      logout(){
        sessionStorage.clear();
        this.$router.push('/admin/login');
      }
    },
  }
</script>

<style>

  #avatar{
    border-radius: 50%;
    margin-top: 5px;
    float: left;
    margin-right: 1rem;
  }
  #username{
    height: 60px;
    float: left;
    line-height: 60px;
    color: #FFFFFF;
  }
  #logout{
    color: white;
    font-size: 14px;
    cursor: pointer;
    line-height: 60px;
  }
  #logout:hover{
    color: #d8d8d8;
  }
  .el-scrollbar__wrap{
    overflow-x: hidden;
  }
  .el-header {
    background-color: #303132;
  }

  .el-aside {
    background-color: #ffffff;
    height: 100%;

  }
  .el-submenu__title{
    padding-left: 2rem !important;
  }
  .admin_center{
    text-align: center;
  }
  .el-main {
    background-color: #E9EEF3;
  }
  #status_bar{
    margin-bottom: 1rem;
  }
  #pagination{
    text-align: center;
    margin-top: 0.2rem;
  }
</style>
