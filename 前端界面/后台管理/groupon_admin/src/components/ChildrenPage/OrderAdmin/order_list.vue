<template>
  <div id="">

   <el-row id="status_bar">
      <el-col :span="5">
        <el-select @change="dataChange" v-model="expressType" placeholder="快递类型">
          <el-option label="全部" value="all"></el-option>
          <el-option label="顺丰" value="SF"></el-option>
          <el-option label="圆通" value="YT"></el-option>
          <el-option label="韵达" value="YD"></el-option>
        </el-select>
      </el-col>

      <el-col :span="5">
        <el-select @change="dataChange" v-model="orderStatus" placeholder="订单状态">
          <el-option label="全部" value="all"></el-option>
          <el-option label="已退货" value="0"></el-option>
          <el-option label="下单" value="1"></el-option>
          <el-option label="支付" value="2"></el-option>
          <el-option label="发货" value="3"></el-option>
          <el-option label="到达自提点" value="4"></el-option>
          <el-option label="用户收货" value="5"></el-option>
          <el-option label="已评论" value="6"></el-option>
          <el-option label="失效订单" value="99"></el-option>
        </el-select>
      </el-col>

      <el-col :span="10">
        <el-date-picker
          @change="dataChange"
          v-model="time_range"
          type="daterange"
          value-format="yyyy-MM-dd HH:mm:ss"
          start-placeholder="开始日期"
          end-placeholder="结束日期">
        </el-date-picker>
      </el-col>
    </el-row>


    <el-table :data="OrderListData" height="530" border style="width: 100%">

        <el-table-column prop="id" label="id" width="80" ></el-table-column>
        <el-table-column prop="user.data.username" label="所属用户" width="80" ></el-table-column>
        <el-table-column prop="package_deliver_name" label="自提点" width="80" ></el-table-column>
        <el-table-column prop="mode_of_payment" label="支付方式"></el-table-column>
        <el-table-column prop="payment_no" label="支付单号"></el-table-column>
        <el-table-column prop="amount" label="总金额" ></el-table-column>
         <el-table-column prop="status_name" label="状态" ></el-table-column>
        <el-table-column prop="created_at" label="创建时间" ></el-table-column>
        <el-table-column label="操作" >

          <template slot-scope="OrderListRow">
            <el-button @click="getOrderInfo(OrderListRow.row)" type="primary" size="small">详情</el-button>
          </template>

        </el-table-column>

    </el-table>

    <!-- 分页 -->
    <el-pagination
          id="pagination"
          @current-change="dataChange('page')"
          :current-page.sync="nowPage"
          :page-size="paginationInfo.per_page"
          layout="prev, pager, next, jumper"
          :total="paginationInfo.total">
    </el-pagination>

    <el-dialog title="订单详情" v-if="OrderInfoVisible" :visible.sync="OrderInfoVisible">

        <el-row>
          <el-row>
             <div id="">
                 用户信息：
             </div>
          </el-row>
          <el-row  style="border: 1px dotted black; padding: 1rem;">
            <el-row>
              <el-col :span="4"><span>头像：</span></el-col>
             <el-col :span="20"><el-avatar :size="100" :src="OrderInfo[0].order.data.user.data.avatar_url"></el-avatar></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>用户id：</span></el-col>
              <el-col :span="20"><div>{{OrderInfo[0].order.data.user.data.id}}</div></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>用户名：</span></el-col>
              <el-col :span="20"><div>{{OrderInfo[0].order.data.user.data.username}}</div></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>角色：</span></el-col>
              <el-col :span="20"><div>{{OrderInfo[0].order.data.user.data.userRole}}</div></el-col>
            </el-row>

            <el-row>
               <el-col :span="4"><span>邮箱：</span></el-col>
               <el-col :span="20"><div>{{OrderInfo[0].order.data.user.data.email}}</div></el-col>
            </el-row>
            <el-row>
               <el-col :span="4"><span>手机号：</span></el-col>
               <el-col :span="20"><div>{{OrderInfo[0].order.data.user.data.tel}}</div></el-col>
            </el-row>
            <el-row>
               <el-col :span="4"><span>性别：</span></el-col>
               <el-col :span="20"><div>{{OrderInfo[0].order.data.user.data.sex}}</div></el-col>
            </el-row>
            <el-row>
               <el-col :span="4"><span>账号状态：</span></el-col>
               <el-col :span="20"><div>{{OrderInfo[0].order.data.user.data.locked==0?'正常':'冻结'}}</div></el-col>
            </el-row>
          </el-row>
        </el-row>


        <el-row>
          <el-row>
             <div id="">
                 订单详情：
             </div>
          </el-row>
          <el-row  style="border: 1px dotted black; padding: 1rem;">
            <el-row>
              <el-col :span="4"><span>订单id：</span></el-col>
              <el-col :span="20"><div>{{OrderInfo[0].order.data.id}}</div></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>用户id：</span></el-col>
              <el-col :span="20"><div>{{OrderInfo[0].order.data.user_id}}</div></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>订单状态：</span></el-col>
              <el-col :span="20"><div>{{OrderInfo[0].order.data.status_name}}</div></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>付款方式：</span></el-col>
              <el-col :span="20"><div>{{OrderInfo[0].order.data.mode_of_payment}}</div></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>支付单号：</span></el-col>
              <el-col :span="20"><div>{{OrderInfo[0].order.data.payment_no}}</div></el-col>
            </el-row>

            <el-row>
               <el-col :span="4"><span>支付时间：</span></el-col>
               <el-col :span="20"><div>{{OrderInfo[0].order.data.pay_time}}</div></el-col>
            </el-row>
            <el-row>
               <el-col :span="4"><span>快递类型：</span></el-col>
               <el-col :span="20"><div>{{OrderInfo[0].order.data.express_type}}</div></el-col>
            </el-row>
            <el-row>
               <el-col :span="4"><span>快递单号：</span></el-col>
               <el-col :span="20"><div>{{OrderInfo[0].order.data.express_no}}</div></el-col>
            </el-row>
            <el-row>
               <el-col :span="4"><span>自提点id：</span></el-col>
               <el-col :span="20"><div>{{OrderInfo[0].order.data.package_deliver_id}}</div></el-col>
            </el-row>
            <el-row>
               <el-col :span="4"><span>自提点：</span></el-col>
               <el-col :span="20"><div>{{OrderInfo[0].order.data.package_deliver_name}}</div></el-col>
            </el-row>
            <el-row>
               <el-col :span="4"><span>签收时间：</span></el-col>
               <el-col :span="20"><div>{{OrderInfo[0].order.data.signfor_time}}</div></el-col>
            </el-row>
            <el-row>
               <el-col :span="6"><span>订单创建时间：</span></el-col>
               <el-col :span="18"><div>{{OrderInfo[0].order.data.created_at}}</div></el-col>
            </el-row>
            <el-row>
               <el-col :span="4"><span>总金额：</span></el-col>
               <el-col :span="20"><div>{{OrderInfo[0].order.data.amount}}</div></el-col>
            </el-row>
          </el-row>
        </el-row>





        <el-row>
          <el-row>
             <div id="">
                 订单商品：
             </div>
          </el-row>
          <el-row v-for="item in OrderInfo" :key="item.id"  style="border: 1px dotted black; padding: 1rem;">
            <el-row>
              <el-col :span="4"><span>商品图：</span></el-col>
              <el-col :span="20"><img :src="item.product.data.picture_url" width="100px" ></el-col>
            </el-row>
            <el-row>
              <el-col :span="4"><span>商品名：</span></el-col>
              <el-col :span="20"><div>{{item.product.data.title}}</div></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>分类：</span></el-col>
              <el-col :span="20"><div>{{item.product.data.category_name}}</div></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>购买数量：</span></el-col>
              <el-col :span="20"><div>{{item.quantity}}</div></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>购买单价：</span></el-col>
              <el-col :span="20"><div>{{item.unit_price}}</div></el-col>
            </el-row>

            <el-row>
               <el-col :span="4"><span>购买总额：</span></el-col>
               <el-col :span="20"><div>{{item.total_amount}}</div></el-col>
            </el-row>

          </el-row>
        </el-row>


    </el-dialog>

  </div>
</template>

<script>
  export default {
    name:"UserList",
    data(){
      return {
        OrderListData:[],
        OrderInfo:[],
        OrderInfoVisible:false,
        expressType:'',
        orderStatus:'',
        time_range:null,
        paginationInfo:'',
        nowPage:1
      };
    },
    mounted(){
      var that=this;
      this.axios.get('https://grouponapi.top/api/admin/order?include=user').then(res=>{

        that.OrderListData=res.data.data
        that.paginationInfo=res.data.meta.pagination
      }).catch(function(error){
        that.$message({
          message: error.response.data.message,
          type:'error'
        });
      })
    },
    methods:{

      //订单详情
      getOrderInfo(row){
        var that=this;
        this.axios.get('https://grouponapi.top/api/admin/order/'+row.id+'?include=order.user,product').then(res=>{

            that.OrderInfo=res.data.data;

            this.OrderInfoVisible=true

        }).catch(function(error){
          that.$message({
            message: error.response.data.message,
            type:'error'
          });
        })
      },


      //状态改变
      dataChange(type){

        if(type!='page'){
          this.nowPage=1
        }

        var that=this;
        let express_type=this.expressType=='all'||this.expressType==''?'':'express_type='+this.expressType+'&';
        let status=this.orderStatus=='all'||this.orderStatus==''?'':'status='+this.orderStatus+'&';

        let time_begin=this.time_range==null?'':'time_begin='+this.time_range[0]+'&';
        let time_end=this.time_range==null?'':'time_end='+this.time_range[1]+'&';

        let page=this.nowPage==0?'':'page='+this.nowPage+'&'


        this.axios.get('https://grouponapi.top/api/admin/order?include=user&'+express_type+status+time_begin+time_end+page).then(res=>{
          that.OrderListData=res.data.data;
          that.paginationInfo=res.data.meta.pagination;
        }).catch(function(error){
          that.$message({
            message: error.response.data.message,
            type:'error'
          });
        })

      },


    }
  }
</script>

<style>

  #pagination{
    text-align: center;
    margin-top: 0.2rem;
  }
</style>
