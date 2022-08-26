<!--  -->
<template>
  <div>
      <div class="myorder_main">
          <Header selectLi="3"></Header>
          <div class="search_fixed hidden-sm-and-up">
            <el-row class="titleBox">
              <el-col :xs='{span: 2}'>
                  <i class="fa fa-chevron-left" aria-hidden="true" v-on:click="back()"></i>
              </el-col>
              <el-col :xs='{span: 20}'>
                  <p>我的订单</p>
              </el-col>
            </el-row>
          </div>
          <div class="myorder_content">
            <div class="myorder_mainTop">
              <el-row>
                  <el-tabs  v-model="activeName" stretch @tab-click="handleClick">
                    <el-tab-pane label="全部订单" name="all">
                      <div class="all_ordersBox" v-if="boxShow">
                        <div class="search_title">
                          <el-row class="search">
                            <div>
                              <el-select size="small" v-model="value" placeholder="请选择搜索方式">
                                <el-option v-for="item in options" :key="item.value" :label="item.label" :value="item.value"></el-option>
                              </el-select>
                              <input @keyup.enter="searchBtn" type="text" v-model.trim="searchVal" name="" id="search" placeholder="输入商品标题或订单号进行搜索" autocomplete="off">
                              <input type="button" id="btn" value="订单搜索" v-on:click="searchBtn()">
                            </div>
                          </el-row>
                          <el-row class="title hidden-xs-only">
                            <el-col :sm="{ span:10 }">商品</el-col>
                            <el-col :sm="{ span:4 }">单价</el-col>
                            <el-col :sm="{ span:3 }">数量</el-col>
                            <el-col :sm="{ span:4 }">订单金额</el-col>
                            <el-col :sm="{ span:3 }">操作</el-col>
                          </el-row>
                        </div>
                        <div class="product_main" v-for="myorder_product in dataBox" :key="myorder_product.id">
                          <el-row class="data_orderNum">
                            <el-col :sm="{ span:16}">
                              <span>{{ myorder_product.pay_time == null ? '未付款' : myorder_product.pay_time }}</span>
                              &nbsp;&nbsp;&nbsp;
                              <span  class="hidden-xs-only">订单编号：{{ myorder_product.id }}</span>
                            </el-col>
                            <el-col :sm="{ span:8}" style="text-align:right;" v-if="!myorder_product.pay_time">
                              订单将在 {{ Math.floor(myorder_product.expiry_date/60) }}分{{ myorder_product.expiry_date%60}}秒 后过期
                            </el-col>
                          </el-row>
                          <el-row class="productInfo">
                            <router-link :to="'/order/detail?orderId='+myorder_product.id" style="color:#000">
                              <el-col :sm="{ span:17}" class="productInfoborder" v-for="products in myorder_product.orderDetail.data" :key="products.id">
                                <el-col :xs="{ span:6}" :sm="{ span:3}" class="productImg">
                                  <img :src="products.product.data.picture_url" alt="">
                                </el-col>
                                <el-col :xs="{ span:12}" :sm="{ span:11}" class="productTitle">
                                  {{ products.product.data.title}}
                                </el-col>
                                <el-col :xs="{ span:6}" :sm="{ span:10}" class="Txtcenter" style="padding-left:1%;">
                                  <el-row>
                                    <el-col :sm="{ span:13}">
                                      &yen;{{ products.product.data.price}}
                                    </el-col>
                                    <el-col :sm="{ span:11}">
                                      <span class="hidden-sm-and-up">共</span>{{ products.quantity }}<span class="hidden-sm-and-up">件</span>
                                    </el-col>
                                  </el-row>
                                </el-col>
                              </el-col>
                            </router-link>
                            <el-col :xs="{ span:24 }" :sm="{ span: 7}" class="amount_operation" >
                              <el-col :sm="{ span:13}" class="amount">
                                <span class="hidden-sm-and-up">订单金额：</span>&yen;{{ myorder_product.amount }}
                              </el-col>
                              <el-col :sm="{ span:11}" class="operation">
                                <div v-show="status(myorder_product.status, 'DDXQ')" style="padding-bottom:3%">
                                  <router-link :to="'/order/detail?orderId=' +myorder_product.id">
                                    <button type="button" class="DDXQ">订单详情</button>
                                  </router-link>
                                </div>
                                <button class="FKQH" type="button" v-show="status(myorder_product.status, 'ZF')" v-on:click="pay(myorder_product.id)" style="background:#ff5000; border-color:#ff5000;">去付款</button>
                                <button class="FKQH" type="button" v-show="status(myorder_product.status, 'QRSH')" v-on:click="qrsh(myorder_product.id)">确认签收</button>
                                <div class="tuihuo_pingjia">
                                  <router-link :to="'/comment?orderId=' +myorder_product.id">
                                    <button type="button" v-show="status(myorder_product.status, 'PJ')">评价</button>
                                  </router-link>
                                  <router-link :to="'/return/request?orderId=' +myorder_product.id">
                                    <button type="button" v-show="status(myorder_product.status, 'SQTH')">申请退货</button>
                                  </router-link>
                                </div>
                                <el-col v-show="status(myorder_product.status, 'YTK')">
                                  <router-link :to="'/return/request?orderId='+myorder_product.id">
                                    <button style="margin-top: 8%;" type="button">退货详情</button>
                                  </router-link>
                                </el-col>
                              </el-col>
                            </el-col>
                          </el-row>
                        </div>
                      </div>
                      <div class="prompt_text" v-else>
                        <p>您还没有任何订单哦~</p>
                        <router-link to="/">
                          <button type="button">去首页看看</button>
                        </router-link>
                      </div>
                    </el-tab-pane>


                    <el-tab-pane label="待付款" name="unpaid">
                      <div class="obligationBox" v-if="boxShow">
                        <div class="search_title">
                          <el-row class="title hidden-xs-only">
                            <el-col :sm="{ span:10 }">商品</el-col>
                            <el-col :sm="{ span:4 }">单价</el-col>
                            <el-col :sm="{ span:3 }">数量</el-col>
                            <el-col :sm="{ span:4 }">订单金额</el-col>
                            <el-col :sm="{ span:3 }">操作</el-col>
                          </el-row>
                        </div>
                        <div class="product_main" v-for="obligations in dataBox" :key="obligations.id">
                          <el-row class="data_orderNum">
                            <el-col :sm="{ span: 16 }">
                              <span>未付款</span>
                              &nbsp;&nbsp;&nbsp;
                              <span  class="hidden-xs-only">订单编号：{{ obligations.id }}</span>
                            </el-col>
                            <el-col :sm="{ span:8}" style="text-align:right;">
                              订单将在 {{ Math.floor(obligations.expiry_date/60) }}分{{ obligations.expiry_date%60}}秒 后过期
                            </el-col>
                          </el-row>
                          <el-row class="productInfo">
                            <router-link :to="'/order/detail?orderId='+obligations.id" style="color:#000">
                              <el-col :sm="{ span:17}" class="productInfoborder" v-for="obligation in obligations.orderDetail.data" :key="obligation.id">
                                <el-col :xs="{ span:6}" :sm="{ span:3}" class="productImg">
                                  <img :src="obligation.product.data.picture_url" alt="">
                                </el-col>
                                <el-col :xs="{ span:12}" :sm="{ span:11}" class="productTitle">
                                  {{ obligation.product.data.title}}
                                </el-col>
                                <el-col :xs="{ span:6}" :sm="{ span:10}" class="Txtcenter" style="padding-left:1%">
                                  <el-row>
                                    <el-col :sm="{ span:13}">
                                      &yen;{{ obligation.product.data.price}}
                                    </el-col>
                                    <el-col :sm="{ span:11}">
                                      <span class="hidden-sm-and-up">共</span>{{ obligation.quantity}}<span class="hidden-sm-and-up">件</span>
                                    </el-col>
                                  </el-row>
                                </el-col>
                              </el-col>
                            </router-link>
                            <el-col :sm="{ span: 7}" class="amount_operation" >
                              <el-col :sm="{ span:13}" class="amount">
                                 <span class="hidden-sm-and-up">订单金额：</span>&yen;{{ obligations.amount }}
                              </el-col>
                              <el-col :sm="{ span:11}" class="operation">
                                <router-link to="">
                                  <button type="button" v-on:click="pay(obligations.id)" style="background:#ff5000; border-color:#ff5000;">去付款</button>
                                </router-link>
                              </el-col>
                            </el-col>
                          </el-row>
                        </div>
                      </div>
                      <div class="prompt_text" v-else>
                        <p>没有待付款的订单哦~</p>
                        <router-link to="/">
                          <button type="button">去首页看看</button>
                        </router-link>
                      </div>
                    </el-tab-pane>


                    <el-tab-pane label="待发货" name="waitdeliver">
                      <div class="sendBox" v-if="boxShow">
                        <div class="search_title">
                          <el-row class="title hidden-xs-only">
                            <el-col :sm="{ span:10 }">商品</el-col>
                            <el-col :sm="{ span:4 }">单价</el-col>
                            <el-col :sm="{ span:3 }">数量</el-col>
                            <el-col :sm="{ span:4 }">订单金额</el-col>
                            <el-col :sm="{ span:3 }">操作</el-col>
                          </el-row>
                        </div>
                        <div class="product_main" v-for="sends in dataBox" :key="sends.id">
                          <el-row class="data_orderNum">
                            <el-col>
                              <span>{{ sends.pay_time }}</span>
                              &nbsp;&nbsp;&nbsp;
                              <span  class="hidden-xs-only">订单编号：{{ sends.id }}</span>
                            </el-col>
                          </el-row>
                          <el-row class="productInfo">
                            <router-link :to="'/order/detail?orderId='+sends.id" style="color:#000">
                              <el-col :sm="{ span:17}" class="productInfoborder" v-for="send in sends.orderDetail.data" :key="send.id">
                                <el-col :xs="{ span:6}" :sm="{ span:3}" class="productImg">
                                  <img :src="send.product.data.picture_url" alt="">
                                </el-col>
                                <el-col :xs="{ span:12}" :sm="{ span:11}" class="productTitle">
                                  {{ send.product.data.title}}
                                </el-col>
                                <el-col :xs="{ span:6}" :sm="{ span:10}" class="Txtcenter" style="padding-left:1%">
                                  <el-row>
                                    <el-col :sm="{ span:13}">
                                      &yen;{{ send.product.data.price}}
                                    </el-col>
                                    <el-col :sm="{ span:11}">
                                      <span class="hidden-sm-and-up">共</span>{{ send.quantity}}<span class="hidden-sm-and-up">件</span>
                                    </el-col>
                                  </el-row>
                                </el-col>
                              </el-col>
                            </router-link>
                            <el-col :sm="{ span: 7}" class="amount_operation" >
                              <el-col :sm="{ span:13}" class="amount">
                                 <span class="hidden-sm-and-up">订单金额：</span>&yen;{{ sends.amount }}
                              </el-col>
                              <el-col :sm="{ span:11}" class="operation">
                                <router-link :to="'/return/request?orderId=' +sends.id">
                                   <button type="button">申请退货</button>
                                </router-link>
                              </el-col>
                            </el-col>
                          </el-row>
                        </div>
                      </div>
                      <div class="prompt_text" v-else>
                        <p>没有待发货的订单哦~</p>
                        <router-link to="/">
                          <button type="button">去首页看看</button>
                        </router-link>
                      </div>
                    </el-tab-pane>


                    <el-tab-pane label="待签收" name="waitpick">
                      <div class="waitpickBox" v-if="boxShow">
                        <div class="search_title">
                          <el-row class="title hidden-xs-only">
                            <el-col :sm="{ span:10 }">商品</el-col>
                            <el-col :sm="{ span:4 }">单价</el-col>
                            <el-col :sm="{ span:3 }">数量</el-col>
                            <el-col :sm="{ span:4 }">订单金额</el-col>
                            <el-col :sm="{ span:3 }">操作</el-col>
                          </el-row>
                        </div>
                        <div class="product_main" v-for="waitpics in dataBox" :key="waitpics.id">
                          <el-row class="data_orderNum">
                            <el-col>
                              <span>{{ waitpics.pay_time }}</span>
                              &nbsp;&nbsp;&nbsp;
                              <span  class="hidden-xs-only">订单编号：{{ waitpics.id }}</span>
                            </el-col>
                          </el-row>
                          <el-row class="productInfo">
                            <router-link :to="'/order/detail?orderId='+waitpics.id" style="color:#000">
                              <el-col :sm="{ span:17}" class="productInfoborder" v-for="waitpic in waitpics.orderDetail.data" :key="waitpic.id">
                                <el-col :xs="{ span:6}" :sm="{ span:3}" class="productImg">
                                  <img :src="waitpic.product.data.picture_url" alt="">
                                </el-col>
                                <el-col :xs="{ span:12}" :sm="{ span:11}" class="productTitle">
                                  {{ waitpic.product.data.title}}
                                </el-col>
                                <el-col :xs="{ span:6}" :sm="{ span:10}" class="Txtcenter" style="padding-left:1%">
                                  <el-row>
                                    <el-col :sm="{ span:13}">
                                      &yen;{{ waitpic.product.data.price}}
                                    </el-col>
                                    <el-col :sm="{ span:11}">
                                      <span class="hidden-sm-and-up">共</span>{{ waitpic.quantity}}<span class="hidden-sm-and-up">件</span>
                                    </el-col>
                                  </el-row>
                                </el-col>
                              </el-col>
                            </router-link>
                            <el-col :sm="{ span: 7}" class="amount_operation" >
                              <el-col :sm="{ span:13}" class="amount">
                                 <span class="hidden-sm-and-up">订单金额：</span>&yen;{{ waitpics.amount }}
                              </el-col>
                              <el-col :sm="{ span:11}" class="operation">
                                <button type="button" :style="'background:'+(waitpics.status==3 ? '#999' : '#2a9b00')" :disabled="waitpics.status==4?false:true" v-on:click="qrsh(waitpics.id)">确认签收</button>
                              </el-col>
                            </el-col>
                          </el-row>
                        </div>
                      </div>
                      <div class="prompt_text" v-else>
                        <p>没有待签收的订单哦~</p>
                        <router-link to="/">
                          <button type="button">去首页看看</button>
                        </router-link>
                      </div>
                    </el-tab-pane>


                    <el-tab-pane label="待评价" name="waitcomment">
                      <div class="waitcommentBox" v-if="boxShow">
                        <div class="search_title">
                          <el-row class="title hidden-xs-only">
                            <el-col :sm="{ span:10 }">商品</el-col>
                            <el-col :sm="{ span:4 }">单价</el-col>
                            <el-col :sm="{ span:3 }">数量</el-col>
                            <el-col :sm="{ span:4 }">订单金额</el-col>
                            <el-col :sm="{ span:3 }">操作</el-col>
                          </el-row>
                        </div>
                        <div class="product_main" v-for="waitcomments in dataBox" :key="waitcomments.id">
                          <el-row class="data_orderNum">
                            <el-col>
                              <span>{{ waitcomments.pay_time }}</span>
                              &nbsp;&nbsp;&nbsp;
                              <span  class="hidden-xs-only">订单编号：{{ waitcomments.id }}</span>
                            </el-col>
                          </el-row>
                          <el-row class="productInfo">
                            <router-link :to="'/order/detail?orderId='+waitcomments.id" style="color:#000">
                              <el-col :sm="{ span:17}" class="productInfoborder" v-for="waitcomment in waitcomments.orderDetail.data" :key="waitcomment.id">
                                <el-col :xs="{ span:6}" :sm="{ span:3}" class="productImg">
                                  <img :src="waitcomment.product.data.picture_url" alt="">
                                </el-col>
                                <el-col :xs="{ span:12}" :sm="{ span:11}" class="productTitle">
                                  {{ waitcomment.product.data.title}}
                                </el-col>
                                <el-col :xs="{ span:6}" :sm="{ span:10}" class="Txtcenter" style="padding-left:1%">
                                  <el-row>
                                    <el-col :sm="{ span:13}">
                                      &yen;{{ waitcomment.product.data.price}}
                                    </el-col>
                                    <el-col :sm="{ span:11}">
                                      <span class="hidden-sm-and-up">共</span>{{ waitcomment.quantity}}<span class="hidden-sm-and-up">件</span>
                                    </el-col>
                                  </el-row>
                                </el-col>
                              </el-col>
                            </router-link>
                            <el-col :sm="{ span: 7}" class="amount_operation" >
                              <el-col :sm="{ span:13}" class="amount">
                                 <span class="hidden-sm-and-up">订单金额：</span>&yen;{{ waitcomments.amount }}
                              </el-col>
                              <el-col :sm="{ span:11}" class="operation" style="margin-top:-18px">
                                <router-link :to="'/comment?orderId='+waitcomments.id">
                                  <button type="button">去评价</button>
                                </router-link>
                                <router-link :to="'/return/request?orderId=' +waitcomments.id">
                                   <button type="button" style="margin-top:5%;">申请退货</button>
                                </router-link>
                              </el-col>
                            </el-col>
                          </el-row>
                        </div>
                      </div>
                      <div class="prompt_text" v-else>
                        <p>没有待评价的订单哦~</p>
                        <router-link to="/">
                          <button type="button">去首页看看</button>
                        </router-link>
                      </div>
                    </el-tab-pane>


                    <el-tab-pane label="已退货商品" name="salesreturn">
                      <div class="salesreturnBox" v-if="boxShow">
                        <div class="search_title">
                          <el-row class="title hidden-xs-only">
                            <el-col :sm="{ span:10 }">商品</el-col>
                            <el-col :sm="{ span:4 }">单价</el-col>
                            <el-col :sm="{ span:3 }">数量</el-col>
                            <el-col :sm="{ span:4 }">订单金额</el-col>
                            <el-col :sm="{ span:3 }">操作</el-col>
                          </el-row>
                        </div>
                        <div class="product_main" v-for="salesreturns in dataBox" :key="salesreturns.id">
                          <el-row class="data_orderNum">
                            <el-col>
                              <span>{{ salesreturns.pay_time }}</span>
                              &nbsp;&nbsp;&nbsp;
                              <span  class="hidden-xs-only">订单编号：{{ salesreturns.id }}</span>
                            </el-col>
                          </el-row>
                          <el-row class="productInfo">
                            <router-link :to="'/order/detail?orderId='+salesreturns.id" style="color:#000">
                              <el-col :sm="{ span:17}" class="productInfoborder" v-for="salesreturn in salesreturns.orderDetail.data" :key="salesreturn.id">
                                <el-col :xs="{ span:6}" :sm="{ span:3}" class="productImg">
                                  <img :src="salesreturn.product.data.picture_url" alt="">
                                </el-col>
                                <el-col :xs="{ span:12}" :sm="{ span:11}" class="productTitle">
                                  {{ salesreturn.product.data.title}}
                                </el-col>
                                <el-col :xs="{ span:6}" :sm="{ span:10}" class="Txtcenter" style="padding-left:1%">
                                  <el-row>
                                    <el-col :sm="{ span:13}">
                                      &yen;{{ salesreturn.product.data.price}}
                                    </el-col>
                                    <el-col :sm="{ span:11}">
                                      <span class="hidden-sm-and-up">共</span>{{ salesreturn.quantity}}<span class="hidden-sm-and-up">件</span>
                                    </el-col>
                                  </el-row>
                                </el-col>
                              </el-col>
                            </router-link>
                            <el-col :sm="{ span: 7}" class="amount_operation" >
                              <el-col :sm="{ span:13}" class="amount">
                                 <span class="hidden-sm-and-up">订单金额：</span>&yen;{{ salesreturns.amount }}
                              </el-col>
                              <el-col :sm="{ span:11}" class="operation">
                                <router-link :to="'/return/request?orderId='+salesreturns.id">
                                    <button type="button">退货详情</button>
                                </router-link>
                              </el-col>
                            </el-col>
                          </el-row>
                        </div>
                      </div>
                      <div class="prompt_text" v-else>
                        <p>没有已退货的订单哦~</p>
                        <router-link to="/">
                          <button type="button">去首页看看</button>
                        </router-link>
                      </div>
                    </el-tab-pane>
                  </el-tabs>
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
                点击前往：<a href="#" @click="go()">我的订单</a>
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
  </div>
</template>

<script>
import Header from '../conn/header/Header';
import QRCode from 'qrcodejs2'

export default {
  name:'Myorder',
  components: {
    Header
  },
  data() {
    return {
        options: [{
          value: 'id',
          label: '订单编号'
        }, {
          value: 'product_name',
          label: '商品名称'
        }],
        value:'product_name',
        activeName: '',
        dataBox:[],
        checkTab:'',
        boxShow:'false',
        page:1,
        total_page:'',
        title:'请选择支付方式',
        orderId:'',
        payment_methodBoxShow:true,
        payqrcodeShow:false,
        dialogTableVisible:false,
        success:'',
        txt:'',
        searchVal:'',
        disabled:true,
    };
  },
  mounted() {
      document.querySelector('body').setAttribute('style', 'background-color:#e7f0e6');

      let that=this;

      if(this.$route.query.tabName != undefined){
        that.activeName=this.$route.query.tabName;
        that.checkTab=this.$route.query.tabName;
      }else{
        that.activeName='all';
        that.checkTab='all';
      }

      this.axios.get('https://grouponapi.top/api/userorder?type='+that.checkTab+'&page='+that.page +'&include=orderDetail,orderDetail.product')
       .then(res => {

         if(res.data.data.length != 0){
           that.boxShow=true;
           that.dataBox=res.data.data;

           that.total_page=res.data.meta.pagination.total_pages;
         }else{
           that.boxShow=false;
         }
       }).catch(function(error){
         console.log(error);
       })

        // 分页
      this.$nextTick(() => {
        let pg=false;
        window.addEventListener('scroll', function(){
          let aa=parseInt(document.body.scrollHeight-window.innerHeight);
          let bb=parseInt(document.documentElement.scrollTop);
          if(aa-bb==0){
            pg=true;
          }
          if(pg && that.page < that.total_page){
            pg=false;
            that.page++;
            that.show(that.page);
          }
        });
      })

      if(that.dataBox){
        var expire_interval=setInterval(function(){
          that.dataBox.forEach(function(item){
            if(!isNaN(item.expiry_date)){
              if(item.expiry_date==0){
                clearInterval(expire_interval);
              }else{
                item.expiry_date--;
              }

            }
          })
        },1000)
      }

  },
  methods: {
    status(status, btnType){
      let that=this;
      if(btnType == 'DDXQ'){
        if(status == 3){
          return true;
        }else{
          return false
        }
      }else if(btnType == 'ZF'){
        if(status == 1){
          return true;
        }else{
          return false
        }
      }else if(btnType == 'QRSH'){
        if(status == 4){
          return true;
        }else{
          return false
        }
      }else if(btnType == 'SQTH'){
        if(status == 2 || status == 5 || status == 6){
          return true;
        }else{
          return false
        }
      }else if(btnType == 'PJ'){
        if(status == 5){
          return true;
        }else{
          return false
        }
      }else if(btnType == 'YTK'){
        if(status == 0){
          return true;
        }else{
          return false
        }
      }
    },
    handleClick(tab, event) {
      var that=this;
      that.checkTab=tab.name;
       this.axios.get('https://grouponapi.top/api/userorder?type='+that.checkTab+'&page='+that.page +'&include=orderDetail,orderDetail.product')
       .then(res => {
console.log(res)
         if(res.data.data.length != 0){
           that.boxShow=true;
           that.dataBox=res.data.data;
           that.total_page=res.data.meta.pagination.total_pages;
         }else{
           that.boxShow=false;
         }
       }).catch(function(error){
         console.log(error);
       })
    },
        //分页
    show(page){

      let that=this;
      this.axios.get('https://grouponapi.top/api/userorder?type='+that.checkTab+'&page='+page +'&include=orderDetail,orderDetail.product')
      .then(res => {
        for(var i=0; i<res.data.data.length; i++){
          that.dataBox.push(res.data.data[i]);
        }
      }).catch(function(error){
        console.log(error);
      });
    },
    pay(orderId){
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
        that.orderId=orderId;
        that.axios.get('https://grouponapi.top/api/order/alipay/'+that.orderId +"?type=wep")
         .then(res => {
           let routeData = this.$router.resolve({path:'/mobile/payment', query: {htmls: res.data}});
           window.open(routeData.href, '_self')
           wepLoading.close();
         })
      }else{
        that.dialogTableVisible=true;
        that.orderId=orderId;
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
                   history.go(0)
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
    go(){
      history.go(0);
    },
    searchBtn(){
      let that=this;
      if(this.searchVal == ''){
        this.$message({
          message:'输入框不能为空！',
          type: 'error'
        })
      }else{
        this.$router.push('/myorder/search?type='+this.value+'&searchInfo='+this.searchVal);
      }
    },
    qrsh(orderId){
      let that=this;
      this.$confirm('是否确认收货？', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
      }).then(() => {
        this.axios.post('https://grouponapi.top/api/userorder/'+orderId +'/signfor')
        .then(res => {
          that.$message({
            message: '确认签收成功！',
            type: 'success'
          })
          history.go(0);
        }).catch(function(error){
            console.log(error);
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
  },
};
</script>
<style scoped>
/* @import url(); 引入css类 */
.myorder_main{
    width: 100%;
}
.myorder_content{
    width: 80%;
    margin: auto;
    padding-bottom: 0.5%;
}
#search{
  width: 260px;
  padding: 0.5% 1% 0.5% 0.8%;
  font-size: 13px;
  outline: none;
  border: 1px solid #2a9b00;
  color: #000;
  border-radius: 3px 0 0 3px;
}
#search::placeholder{
		color:#999;
}
#btn{
  position: relative;
  left: -0.75%;
  width: 80px;
  padding:  0.4% 0.8%;
  font-size: 13px;
  border: 2px solid #2a9b00;
  background-color: #2a9b00;
  color: #fff;
  border-radius: 0 3px 3px 0;
  cursor: pointer;
}
.title{
  margin-top: 1.5%;
  text-align: center;
  padding: 1% 1%;
  background-color: #f1f1f1;
  border: 1px solid #2a9b00;
  border-radius: 2px;
}
.product_main{
  margin-top: 1.5%;
}
.data_orderNum{
  padding: 1% 1%;
  background-color: #f1f1f1;
  border: 1px solid #2a9b00;
  border-radius: 2px;
}
.productInfo{
  position: relative;
  background-color: #fff;
  border: 1px solid #2a9b00;
  border-radius: 2px;
  border-top: none;
}
.productInfoborder{
  padding: 1% 1%;
  border-top: 1px solid #2a9b00;
  border-right:  1px solid #2a9b00;
}
.productInfoborder:first-child{
  border-top: none;
}
.productImg img{
  width: 100%;
  height: auto;
  aspect-ratio: 1/1;
}
.productTitle{
  padding: 0 1%;
}
.Txtcenter{
  text-align: center;
  padding: 4.5% 0;
}
.amount_operation{
  position: absolute;
  top: 50%;
  right: 0;
  margin-top: -8px;
  text-align: center;
  padding-right: 1%;
}
.amount,
.operation{
  font-weight: bold;
}
.operation{
  padding-left: 2%;
}
.operation button{
  margin-top: -12px;
  width: 100%;
  padding: 5% 2.5%;
  background-color: #2a9b00;
  color: #fff;
  outline: none;
  border: 1px solid #2a9b00;
  border-radius: 2px;
  cursor: pointer;
}
.tuihuo_pingjia{
  margin-top: -35px;
}
.tuihuo_pingjia button:last-child{
  margin-top: 5%;
}
.prompt_text{
font-size: 22px;
font-weight: bold;
text-align: center;
padding-top: 15%;
}
.prompt_text button{
  background-color: #e23a3d;
  border: 1px solid #e23a3d;
  margin-top: 1.5%;
  outline: none;
  width: 7rem;
  padding: 0.5rem 0;
  border-radius: 2px;
  color: #fff;
  cursor: pointer;
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
  width: 150px;
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
@media only screen and (max-width: 767px) {
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
  .myorder_content{
    width: 96%;
    margin: 11.5% auto 0;
  }
  #search{
  width: 75%;
  padding: 2.5% 1.5%;
  font-size: 15px;
  border-radius: 5px 0 0 5px;
  }
  #btn{
    position: relative;
    left: -1.5%;
    width: 20%;
    padding:  2.2% 0.8%;
    font-size: 13px;
    border: 3px solid #2a9b00;
    border-radius: 0 5px 5px 0;
  }
  .product_main{
    margin-top: 3%;
  }
  .data_orderNum{
    padding: 1.5% 1%;
    font-size: 17px;
    border: none;
    border-bottom: 1px solid #2a9b00;
    background-color: #fff;
    border-radius: 0;
  }
  .productInfo{
    position: static;
    font-size: 18px;
    border: none;
    border-radius: 0;
    padding-bottom: 2%;
  }
  .productInfoborder{
    padding: 1.5% 1%;
    border-top: 1px solid #d2f1c7;
    border-right: none;
  }
  .Txtcenter{
    padding: 0;
  }
  .amount_operation{
    position: static;
    margin-top: 0px;
    text-align: right;
    padding-right: 0%;
  }
  .amount,
  .operation{
    padding: 2% 2%;
    border-top: 1px solid #addd9c;
  }
  .operation{
    padding-left: 0;
    border-left: none;
  }
  .operation button{
    margin-top: 0;
    width: 35%;
    padding: 2%;
    font-size: 16px;
    background-color: #2a9b00;
    color: #fff;
    outline: none;
    border: 1px solid #2a9b00;
    border-radius: 30px;
    cursor: pointer;
  }
  .tuihuo_pingjia{
    margin-top: -20px;
  }
  .operation .FKQH{
    margin: 2% 0 6%;
  }
}
</style>
