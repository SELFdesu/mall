<template>
  <div id="">

   <el-row id="status_bar">
      <el-col :span="5">
        <el-select @change="dataChange" v-model="salaryStatus" placeholder="状态">
          <el-option label="全部" value="all"></el-option>
          <el-option label="拒绝" value="0"></el-option>
          <el-option label="同意" value="1"></el-option>
          <el-option label="用户寄出" value="2"></el-option>
          <el-option label="平台签收" value="3"></el-option>
          <el-option label="退货完成" value="4"></el-option>
          <el-option label="未处理" value="5"></el-option>
        </el-select>
      </el-col>

      <el-col :span="5">
        <el-select @change="dataChange" v-model="salaryCause" placeholder="退货原因">
          <el-option label="全部" value="all"></el-option>
          <el-option label="做工问题" value="1"></el-option>
          <el-option label="质量问题" value="2"></el-option>
          <el-option label="配件问题" value="3"></el-option>
          <el-option label="商品破损" value="4"></el-option>
          <el-option label="未按时间发货" value="5"></el-option>
          <el-option label="发错货" value="6"></el-option>
          <el-option label="少件漏发" value="7"></el-option>
          <el-option label="快件丢失" value="8"></el-option>
          <el-option label="其他" value="9"></el-option>
          <el-option label="未发货商品申请退款" value="10"></el-option>
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


    <el-table :data="SalesReturnListData" height="530" border style="width: 100%">

        <el-table-column prop="id" label="id" width="80" ></el-table-column>
        <el-table-column prop="user.data.username" label="用户名"></el-table-column>
        <el-table-column prop="order_id" label="订单id"></el-table-column>
        <el-table-column prop="user.data.tel" label="手机号"></el-table-column>
        <el-table-column prop="cause_name" label="申请原因" ></el-table-column>
        <el-table-column prop="status_name" label="状态" ></el-table-column>
        <el-table-column prop="order.data.amount" label="退款金额" ></el-table-column>
        <el-table-column prop="created_at" label="申请时间" ></el-table-column>
        <el-table-column label="操作" >
          <template slot-scope="SalaryReturnListRow">
            <el-button @click="getSalaryReturnInfo(SalaryReturnListRow.row)" type="primary" size="small">详情</el-button>

            <el-popconfirm v-if="SalaryReturnListRow.row.status==5" @confirm="acceptReturn(SalaryReturnListRow.row,0)" title="确定要同意该用户的退货请求吗？">
              <el-button  slot="reference" type="danger" size="small" >拒绝</el-button>
            </el-popconfirm>

            <el-popconfirm v-if="SalaryReturnListRow.row.status==5" @confirm="acceptReturn(SalaryReturnListRow.row,1)" title="确定要同意该用户的退货请求吗？">
              <el-button  slot="reference" type="danger" size="small" >同意</el-button>
            </el-popconfirm>

            <el-popconfirm v-if="SalaryReturnListRow.row.status==2" @confirm="acceptReturn(SalaryReturnListRow.row,3)" title="确定要同意该用户的退货请求吗？">
              <el-button  slot="reference" type="danger" size="small" >签收</el-button>
            </el-popconfirm>

            <el-popconfirm v-if="SalaryReturnListRow.row.status==3" @confirm="acceptReturn(SalaryReturnListRow.row,4)" title="确定要同意该用户的退货请求吗？">
              <el-button  slot="reference" type="danger" size="small" >退款</el-button>
            </el-popconfirm>

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

    <el-dialog title="用户详情" v-if="salaryReturnInfoVisible" :visible.sync="salaryReturnInfoVisible">

       <el-row>
          <el-row>
             <div id="">
                 用户信息：
          </div>
          </el-row>
          <el-row  style="border: 1px dotted black; padding: 1rem;">
            <el-row>
              <el-col :span="4"><span>用户头像：</span></el-col>
              <el-col :span="20"><el-avatar :size="100" :src="salaryReturnInfo.user.data.avatar_url"></el-avatar></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>用户名：</span></el-col>
              <el-col :span="20"><div>{{salaryReturnInfo.user.data.username}}</div></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>邮箱：</span></el-col>
              <el-col :span="20"><div>{{salaryReturnInfo.user.data.email}}</div></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>电话：</span></el-col>
              <el-col :span="20"><div>{{salaryReturnInfo.user.data.tel}}</div></el-col>
            </el-row>
          </el-row>
        </el-row>


        <el-row>
          <el-row>
             <div id="">
                 退货信息：
          </div>
          </el-row>
          <el-row  style="border: 1px dotted black; padding: 1rem;">
            <el-row>
              <el-col :span="4"><span>申请原因：</span></el-col>
              <el-col :span="20"><div>{{salaryReturnInfo.cause_name}}</div></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>补充信息：</span></el-col>
              <el-col :span="20"><div>{{salaryReturnInfo.describe}}</div></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>处理状态：</span></el-col>
              <el-col :span="20"><div>{{salaryReturnInfo.status_name}}</div></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>退款类型：</span></el-col>
              <el-col :span="20"><div>{{salaryReturnInfo.type_name}}</div></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>快递类型：</span></el-col>
              <el-col :span="20"><div>{{salaryReturnInfo.express_type}}</div></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>快递单号：</span></el-col>
              <el-col :span="20"><div>{{salaryReturnInfo.express_no}}</div></el-col>
            </el-row>

            <el-row>
              <el-col :span="24"><span>补充图片：</span></el-col>
              <el-col :span="24" v-for="item in salaryReturnInfo.pics_url" :key="item">
                <div>
                  <img :src="item"  width="150px">
                </div>
              </el-col>
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
              <el-col :span="20"><div>{{salaryReturnInfo.order.data.id}}</div></el-col>
            </el-row>


            <el-row>
              <el-col :span="4"><span>订单状态：</span></el-col>
              <el-col :span="20"><div>{{salaryReturnInfo.order.data.status_name}}</div></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>付款方式：</span></el-col>
              <el-col :span="20"><div>{{salaryReturnInfo.order.data.mode_of_payment}}</div></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>支付单号：</span></el-col>
              <el-col :span="20"><div>{{salaryReturnInfo.order.data.payment_no}}</div></el-col>
            </el-row>

            <el-row>
               <el-col :span="4"><span>支付时间：</span></el-col>
               <el-col :span="20"><div>{{salaryReturnInfo.order.data.pay_time}}</div></el-col>
            </el-row>
            <el-row>
               <el-col :span="4"><span>快递类型：</span></el-col>
               <el-col :span="20"><div>{{salaryReturnInfo.order.data.express_type}}</div></el-col>
            </el-row>
            <el-row>
               <el-col :span="4"><span>快递单号：</span></el-col>
               <el-col :span="20"><div>{{salaryReturnInfo.order.data.express_no}}</div></el-col>
            </el-row>
            <el-row>
               <el-col :span="4"><span>自提点id：</span></el-col>
               <el-col :span="20"><div>{{salaryReturnInfo.order.data.package_deliver_id}}</div></el-col>
            </el-row>
            <el-row>
               <el-col :span="4"><span>自提点：</span></el-col>
               <el-col :span="20"><div>{{salaryReturnInfo.order.data.package_deliver_name}}</div></el-col>
            </el-row>
            <el-row>
               <el-col :span="4"><span>签收时间：</span></el-col>
               <el-col :span="20"><div>{{salaryReturnInfo.order.data.signfor_time}}</div></el-col>
            </el-row>
            <el-row>
               <el-col :span="6"><span>订单创建时间：</span></el-col>
               <el-col :span="18"><div>{{salaryReturnInfo.order.data.created_at}}</div></el-col>
            </el-row>
            <el-row>
               <el-col :span="4"><span>总金额：</span></el-col>
               <el-col :span="20"><div>{{salaryReturnInfo.order.data.amount}}</div></el-col>
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
        SalesReturnListData:[],
        salaryReturnInfo:[],
        salaryReturnInfoVisible:false,
        salaryStatus:'',
        salaryCause:'',
        time_range:null,
        paginationInfo:'',
        nowPage:1
      };
    },
    mounted(){
      var that=this;
      this.axios.get('https://grouponapi.top/api/admin/sales/return?include=user,order').then(res=>{

        that.SalesReturnListData=res.data.data
        that.paginationInfo=res.data.meta.pagination
      }).catch(function(error){
        that.$message({
          message: error.response.data.message,
          type:'error'
        });
      })
    },
    methods:{

      //退货详情
      getSalaryReturnInfo(row){
        var that=this;
        this.axios.get('https://grouponapi.top/api/admin/sales/return/'+row.id+'?include=user,order.orderDetail.product').then(res=>{

            that.salaryReturnInfo=res.data.data;
            this.salaryReturnInfoVisible=true


        }).catch(function(error){
          that.$message({
            message: error.response.data.message,
            type:'error'
          });
        })
      },

      //退货
      acceptReturn(row,status){
        var that=this;
        this.axios.patch('https://grouponapi.top/api/admin/sales/return/'+row.id+'/status',{
          status:status
        }).then(res=>{
          that.$message({
            message: '操作成功！',
            type:'success'
          });

          row.status=status;

          if(status==0){

              row.status_name='拒绝退货'

          }else if(status==1){

              row.status_name='同意退货'

          }else if(status==3){

              row.status_name='平台签收'

          }else if(status==4){

              row.status_name='退货完成'

          }

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
        let status=this.salaryStatus=='all'||this.salaryStatus==''?'':'status='+this.salaryStatus+'&';
        let salaryCause=this.salaryCause=='all'||this.salaryCause==''?'':'cause='+this.salaryCause+'&';

        let time_begin=this.time_range==null?'':'time_begin='+this.time_range[0]+'&';
        let time_end=this.time_range==null?'':'time_end='+this.time_range[1]+'&';

        let page=this.nowPage==0?'':'page='+this.nowPage+'&'


        this.axios.get('https://grouponapi.top/api/admin/sales/return?include=user,order&'+status+salaryCause+time_begin+time_end+page).then(res=>{
          that.SalesReturnListData=res.data.data;
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
