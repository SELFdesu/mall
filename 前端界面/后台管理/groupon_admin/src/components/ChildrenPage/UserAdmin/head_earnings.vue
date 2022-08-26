<template>
  <div id="">

   <el-row id="status_bar">
      <el-col :span="5">
        <el-select @change="dataChange" v-model="salaryStatus" placeholder="处理状态">
          <el-option label="全部" value="all"></el-option>
          <el-option label="未处理" value="0"></el-option>
          <el-option label="已处理" value="1"></el-option>
        </el-select>
      </el-col>
    </el-row>


    <el-table :data="HeadEarningsData" height="530" border style="width: 100%">

        <el-table-column prop="id" label="id" width="80" ></el-table-column>
        <el-table-column prop="headInfo.data.name" label="姓名" width="80" ></el-table-column>
        <el-table-column prop="payment_method" label="打款方式"></el-table-column>
        <el-table-column prop="payment_account" label="打款账号"></el-table-column>
        <el-table-column prop="salary" label="上月收益"></el-table-column>
        <el-table-column prop="status" label="状态" ></el-table-column>
        <el-table-column width="220" label="操作" >
          <template slot-scope="salaryListRow">
            <el-button @click="getHeadInfo(salaryListRow.row)" type="primary" size="small">详情</el-button>

            <el-button @click="getPayroll(salaryListRow.row)" type="primary" size="small">工资条</el-button>

            <el-popconfirm v-if="salaryListRow.row.status=='未处理'" @confirm="settleAccounts(salaryListRow.row)" title="确定要结算该团长收益吗？">
              <el-button slot="reference" type="danger" size="small" >结算</el-button>
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

    <el-dialog title="详情" v-if="HeadInfoVisible" :visible.sync="HeadInfoVisible">
        <el-row>
          <el-row>
             <div id="">
                 用户信息：
             </div>
          </el-row>
          <el-row  style="border: 1px dotted black; padding: 1rem;">
            <el-row>
              <el-col :span="4"><span>头像：</span></el-col>
             <el-col :span="20"><el-avatar :size="100" :src="HeadInfo.user.data.avatar_url"></el-avatar></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>用户id：</span></el-col>
              <el-col :span="20"><div>{{HeadInfo.user.data.id}}</div></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>用户名：</span></el-col>
              <el-col :span="20"><div>{{HeadInfo.user.data.username}}</div></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>角色：</span></el-col>
              <el-col :span="20"><div>{{HeadInfo.user.data.userRole}}</div></el-col>
            </el-row>

            <el-row>
               <el-col :span="4"><span>邮箱：</span></el-col>
               <el-col :span="20"><div>{{HeadInfo.user.data.email}}</div></el-col>
            </el-row>
            <el-row>
               <el-col :span="4"><span>手机号：</span></el-col>
               <el-col :span="20"><div>{{HeadInfo.user.data.tel}}</div></el-col>
            </el-row>
            <el-row>
               <el-col :span="4"><span>性别：</span></el-col>
               <el-col :span="20"><div>{{HeadInfo.user.data.sex}}</div></el-col>
            </el-row>
            <el-row>
               <el-col :span="4"><span>账号状态：</span></el-col>
               <el-col :span="20"><div>{{HeadInfo.user.data.locked==0?'正常':'冻结'}}</div></el-col>
            </el-row>
          </el-row>
        </el-row>

        <el-row>
          <el-row>
             <div id="">
                 团长信息：
             </div>
          </el-row>
          <el-row  style="border: 1px dotted black; padding: 1rem;">

            <el-row>
              <el-col :span="4"><span>团长id：</span></el-col>
              <el-col :span="20"><div>{{HeadInfo.id}}</div></el-col>
            </el-row>
            <el-row>
              <el-col :span="4"><span>姓名：</span></el-col>
              <el-col :span="20"><div>{{HeadInfo.name}}</div></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>身份：</span></el-col>
              <el-col :span="20"><div>{{HeadInfo.identity_name}}</div></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>手机号：</span></el-col>
              <el-col :span="20"><div>{{HeadInfo.tel}}</div></el-col>
            </el-row>

            <el-row>
               <el-col :span="4"><span>身份证号：</span></el-col>
               <el-col :span="20"><div>{{HeadInfo.id_number}}</div></el-col>
            </el-row>

            <el-row>
               <el-col :span="4"><span>状态：</span></el-col>
               <el-col :span="20"><div>{{HeadInfo.status==0?'离职':'在职'}}</div></el-col>
            </el-row>
          </el-row>
        </el-row>

        <el-row>
          <el-row>
             <div id="">
                 自提点信息：
             </div>
          </el-row>
          <el-row  style="border: 1px dotted black; padding: 1rem;">
            <el-row>
              <el-col :span="4"><span>店铺图片：</span></el-col>
             <el-col :span="20"><el-avatar :size="100" :src="HeadInfo.packageDeliver.data.pic_url"></el-avatar></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>自提点id：</span></el-col>
              <el-col :span="20"><div>{{HeadInfo.packageDeliver.data.id}}</div></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>自提点名：</span></el-col>
              <el-col :span="20"><div>{{HeadInfo.packageDeliver.data.store_name}}</div></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>地区：</span></el-col>
              <el-col :span="20"><div>{{HeadInfo.packageDeliver.data.address_name}}</div></el-col>
            </el-row>

            <el-row>
               <el-col :span="4"><span>详细地址：</span></el-col>
               <el-col :span="20"><div>{{HeadInfo.packageDeliver.data.address_info}}</div></el-col>
            </el-row>

            <el-row>
               <el-col :span="4"><span>状态：</span></el-col>
               <el-col :span="20"><div>{{HeadInfo.status==0?'闭店':'正常'}}</div></el-col>
            </el-row>
          </el-row>
        </el-row>

    </el-dialog>


    <el-dialog title="工资条" v-if="payrollVisible" :visible.sync="payrollVisible">
      <el-table :data="payrollData" height="400" border style="width: 100%">

          <el-table-column prop="id" label="id" width="80" ></el-table-column>
          <el-table-column prop="payment_method" label="打款方式"></el-table-column>
          <el-table-column prop="payment_account" label="打款账号"></el-table-column>
          <el-table-column prop="salary" label="收益"></el-table-column>
          <el-table-column prop="date" label="日期"></el-table-column>
          <el-table-column prop="status" label="状态" ></el-table-column>
          <el-table-column label="操作" >
            <template slot-scope="payrollListRow">
              <el-popconfirm v-if="payrollListRow.row.status=='未处理'" @confirm="changePayroll(payrollListRow.row)" title="确定要结算该团长收益吗？">
                <el-button slot="reference" type="danger" size="small" >结算</el-button>
              </el-popconfirm>

            </template>
          </el-table-column>

      </el-table>
      <!-- 工资条分页 -->
      <el-pagination
            id="pagination"
            @current-change="payrollPageChange"
            :page-size="payrollPagination.per_page"
            layout="prev, pager, next, jumper"
            :total="payrollPagination.total">
      </el-pagination>
    </el-dialog>

  </div>
</template>

<script>
  export default {
    name:"HeadEarnings",
    data(){
      return {
        HeadEarningsData:[],
        HeadInfo:[],
        HeadInfoVisible:false,
        payrollVisible:false,
        salaryStatus:'',
        paginationInfo:'',
        nowPage:1,

        nowSalaryPayroll:'',
        payrollData:[],
        payrollPagination:[],
      };
    },
    mounted(){
      var that=this;
      this.axios.get('https://grouponapi.top/api/admin/head/salary/list?include=headInfo').then(res=>{
        that.HeadEarningsData=res.data.data
        that.paginationInfo=res.data.meta.pagination
      }).catch(function(error){
        that.$message({
          message: error.response.data.message,
          type:'error'
        });
      })
    },

    methods:{

      //团长详情
      getHeadInfo(row){
        var that=this;
        this.axios.get('https://grouponapi.top/api/admin/head/headinfo/'+row.head_id+'?include=user,packageDeliver').then(res=>{
            that.HeadInfo=res.data.data;
            this.HeadInfoVisible=true
        }).catch(function(error){
          that.$message({
            message: error.response.data.message,
            type:'error'
          });
        })
      },

      //获得工资条
      getPayroll(row){
        var that=this;
        this.axios.get('https://grouponapi.top/api/admin/head/payroll/'+row.id+'/list').then(res=>{
          that.payrollData=res.data.data;
          that.payrollPagination=res.data.meta.pagination;
          that.payrollVisible=true;
          that.nowSalaryPayroll=row.id;

        }).catch(function(error){
          that.$message({
            message: error.response.data.message,
            type:'error'
          });
        })
      },

      //结算收益
      settleAccounts(row){
        var that=this;
        this.axios.patch('https://grouponapi.top/api/admin/head/salary/'+row.id+'/settlement').then(res=>{
          that.$message({
            message: '操作成功！',
            type:'success'
          });
          row.status='已处理';
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

        let page=this.nowPage==0?'':'page='+this.nowPage+'&'

        this.axios.get('https://grouponapi.top/api/admin/head/salary/list?include=headInfo&'+status+page).then(res=>{
          that.HeadEarningsData=res.data.data;
          that.paginationInfo=res.data.meta.pagination;
        }).catch(function(error){
          that.$message({
            message: error.response.data.message,
            type:'error'
          });
        })

      },

      //改变工资条状态
      changePayroll(row){
        var that=this;
        this.axios.patch('https://grouponapi.top/api/admin/head/payroll/'+row.id+'/change').then(res=>{
          that.$message({
            message: "修改成功!",
            type:'success'
          });
        }).catch(function(error){
          that.$message({
            message: error.response.data.message,
            type:'error'
          });
        })
      },

      // 工资条翻页
      payrollPageChange(current_page){
        var that=this;
        this.axios.get('https://grouponapi.top/api/admin/head/payroll/'+this.nowSalaryPayroll+'/list?page='+current_page).then(res=>{
          that.payrollData=res.data.data;
          that.payrollPagination=res.data.meta.pagination;
        }).catch(function(error){
          that.$message({
            message: error.response.data.message,
            type:'error'
          });
        })
      }


    }
  }
</script>

<style>
  #status_bar{
    margin-bottom: 1rem;
  }
  #pagination{
    text-align: center;
    margin-top: 0.2rem;
  }
</style>
