import Vue from 'vue'
import Router from 'vue-router'
import axios from 'axios'

Vue.use(Router)

Vue.prototype.axios=axios
import Home from '@/components/Home/Home'
import Login from '../components/Login/Login'
import RegisterMobile from '../components/Register/RegisterMobile'
import RegisterMail from '../components/Register/RegisterMail'
import UpdatePasswordMobile from '../components/UpdatePassword/UpdatePasswordMobile'
import UpdatePasswordMail from '../components/UpdatePassword/UpdatePasswordMail'
import PickUpAdd from '../components/PickUp/PickUpAdd'
import PickUpList from '../components/PickUp/PickUpList'
import SearchList from '../components/SearchList/SearchList'
import Classify from '../components/Classify/Classify'
import RecommendList from '../components/RecommendList/RecommendList'
import Productdetail from '../components/Product_details/Product_details'
import Shoppingcart from '../components/ShoppingCart/ShoppingCart'
import Orderview from '../components/OrderView/OrderView'
import Mobilepayment from '../components/MobilePayment/MobilePayment'
import Myorder from '../components/MyOrder/MyOrder'
import Myorder_search from '../components/MyOrder/MyOrder_Secrch'
import Orderdetail from '../components/MyOrder/OrderDetail'
import Comment from '../components/MyOrder/Comment.vue'
import Returnrequest from '../components/MyOrder/Return_request'
import My from '../components/My/My'
import Headmanagement from '../components/My/Head_Management'

export default new Router({
  mode:'history',
  routes: [
    {
      path: '/',
      name: 'Home',
      component: Home,
      meta: {
        title:'首页',
        type:''
      }
    },
    {
      path: '/login',
      name: 'Login',
      component: Login,
      meta:{
        title:'登录',
        type:''
      }
    },
    {
      path: '/register/mobile',
      name: 'RegisterMobile',
      component: RegisterMobile,
      meta:{
        title:'手机注册',
        type:''
      }
    },
    {
      path: '/register/mail',
      name: 'RegisterMail',
      component: RegisterMail,
      meta:{
        title:'邮箱注册',
        type:''
      }
    },
    {
      path: '/updatepwd/mobile',
      name: 'UpdatePasswordMobile',
      component: UpdatePasswordMobile,
      meta:{
        title:'手机修改密码',
        type:''
      }
    },
    {
      path: '/updatepwd/mail',
      name: 'UpdatePasswordMail',
      component: UpdatePasswordMail,
      meta:{
        title:'邮箱修改密码',
        type:''
      }
    },
    {
      path: '/pickup/list',
      name: 'PickUpList',
      component: PickUpList,
      meta:{
        title:'自提点列表',
        type:'login'
      }
    },
    {
      path: '/pickup/add',
      name: 'PickUpAdd',
      component: PickUpAdd,
      meta:{
        title:'添加自提点',
        type:'login'
      }
    },
    {
      path: '/search/list',
      name: 'SearchList',
      component: SearchList,
      meta:{
        title:'',
        type:''
      }
    },
    {
      path: '/classify',
      name: 'Classify',
      component: Classify,
      meta:{
        title:'分类',
        type:''
      }
    },
    {
      path: '/recommend/list',
      name: 'RecommendList',
      component: RecommendList,
      meta:{
        title:'推荐列表',
        type:''
      }
    },
    {
      path: '/product/detail',
      name: 'Productdetail',
      component: Productdetail,
      meta:{
        title:'商品详情',
        type:''
      }
    },
    {
      path: '/shopping/cart',
      name: 'Shoppingcart',
      component: Shoppingcart,
      meta:{
        title:'购物车',
        type:'login'
      }
    },
    {
      path: '/order/view',
      name: 'Orderview',
      component: Orderview,
      meta: {
        title: '订单预览',
        type: 'login'
      }
    },
    {
      path: '/mobile/payment',
      name: 'Mobilepayment',
      component: Mobilepayment,
      meta: {
        title: '手机端支付',
        type: 'login'
      }
    },
    {
      path: '/my/order',
      name: 'Myorder',
      component: Myorder,
      meta: {
        title: '我的订单',
        type: 'login'
      }
    },
    {
      path: '/myorder/search',
      name: 'Myorder_search',
      component: Myorder_search,
      meta: {
        title: '订单搜索',
        type: 'login'
      }
    },
    {
      path: '/order/detail',
      name: 'Orderdetail',
      component: Orderdetail,
      meta: {
        title: '订单详情',
        type: 'login'
      }
    },
    {
      path: '/comment',
      name: 'Comment',
      component: Comment,
      meta: {
        title: '商品评论',
        type: 'login'
      }
    },
    {
      path: '/return/request',
      name: 'Returnrequest',
      component: Returnrequest,
      meta: {
        title: '退货申请',
        type: 'login'
      }
    },
    {
      path: '/my',
      name: 'My',
      component: My,
      meta: {
        title: '我的',
        type: 'login'
      }
    },
    {
      path: '/head/management',
      name: 'Headmanagement',
      component: Headmanagement,
      meta: {
        title: '团长管理',
        type: 'login'
      }
    }
  ]
})
