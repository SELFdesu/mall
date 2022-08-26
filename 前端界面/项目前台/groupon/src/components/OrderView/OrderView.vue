<!--  -->
<template>
  <div>
    <div class="orderview_main">
      <Header></Header>
      <el-row class="orderview_header hidden-sm-and-up">
        <el-col :xs='{span: 2}'>
          <i class="fa fa-chevron-left" aria-hidden="true" v-on:click="back()"></i>
        </el-col>
        <el-col :xs='{span: 20}'>
          <span>确认订单</span>
        </el-col>
      </el-row>
      <div class="orderview_content">
        <div class="orderview_pickupAll">
          <el-row class="orderview_pickupTitle hidden-xs-only">选择自提点</el-row>
          <el-row class="orderview_pickupOne">
            <el-col class="orderview_pickup">
              <el-row>
                <el-col class="pickup_name" :xs="{span:19}" :sm="{span:18}">自提点：{{ orderview_pickup.store_name }}</el-col>
                <el-col class="management_add hidden-xs-only" :sm="{span:6}">
                  <router-link to="/pickup/list">
                    选择/添加自提点
                  </router-link>
                </el-col>
                <el-col class="management_i hidden-sm-and-up" :xs="{span:5}">
                  <router-link to="/pickup/list">
                    <span>切换&nbsp;<i class="fa fa-chevron-right"></i></span>
                  </router-link>
                </el-col>
              </el-row>
              <el-row class="pickupAddress">
                <p v-if="pickup_show">{{ orderview_pickup.address_name }}&nbsp;&nbsp;&nbsp;{{ orderview_pickup.address_info }}</p>
                <el-alert v-else title="请先选择自提点，才可以提交订单！" type="error" :closable="false"></el-alert>
              </el-row>
              <el-row>
                <el-alert title="下单后如有问题，可联系团长为您解决。" type="error" :closable="false"></el-alert>
              </el-row>
            </el-col>
          </el-row>
        </div>
        <div class="confirmation_msg">
          <el-row class="confirmation_msgTitle hidden-xs-only">
            <el-col :sm="{span:9}">商品信息</el-col>
            <el-col :sm="{span:4, offset:1}">单价</el-col>
            <el-col :sm="{span:4, offset:1}">数量</el-col>
            <el-col :sm="{span:4, offset:1}">小计</el-col>
          </el-row>
          <el-row class="product_message" v-for="orderview_product in orderview_productBox.products" :key="orderview_product.product_id">
            <el-col class="product_img" :xs="{span:5}" :sm="{span:2}">
              <img :src="orderview_product.picture" alt="">
            </el-col>
            <el-col class="product_Txt" :xs="{span:19}" :sm="{span:22}">
              <el-col class="product_Txt_name"  :xs="{span:23, offset:1}" :sm="{span:8}">
                <span>{{ orderview_product.title }}</span>
              </el-col>
              <el-col class="center hidden-xs-only" :sm="{span:4, offset:1}">
                <span>{{ orderview_product.unit_price }}</span>
              </el-col>
              <el-col class="center hidden-sm-and-up" :xs="{span:11,offset:1}" :sm="{span:4, offset:2}">
                <span class="hidden-sm-and-up">&yen;</span>
                <span>{{ orderview_product.total_amount }}</span>
              </el-col>
              <el-col class="center right" :xs="{span:12}" :sm="{span:3, offset:2}">
                <span class="hidden-sm-and-up">共</span>
                <span>{{ orderview_product.quantity }}</span>
                <span class="hidden-sm-and-up">件</span>
              </el-col>
              <el-col class="center hidden-xs-only" :sm="{span:4, offset:2}">
                <span>{{ orderview_product.total_amount }}</span>
              </el-col>
            </el-col>
          </el-row>
        </div>
        <el-row class="orderAmount">
          共计&nbsp;<span>{{ order_pieceAll }}<span style="color:#000">件</span></span>
          应付总额：<span>&yen;{{ orderview_productBox.orderAmount }}</span>&nbsp;&nbsp;
          <button id="sbtBtn" v-on:click="submitOrder" :disabled="disabled">提交订单</button>
        </el-row>
      </div>
      <el-dialog class="faBox" :title="title" :show-close=false :close-on-click-modal=false :close-on-press-escape=false :visible.sync="dialogTableVisible">
        <div class="payment_methodBox" v-show="payment_methodBoxShow">
          <div class="payment_method" v-on:click="payment_method('web')">
            <img src="../../assets/images/alipay.png" alt="">
            <p>网站支付</p>
          </div>
          <div class="payment_method" v-on:click="payment_method('scan')">
            <img src="../../assets/images/scan.png" alt="">
            <p>扫码支付</p>
          </div>
        </div>
        <div v-show="payqrcodeShow" class="payqrcodeBox_a">
          点击前往：<router-link to="/my/order">我的订单</router-link>
        </div>
        <div class="payqrcodeBox" v-show="payqrcodeShow">
          <div class="payqrcode" ref="payqrcode">

          </div>
          <p>{{ success }}</p>
          <p>{{ txt }}</p>
        </div>
      </el-dialog>
    </div>
  </div>
</template>

<script>
import Header from '../conn/header/Header'
import QRCode from 'qrcodejs2'

export default {
  name:'orderview',
  components: {
    Header
  },
  data() {
    return {
      title:'请选择支付方式',
      payment_methodBoxShow:true,
      payqrcodeShow:false,
      dialogTableVisible:false,
      pickup_show:true,
      disabled:false,
      orderview_pickup:'',
      orderview_productBox:'',
      order_pieceAll:0,
      orderId:'',
      success:'',
      txt:''
    };
  },
  mounted() {
      document.querySelector('body').setAttribute('style', 'background-color:#e7f0e6');

      let that=this;
      this.axios.get('https://grouponapi.top/api/user/delivery/list')
      .then(res => {
        if(res.data.data[0] != undefined && localStorage.getItem('pickupId')!=null){
          that.orderview_pickup=res.data.data[0];
        }else{
          that.pickup_show=false;
          that.disabled=true;
          document.getElementById("sbtBtn").style.backgroundColor="#ccc"
        }
      }).catch(function(error){
        console.log(error);
      })

      this.axios.post('https://grouponapi.top/api/userorder/page/preview',{
        type: this.$route.query.type,
        products: this.$route.query.products
      }).then(res => {

        that.orderview_productBox=res.data;
     
        that.orderview_productBox.products.forEach(function(item){
          that.order_pieceAll+=item.quantity;
        })

      }).catch(function(error){
        console.log(error);
      })
  },
  methods: {
    submitOrder(){
      let that=this;
      var loading=that.$loading({
        lock:true,
        text:'订单处理中，请销后！',
        spinner:'el-icon-loading',
        background:'rgba(0,0,0,0.7)'
      })
      this.axios.post('https://grouponapi.top/api/userorder',{
        type:this.$route.query.type,
        package_deliver_id: localStorage.getItem('pickupId'),
        products: this.$route.query.products
      }).then(res => {
  
        that.orderId=res.data.id;
        loading.close();
        let flag = navigator.userAgent.match(/(phone|pad|pod|iPhone|iPod|ios|iPad|Android|Mobile|BlackBerry|IEMobile|MQQBrowser|JUC|Fennec|wOSBrowser|BrowserNG|WebOS|Symbian|Windows Phone)/i)
        if(flag){
          that.dialogTableVisible=false;
          var wepLoading=that.$loading({
            lock:true,
            text:'订单处理中，请销后！',
            spinner:'el-icon-loading',
            background:'rgba(0,0,0,0.7)'
          })
          that.axios.get('https://grouponapi.top/api/order/alipay/'+that.orderId +"?type=wep")
           .then(res => {
   
             let routeData = this.$router.resolve({path:'/mobile/payment', query: {htmls: res.data}});
             window.open(routeData.href, '_self')
             wepLoading.close();
           })
        }else{
          that.dialogTableVisible=true;
        }
      }).catch(function(error){
        that.$message({
          type: 'error',
          message: '订单提交失败！请刷新页面或重新选择自提点！'
        });
      })
    },

    payment_method(type){
      let that=this;
      var PcLoading=that.$loading({
        lock:true,
        text:'订单处理中，请销后！',
        spinner:'el-icon-loading',
        background:'rgba(0,0,0,0.7)'
      })
      that.axios.get('https://grouponapi.top/api/order/alipay/'+that.orderId +"?type="+type)
      .then(res => {
      
        if(type == 'scan'){
          that.payment_methodBoxShow=false;
          that.payqrcodeShow=true;
          that.title="请扫码进行支付";
          that.payqrcode(res.data);
          var interval=setInterval(function(){
            that.axios.get('https://grouponapi.top/api/userorder/'+that.orderId+'?include=order')
             .then(res => {
           
               if(res.data.data[0].order.data.status != 1){
                 clearInterval(interval);
          
                 that.success='支付成功！';
                 that.txt='页面将在3秒后进行跳转'
                 setTimeout(function(){
                   that.$router.push('/my/order');
                 },3000)
               }
             })
          },5000)
        }else{
          let routeData = this.$router.resolve({path:'/mobile/payment', query: {htmls: res.data}});
          window.open(routeData.href, '_self')
        }
        PcLoading.close();
      })
    },
    payqrcode(path){
      let qrcode=new QRCode(this.$refs.payqrcode, {
        width: 150,
        height: 150,
        text: path
      })
    },

    back(){
      this.$router.back();
    }
  }
};
</script>
<style scoped>
/* @import url(); 引入css类 */
@import url('../../assets/css/orderview.css');
</style>
