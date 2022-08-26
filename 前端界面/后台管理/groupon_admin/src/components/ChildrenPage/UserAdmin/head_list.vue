<template>
  <div id="">
    <el-row id="status_bar">


      <el-col :span="7">

         <el-input placeholder="请输入内容" v-model="HeadSerch" class="input-with-select">

           <el-select v-model="SerchType" slot="prepend" placeholder="请选择" id="serchSelect">
             <el-option label="团长id" value="head_id"></el-option>
             <el-option label="用户id" value="user_id"></el-option>
             <el-option label="姓名" value="name"></el-option>
             <el-option label="电话" value="tel"></el-option>
           </el-select>

           <el-button slot="append" icon="el-icon-search" @click="HeadSerchClick"></el-button>

         </el-input>

      </el-col>

       <el-col :span="3" :offset="1">
         <el-select @change="dataChange" v-model="headStatus" placeholder="状态">
           <el-option label="全部" value="all"></el-option>
           <el-option label="在职" value="1"></el-option>
           <el-option label="离职" value="0"></el-option>
         </el-select>
       </el-col>

       <el-col :span="10" :offset="1">
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

    <el-table :data="HeadListData" height="530" border style="width: 100%">

       <el-table-column prop="id" label="id" width="80" ></el-table-column>
        <el-table-column prop="name" label="姓名"></el-table-column>
        <el-table-column prop="identity_name" label="身份"></el-table-column>
        <el-table-column prop="tel" label="手机号"></el-table-column>
        <el-table-column prop="status" label="状态" ></el-table-column>
        <el-table-column label="操作" >
          <template slot-scope="HeadListRow">
            <el-button @click="getHeadInfo(HeadListRow.row)" type="primary" size="small">详情</el-button>

            <el-popconfirm v-if="HeadListRow.row.status==1" @confirm="HeadLocked(HeadListRow.row)" title="确定要撤销该用户团长身份吗？">
              <el-button slot="reference" type="danger" size="small" >撤职</el-button>
            </el-popconfirm>

            <el-popconfirm v-else @confirm="HeadLocked(HeadListRow.row)" title="确定要恢复该用户团长身份吗？">
              <el-button slot="reference" type="danger" size="small" >恢复</el-button>
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
          @current-change="HeadSerchClick('page')"
          :current-page.sync="nowPage"
          :page-size="paginationInfo.per_page"
          layout="prev, pager, next, jumper"
          :total="paginationInfo.total">
    </el-pagination>


   <el-dialog title="团长详情" v-if="HeadInfoVisible" :visible.sync="HeadInfoVisible">

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

  </div>
</template>

<script>
  export default {
    name:"HeadList",
    data(){
      return {
        HeadListData:[],
        HeadInfo:[],
        HeadInfoVisible:false,
        headStatus:'',
        time_range:null,
        paginationInfo:'',
        nowPage:1,

        SerchType:'',
        HeadSerch:'',

        serchSwitch:false
      };
    },
    mounted(){
      var that=this;
      this.axios.get('https://grouponapi.top/api/admin/head/headlist').then(res=>{
        that.HeadListData=res.data.data
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
        this.axios.get('https://grouponapi.top/api/admin/head/headinfo/'+row.id+'?include=user,packageDeliver').then(res=>{
            that.HeadInfo=res.data.data;
            this.HeadInfoVisible=true
        }).catch(function(error){
          that.$message({
            message: error.response.data.message,
            type:'error'
          });
        })
      },

      //修改团长状态
      HeadLocked(row){

        var that=this;
        if(row.status){
          this.axios.patch('https://grouponapi.top/api/admin/head/'+row.userId+'/takeback').then(res=>{
            that.$message({
              message: '操作成功！',
              type:'success'
            });
            row.status=row.status==0?1:0;
          }).catch(function(error){
            that.$message({
              message: error.response.data.message,
              type:'error'
            });
          })
        }else{
          this.axios.patch('https://grouponapi.top/api/admin/head/'+row.userId+'/recover').then(res=>{
            that.$message({
              message: '操作成功！',
              type:'success'
            });
            row.status=row.status==0?1:0;
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
        let status=this.headStatus=='all'||this.headStatus==''?'':'status='+this.headStatus+'&';

        let time_begin=this.time_range==null?'':'time_begin='+this.time_range[0]+'&';
        let time_end=this.time_range==null?'':'time_end='+this.time_range[1]+'&';

        let page=this.nowPage==0?'':'page='+this.nowPage+'&'

        this.axios.get('https://grouponapi.top/api/admin/head/headlist?'+status+time_begin+time_end+page).then(res=>{
          that.HeadListData=res.data.data
          that.paginationInfo=res.data.meta.pagination
        }).catch(function(error){
          that.$message({
            message: error.response.data.message,
            type:'error'
          });
        })

      },

      HeadSerchClick(type){

        if(this.SerchType==''){
          this.$message({
            message: "搜索类型不能为空",
            type:'error'
          });
          return 0;
        }else if(this.HeadSerch==''){
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

        this.headStatus='';
        this.time_range=null;

        let page=this.nowPage==0?'':'page='+this.nowPage+'&'

        var that=this;
        this.axios.get('https://grouponapi.top/api/admin/head/serch/head?type='+this.SerchType+'&number='+this.HeadSerch+'&'+page).then(res=>{
          that.HeadListData=res.data.data
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
  #pagination{
    text-align: center;
    margin-top: 0.2rem;
  }
  #serchSelect {
    width: 90px;
  }
</style>
