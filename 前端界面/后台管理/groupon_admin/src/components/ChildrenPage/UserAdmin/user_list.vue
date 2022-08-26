<template>
  <div id="">

   <el-row id="status_bar">
      <el-col :span="5">
        <el-select @change="dataChange" v-model="userStatus" placeholder="用户状态">
          <el-option label="全部" value="all"></el-option>
          <el-option label="正常" value="0"></el-option>
          <el-option label="锁定" value="1"></el-option>
        </el-select>
      </el-col>

      <el-col :span="5">
        <el-select @change="dataChange" v-model="userSex" placeholder="性别">
          <el-option label="全部" value="all"></el-option>
          <el-option label="男" value="0"></el-option>
          <el-option label="女" value="1"></el-option>
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


    <el-table :data="UserListData" height="530" border style="width: 100%">

        <el-table-column prop="id" label="id" width="80" ></el-table-column>
        <el-table-column prop="username" label="用户名"></el-table-column>
        <el-table-column prop="tel" label="手机号"></el-table-column>
        <el-table-column prop="email" label="邮箱"></el-table-column>
        <el-table-column prop="sex" label="性别" ></el-table-column>
        <el-table-column prop="locked" label="锁定" ></el-table-column>
        <el-table-column prop="created_at" label="注册时间" ></el-table-column>
        <el-table-column label="操作" >
          <template slot-scope="userListRow">
            <el-button @click="getUserInfo(userListRow.row)" type="primary" size="small">详情</el-button>

            <el-popconfirm @confirm="userLocked(userListRow.row)" title="确定要冻结该用户吗？">
              <el-button v-if="userListRow.row.locked==0" slot="reference" type="danger" size="small" >冻结</el-button>
              <el-button v-else slot="reference" type="danger" size="small" >解冻</el-button>
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

    <el-dialog title="用户详情" v-if="UserInfoVisible" :visible.sync="UserInfoVisible">
        <el-row>
          <el-col :span="4"><span>头像：</span></el-col>
         <el-col :span="20"><el-avatar :size="100" :src="UserInfo.avatar_url"></el-avatar></el-col>
        </el-row>

        <el-row>
          <el-col :span="4"><span>用户id：</span></el-col>
          <el-col :span="20"><div>{{UserInfo.id}}</div></el-col>
        </el-row>

        <el-row>
          <el-col :span="4"><span>用户名：</span></el-col>
          <el-col :span="20"><div>{{UserInfo.username}}</div></el-col>
        </el-row>

        <el-row>
          <el-col :span="4"><span>角色：</span></el-col>
          <el-col :span="20"><div>{{UserInfo.userRole}}</div></el-col>
        </el-row>

        <el-row>
           <el-col :span="4"><span>邮箱：</span></el-col>
           <el-col :span="20"><div>{{UserInfo.email}}</div></el-col>
        </el-row>
        <el-row>
           <el-col :span="4"><span>手机号：</span></el-col>
           <el-col :span="20"><div>{{UserInfo.tel}}</div></el-col>
        </el-row>
        <el-row>
           <el-col :span="4"><span>性别：</span></el-col>
           <el-col :span="20"><div>{{UserInfo.sex}}</div></el-col>
        </el-row>
        <el-row>
           <el-col :span="4"><span>账号状态：</span></el-col>
           <el-col :span="20"><div>{{UserInfo.locked==0?'正常':'冻结'}}</div></el-col>
        </el-row>
    </el-dialog>

  </div>
</template>

<script>
  export default {
    name:"UserList",
    data(){
      return {
        UserListData:[],
        UserInfo:[],
        UserInfoVisible:false,
        userStatus:'',
        userSex:'',
        time_range:null,
        paginationInfo:'',
        nowPage:1
      };
    },
    mounted(){
      var that=this;
      this.axios.get('https://grouponapi.top/api/admin/user').then(res=>{
        that.UserListData=res.data.data
        that.paginationInfo=res.data.meta.pagination
      }).catch(function(error){
        that.$message({
          message: error.response.data.message,
          type:'error'
        });
      })
    },
    methods:{

      //用户详情
      getUserInfo(row){
        var that=this;
        this.axios.get('https://grouponapi.top/api/admin/user/'+row.id).then(res=>{

            that.UserInfo=res.data.data;

        }).catch(function(error){
          that.$message({
            message: error.response.data.message,
            type:'error'
          });
        })
        this.UserInfoVisible=true
      },

      //是否冻结用户
      userLocked(row){
        var that=this;
        this.axios.patch('https://grouponapi.top/api/admin/user/'+row.id+'/on').then(res=>{
          that.$message({
            message: '操作成功！',
            type:'success'
          });
          row.locked=row.locked==0?1:0;
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
        let locked=this.userStatus=='all'||this.userStatus==''?'':'locked='+this.userStatus+'&';
        let sex=this.userSex=='all'||this.userSex==''?'':'sex='+this.userSex+'&';

        let time_begin=this.time_range==null?'':'time_begin='+this.time_range[0]+'&';
        let time_end=this.time_range==null?'':'time_end='+this.time_range[1]+'&';

        let page=this.nowPage==0?'':'page='+this.nowPage+'&'


        this.axios.get('https://grouponapi.top/api/admin/user?'+locked+sex+time_begin+time_end+page).then(res=>{
          that.UserListData=res.data.data;
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
