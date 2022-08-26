<!--  -->
<template>
  <div>
    <div class="orderDetail_main">
      <Header></Header>
      <div class="search_fixed hidden-sm-and-up">
        <el-row class="titleBox">
          <el-col :xs='{span: 2}'>
            <i class="fa fa-chevron-left" aria-hidden="true" v-on:click="back()"></i>
          </el-col>
          <el-col :xs='{span: 20}'>
            <p>商品详情</p>
          </el-col>
        </el-row>
      </div>
      <div class="orderDetail_content">
        <el-row class="order_status">当前订单状态：<span>{{ order.order.data.status_name }}</span></el-row>
        <el-row class="product_title hidden-xs-only">
          <el-col :sm="{ span:12 }">商品</el-col>
          <el-col :sm="{ span:4 }">单价</el-col>
          <el-col :sm="{ span:4 }">数量</el-col>
          <el-col :sm="{ span:4 }">总额</el-col>
        </el-row>
        <el-row class="productInfo">
          <el-col :xs="{ span:24 }" :sm="{ span:20 }" class="border" v-for="productInfo in orderBox" :key="productInfo.id">
            <router-link :to="'/product/detail?productId='+ productInfo.product_id" style="color:#000">
              <el-col :xs="{ span:18 }" :sm="{ span:14 }" class="img_name">
                <el-col :xs="{ span:8 }" :sm="{ span:4 }" class="productImg">
                  <img :src="productInfo.product.data.picture_url" alt="">
                </el-col>
                <el-col :xs="{ span:16 }" :sm="{ span:20 }" class="productName">
                  {{ productInfo.product.data.title }}
                </el-col>
              </el-col>
            </router-link>
            <el-col :xs="{ span:6 }" :sm="{ span:10 }" class="price_quantity">
              <el-col :sm="{ span:12 }" class="first">
                &yen;{{ productInfo.product.data.price }}
              </el-col>
              <el-col :sm="{ span:12 }">
                <span class="hidden-sm-and-up" style="font-size:14px">共</span> {{ productInfo.quantity }} <span class="hidden-sm-and-up" style="font-size:14px">件</span>
              </el-col>
            </el-col>
          </el-col>
          <el-col :sm="{ span:4 }" class="amount">
            <el-col :xs="{ span:10 }"><span class="hidden-sm-and-up">实际付款:</span></el-col>
            <el-col :xs="{ span:14 }">&yen;{{ order.order.data.amount }}</el-col>
          </el-col>
        </el-row>
        <el-row class="orderInfo">
          <el-row class="orderInfo_txt">订单信息</el-row>
          <el-row  class="orderInfo_content">
            <el-col :sm="{ span:12 }">
              自提点：<span>{{ order.order.data.package_deliver_name }}</span>
            </el-col>
            <el-col :sm="{ span:12 }">
              订单编号：<span>{{ order.order.data.id }}</span>
            </el-col>
            <el-col :sm="{ span:12 }">
              快递单号：<span>{{ order.order.data.express_no }}</span>
            </el-col>
            <el-col :sm="{ span:12 }">
              支付方式：<span>{{ order.order.data.mode_of_payment }}</span>
            </el-col>
            <el-col :sm="{ span:12 }">
              支付单号：<span>{{ order.order.data.payment_no }}</span>
            </el-col>
            <el-col :sm="{ span:12 }">
              创建时间：<span>{{ order.order.data.created_at }}</span>
            </el-col>
            <el-col :sm="{ span:12 }">
              付款时间：<span>{{ order.order.data.pay_time }}</span>
            </el-col>
            <el-col :sm="{ span:12 }">
              签收时间：<span>{{ order.order.data.signfor_time }}</span>
            </el-col>
          </el-row>
          <el-row class="timeline">
            <el-timeline :reverse="reverse">
              <el-timeline-item v-for="(activity, index) in activities" :key="index" :color="activity.color" size="large" :timestamp="activity.AcceptTime">
                {{activity.AcceptStation}}
                {{ activity.Remark==null ? '' : '（'+ activity.Remark +'）' }}
              </el-timeline-item>
            </el-timeline>
          </el-row>
        </el-row>

        <el-row class="btn">
          <el-col>
            <button type="button" v-show="ZF" style="background:#ff5000; border-color:#ff5000" v-on:click="zf()">去支付</button>
            <button type="button" v-show="QRSH" v-on:click="qrsh()">确认收货</button>
            <router-link :to="'/return/request?orderId=' +order.order.data.id">
              <button type="button" v-show="SQTK">申请退货</button>
            </router-link>
            <router-link :to="'/comment?orderId=' +order.order.data.id">
              <button type="button" v-show="PJ">评价</button>
            </router-link>
          </el-col>
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
  name: 'orderDeatil',
  components: {
    Header
  },
  data() {
    return {
      orderId:'',
      orderBox:'',
      order:'',
      reverse:false,
      activities:'',
      ZF:true,
      title:'请选择支付方式',
      payment_methodBoxShow:true,
      payqrcodeShow:false,
      dialogTableVisible:false,
      success:'',
      txt:'',
      QRSH:true,
      SQTK:true,
      PJ:true
    };
  },
  mounted() {
    document.querySelector('body').setAttribute('style', 'background-color:#e7f0e6');

    let that=this;

    this.orderId=this.$route.query.orderId;
    this.axios.get('https://grouponapi.top/api/userorder/'+this.orderId +'?include=order,product')
    .then(res => {

      that.orderBox=res.data.data
      that.order=res.data.data[0];
      let order_status=that.order.order.data.status;

      if(order_status == 1){
        that.ZF=true;
        that.QRSH=false;
        that.SQTK=false;
        that.PJ=false;
      }else if(order_status == 4){
        that.ZF=false;
        that.QRSH=true;
        that.SQTK=false;
        that.PJ=false;
      }else if(order_status == 5){
        that.ZF=false;
        that.QRSH=false;
        that.SQTK=true;
        that.PJ=true;
      }else if(order_status == 2 || order_status == 6){
        that.ZF=false;
        that.QRSH=false;
        that.SQTK=true;
        that.PJ=false;
      }else{
        that.ZF=false;
        that.QRSH=false;
        that.SQTK=false;
        that.PJ=false;
      }
    })

    this.axios.get('https://grouponapi.top/api/express/'+that.orderId +'/serch')
    .then(res => {

      that.activities=res.data.Traces;
      that.activities[0].color="#0BBD87";

    })


  },
  methods: {
    zf(){
      let that=this;
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
    qrsh(){
      let that=this;
      this.$confirm('是否确认收货？', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
      }).then(() => {
        this.axios.post('https://grouponapi.top/api/userorder/'+that.orderId +'/signfor')
        .then(res => {
          that.$message({
            message: '确认收货成功！',
            type: 'success'
          }).catch(function(error){
            console.log(error);
          })
        })
      }).catch(() => {
        this.$message({
          type: 'info',
          message: '已取消确认收货！'
        });
      });

    },
    back(){
      this.$router.back();
    }
  }
};
</script>
<style scoped>
/* @import url(); 引入css类 */
.orderDetail_main{
  width: 100%;
}
.orderDetail_content{
  width: 80%;
  margin: 1% auto 0;
  background-color: #fff;
}
.order_status{
  font-size: 20px;
  font-weight: bold;
  padding: 2.5%;
  background-color: #fff7eb;
  border: 2px solid #f58b0f;
  border-radius: 5px;
}
.order_status span{
  color: #f58b0f;
}
.product_title{
  margin-top: 2%;
  padding: 1% 0 0.5%;
  text-align: center;
  background-color: #ebe8e8;
  border: 1px solid #2a9b00;
}
.productInfo{
  position: relative;
  text-align: center;
  border: 1px solid #2a9b00;
  border-top: none;
}
.border{
  border-top: 1px solid #2a9b00;
}
.border:first-child{
  border-top: none;
}
.img_name{
  padding: 0.5% 0 0.5% 0.5%;
  border-right: 1px solid #2a9b00;

}
.productImg img{
  width: 100%;
  height: auto;
  aspect-ratio: 1/1;

}
.productName{
  text-align: left;
  padding-left: 2%;
}
.price_quantity div{
  padding: 11% 0;
  border-right: 1px solid #2a9b00;
}
.amount{
  position: absolute;
  text-align: center;
  top: 50%;
  right: 0;
  margin-top: -8px;
}
.orderInfo{
  margin-top: 2%;
  border: 1px solid #2a9b00;
}
.orderInfo_txt{
  padding: 1%;
  padding-bottom: 1%;
  font-size: 18px;
  border-bottom: 1px solid #2a9b00;
}
.orderInfo_content div{
  padding: 1%;
}
.timeline{
  padding: 2% 1% 0;
  border-top: 1px solid #2a9b00;
}
.btn{
  text-align: right;
  padding: 1%;
}
.btn button{
  width: 12%;
  font-size: 16px;
  color: #fff;
  padding: 0.5% 0;
  border-radius: 5px;
  border: 1px solid #2a9b00;
  background-color: #2a9b00;
  cursor: pointer;
  margin-left: 2%;
}
.payment_methodBox,
.payqrcodeBox{
  width: 100%;
  text-align: center;
}
.payment_method{
  display: inline-block;
  width: 25%;
  padding: 15px 25px;
  text-align: center;
  border-radius: 5px;
  cursor: pointer;
}
.payqrcode{
  width: 33%;
  margin: auto;
}
.payment_method:last-child{
  margin-left: 15%;
}
.payment_method:hover{
  box-shadow: 0 0 6px rgba(0, 0, 0, 0.5);
}
.payment_method img{
  width: 100%;
}
.payment_method p{
  margin-top: 10px;
  font-size: 20px;
}
.payqrcodeBox p{
  margin-top: 15px;
  font-size: 20px;
  color: #2a9b00;
}
.payqrcodeBox_a{
  position: absolute;
  top: 1.2rem;
  right: 1.2rem;
  font-weight: bold;
}
.payqrcodeBox_a a{
  color: #2a9b00;
}
@media only screen and (max-width: 767px){
  .search_fixed{
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 999;
        background-color: #2a9b00;
    }
    .titleBox{
        width: 100%;
        height: 36px;
        line-height: 36px;
        margin:5px auto;
        font-size: 20px;
        font-weight: bold;
        color: #fff;
        text-align: center;
    }
  .orderDetail_content{
    width: 98%;
    margin: 11.5% auto 0;
    background-color: #fff;
  }
  .productInfo{
    margin: 1%;
    border-top: 1px solid #2a9b00;
  }
.img_name{
  padding: 0.5% 0 0.5% 0.5%;
  border-right: 1px solid #2a9b00;

}
.productImg img{
  width: 100%;
  height: auto;
  aspect-ratio: 1/1;

}
.productName{
  text-align: left;
  padding-left: 3%;
}
.price_quantity div{
  text-align: right;
  padding: 3% 5%;
  border-right: none;
}
.amount{
  position: static;
  text-align: left;
  margin-top: 0;
  border-top: 1px solid #2a9b00;
  padding: 2%;
  font-weight: bold;
  color: #f31414;
}
.amount div:last-child{
  text-align: right;
  padding-top: 1%;
}
.orderInfo{
  margin: 2% 1%;
  border: 1px solid #2a9b00;
}
.orderInfo_txt{
  font-size: 15px;
}
.orderInfo_content div{
  font-size: 13px;
}
.timeline{
  margin: 0 1%
}
.btn{
  padding-bottom: 2%;
}
.btn button{
  width: 30%;
  padding: 1% 0;
  border-radius: 18px;
}

}
</style>
