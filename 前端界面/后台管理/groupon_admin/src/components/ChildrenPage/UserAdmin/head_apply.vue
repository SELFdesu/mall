<template>
  <div id="">

   <el-row id="status_bar">
     <el-col :span="7">

        <el-input placeholder="请输入内容" v-model="HeadApplySerch" class="input-with-select">

          <el-select v-model="SerchType" slot="prepend" placeholder="请选择" id="serchSelect">
            <el-option label="用户id" value="user_id"></el-option>
            <el-option label="申请id" value="apply_id"></el-option>
          </el-select>

          <el-button slot="append" icon="el-icon-search" @click="applySerch"></el-button>

        </el-input>

     </el-col>

      <el-col :span="3" :offset="1">
        <el-select @change="dataChange" v-model="HeadApplyStatus" placeholder="状态">
          <el-option label="全部" value="all"></el-option>
          <el-option label="未通过" value="0"></el-option>
          <el-option label="通过" value="1"></el-option>
          <el-option label="待处理" value="2"></el-option>
        </el-select>
      </el-col>

      <el-col :span="10"  :offset="1">
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


    <el-table :data="ApplyHeadListData" height="530" border style="width: 100%">

        <el-table-column prop="id" label="id" width="80" ></el-table-column>
        <el-table-column prop="user.data.username" label="用户名"></el-table-column>
        <el-table-column prop="user_id" label="用户id" width="80" ></el-table-column>
        <el-table-column prop="apply_role_name" label="申请权限"></el-table-column>
        <el-table-column prop="status_name" label="申请状态"></el-table-column>
        <el-table-column prop="created_at" label="申请时间"></el-table-column>

        <el-table-column label="操作" >
          <template slot-scope="headApplyListRow">
            <el-button @click="getHeadApplyInfo(headApplyListRow.row)" type="primary" size="small">详情</el-button>

            <el-popconfirm v-if="headApplyListRow.row.status==2" @confirm="disposeApply(headApplyListRow.row,1)" title="确认要同意该用户成为团长吗？">
              <el-button slot="reference" type="success" size="small" >同意</el-button>
            </el-popconfirm>

            <el-popconfirm v-if="headApplyListRow.row.status==2" @confirm="disposeApply(headApplyListRow.row,0)" title="确认要拒绝该用户成为团长吗？">
              <el-button slot="reference" type="danger" size="small" >拒绝</el-button>
            </el-popconfirm>

          </template>
        </el-table-column>

    </el-table>

    <!-- 普通分页 -->
    <el-pagination
          v-if="!serchSwitch"
          id="pagination"
          @current-change="dataChange('page')"
          :current-page.sync="nowPage"
          :page-size="paginationInfo.per_page"
          layout="prev, pager, next, jumper"
          :total="paginationInfo.total">
    </el-pagination>

    <!-- 搜索分页 -->
    <el-pagination
          v-else
          id="pagination"
          @current-change="applySerch('page')"
          :current-page.sync="nowPage"
          :page-size="paginationInfo.per_page"
          layout="prev, pager, next, jumper"
          :total="paginationInfo.total">
    </el-pagination>

    <el-dialog title="申请详细信息" v-if="ApplyHeadInfoVisible" :visible.sync="ApplyHeadInfoVisible">
        <el-row>
          <el-col :span="4"><span>店铺图片：</span></el-col>
         <el-col :span="20"><el-avatar :size="100" :src="ApplyHeadInfo.pic_url"></el-avatar></el-col>
        </el-row>

        <el-row>
          <el-col :span="4"><span>店铺名：</span></el-col>
          <el-col :span="20"><div>{{ApplyHeadInfo.store_name}}</div></el-col>
        </el-row>

        <el-row>
          <el-col :span="4"><span>申请人：</span></el-col>
          <el-col :span="20"><div>{{ApplyHeadInfo.applicant}}</div></el-col>
        </el-row>

        <el-row>
          <el-col :span="4"><span>身份：</span></el-col>
          <el-col :span="20"><div>{{ApplyHeadInfo.identity_name}}</div></el-col>
        </el-row>

        <el-row>
          <el-col :span="4"><span>联系电话：</span></el-col>
          <el-col :span="20"><div>{{ApplyHeadInfo.tel}}</div></el-col>
        </el-row>

        <el-row>
          <el-col :span="4"><span>身份证号：</span></el-col>
          <el-col :span="20"><div>{{ApplyHeadInfo.id_number}}</div></el-col>
        </el-row>

        <el-row>
           <el-col :span="4"><span>地区：</span></el-col>
           <el-col :span="20"><div>{{ApplyHeadInfo.address_name}}</div></el-col>
        </el-row>

        <el-row>
           <el-col :span="4"><span>详细地址：</span></el-col>
           <el-col :span="20"><div>{{ApplyHeadInfo.store_address}}</div></el-col>
        </el-row>

        <el-row>
           <el-col :span="4"><span>申请时间：</span></el-col>
           <el-col :span="20"><div>{{ApplyHeadInfo.created_at}}</div></el-col>
        </el-row>

    </el-dialog>

  </div>
</template>

<script>
  export default {
    name:"UserList",
    data(){
      return {
        ApplyHeadListData:[],
        ApplyHeadInfo:[],
        ApplyHeadInfoVisible:false,
        HeadApplyStatus:'',
        time_range:null,
        paginationInfo:'',
        nowPage:1,

        HeadApplySerch:'',
        SerchType:'',
        serchSwitch:false
      };
    },
    mounted(){
      var that=this;
      this.axios.get('https://grouponapi.top/api/admin/head/applylist?include=user').then(res=>{

        that.ApplyHeadListData=res.data.data
        that.paginationInfo=res.data.meta.pagination
      }).catch(function(error){
        that.$message({
          message: error.response.data.message,
          type:'error'
        });
      })
    },
    methods:{

      //团长申请详情信息
      getHeadApplyInfo(row){
        var that=this;
        this.axios.get('https://grouponapi.top/api/admin/head/applyinfo/'+row.info_id).then(res=>{
            that.ApplyHeadInfo=res.data.data;
            that.ApplyHeadInfoVisible=true;

        }).catch(function(error){
          that.$message({
            message: error.response.data.message,
            type:'error'
          });
        })

      },

      //同意或拒绝团长申请
      disposeApply(row,status){
        var that=this;
        if(status){
          this.axios.patch('https://grouponapi.top/api/admin/head/'+row.id+'/addhead').then(res=>{
            that.$message({
              message: '操作成功！',
              type:'success'
            });
            row.status_name='申请通过';
            row.status=1;
          }).catch(function(error){
            that.$message({
              message: error.response.data.message,
              type:'error'
            });
          })
        }else{

          this.axios.patch('https://grouponapi.top/api/admin/head/'+row.id+'/refusehead').then(res=>{
            that.$message({
              message: '操作成功！',
              type:'success'
            });
            row.status_name='申请不通过';
            row.status=0;
          }).catch(function(error){
            that.$message({
              message: error.response.data.message,
              type:'error'
            });
          })
        }

      },

      //状态改变
      dataChange(type){

        this.serchSwitch=false;

        if(type!='page'){
          this.nowPage=1
        }

        var that=this;
        let status=this.HeadApplyStatus=='all'||this.HeadApplyStatus==''?'':'status='+this.HeadApplyStatus+'&';

        let time_begin=this.time_range==null?'':'time_begin='+this.time_range[0]+'&';
        let time_end=this.time_range==null?'':'time_end='+this.time_range[1]+'&';

        let page=this.nowPage==0?'':'page='+this.nowPage+'&'



        this.axios.get('https://grouponapi.top/api/admin/head/applylist?include=user&'+status+time_begin+time_end+page).then(res=>{
          that.ApplyHeadListData=res.data.data
          that.paginationInfo=res.data.meta.pagination
        }).catch(function(error){
          that.$message({
            message: error.response.data.message,
            type:'error'
          });
        })

      },


      applySerch(type){

        if(this.SerchType==''){
          this.$message({
            message: "搜索类型不能为空",
            type:'error'
          });
          return 0;
        }else if(this.HeadApplySerch==''){
          this.$message({
            message: "搜索内容不能为空",
            type:'error'
          });
          return 0;
        }

        if(type!='page'){
          this.nowPage=1
        }

        this.serchSwitch=true;

        let page=this.nowPage==0?'':'page='+this.nowPage+'&'

        var that=this;
        this.HeadApplyStatus='';
        this.time_range=null;

        this.axios.get('https://grouponapi.top/api/admin/head/serch/apply?include=user&type='+this.SerchType+'&number='+this.HeadApplySerch+'&'+page).then(res=>{
          that.ApplyHeadListData=res.data.data
          that.paginationInfo=res.data.meta.pagination
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
  #serchSelect {
    width: 90px;
  }
</style>
