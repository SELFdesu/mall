<template>
  <div id="">

   <el-row id="status_bar">
      <el-col :span="5">
        <el-select @change="dataChange" v-model="HeadResignStatus" placeholder="处理状态">
          <el-option label="全部" value="all"></el-option>
          <el-option label="拒绝" value="0"></el-option>
          <el-option label="同意" value="1"></el-option>
          <el-option label="未处理" value="2"></el-option>
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


    <el-table :data="HeadResignListData" height="530" border style="width: 100%">

        <el-table-column prop="id" label="id" width="80" ></el-table-column>
        <el-table-column prop="headInfo.data.name" label="姓名"></el-table-column>
        <el-table-column prop="headInfo.data.identity_name" label="身份"></el-table-column>
        <el-table-column prop="headInfo.data.tel" label="手机号"></el-table-column>
        <el-table-column prop="status_name" label="状态" ></el-table-column>
        <el-table-column prop="type_name" label="申请类型" ></el-table-column>
        <el-table-column label="操作" >
          <template slot-scope="HeadResignListRow">
            <el-button @click="getHeadResignInfo(HeadResignListRow.row)" type="primary" size="small">详情</el-button>

            <el-popconfirm v-if="HeadResignListRow.row.status==2" @confirm="disposeApply(HeadResignListRow.row,1)" title="确认要同意该团长离职吗？">
              <el-button slot="reference" type="success" size="small" >同意</el-button>
            </el-popconfirm>

            <el-popconfirm v-if="HeadResignListRow.row.status==2" @confirm="disposeApply(HeadResignListRow.row,0)" title="确认要拒绝该团长离职吗？">
              <el-button slot="reference" type="danger" size="small" >拒绝</el-button>
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

    <el-dialog title="详情" v-if="HeadResignInfoVisible" :visible.sync="HeadResignInfoVisible">

        <el-row>
          <el-row>
             <div id="">
                 团长信息：
             </div>
          </el-row>
          <el-row  style="border: 1px dotted black; padding: 1rem;">

            <el-row>
              <el-col :span="4"><span>团长id：</span></el-col>
              <el-col :span="20"><div>{{HeadResignInfo.headInfo.data.id}}</div></el-col>
            </el-row>
            <el-row>
              <el-col :span="4"><span>姓名：</span></el-col>
              <el-col :span="20"><div>{{HeadResignInfo.headInfo.data.name}}</div></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>身份：</span></el-col>
              <el-col :span="20"><div>{{HeadResignInfo.headInfo.data.identity_name}}</div></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>手机号：</span></el-col>
              <el-col :span="20"><div>{{HeadResignInfo.headInfo.data.tel}}</div></el-col>
            </el-row>

            <el-row>
               <el-col :span="4"><span>身份证号：</span></el-col>
               <el-col :span="20"><div>{{HeadResignInfo.headInfo.data.id_number}}</div></el-col>
            </el-row>

            <el-row>
               <el-col :span="4"><span>状态：</span></el-col>
               <el-col :span="20"><div>{{HeadResignInfo.headInfo.data.status==0?'离职':'在职'}}</div></el-col>
            </el-row>
          </el-row>
        </el-row>

        <el-row>
          <el-row>
             <div id="">
                 申请离职信息：
             </div>
          </el-row>
          <el-row  style="border: 1px dotted black; padding: 1rem;">

            <el-row>
              <el-col :span="4"><span>离职原因：</span></el-col>
              <el-col :span="20"><div>{{HeadResignInfo.cause}}</div></el-col>
            </el-row>
            <el-row>
              <el-col :span="4"><span>处理状态：</span></el-col>
              <el-col :span="20"><div>{{HeadResignInfo.status_name}}</div></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>申请类型：</span></el-col>
              <el-col :span="20"><div>{{HeadResignInfo.type_name}}</div></el-col>
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
        HeadResignListData:[],
        HeadResignInfo:[],
        HeadResignInfoVisible:false,
        HeadResignStatus:'',
        time_range:null,
        paginationInfo:'',
        nowPage:1
      };
    },
    mounted(){
      var that=this;
      this.axios.get('https://grouponapi.top/api/admin/head/apply/resign/list?include=headInfo').then(res=>{

        that.HeadResignListData=res.data.data
        that.paginationInfo=res.data.meta.pagination
      }).catch(function(error){
        that.$message({
          message: error.response.data.message,
          type:'error'
        });
      })
    },
    methods:{

      //团长申请离职详情
      getHeadResignInfo(row){
        var that=this;
        this.axios.get('https://grouponapi.top/api/admin/head/apply/resign/'+row.id+'/info?include=headInfo').then(res=>{

            that.HeadResignInfo=res.data.data;

        }).catch(function(error){
          that.$message({
            message: error.response.data.message,
            type:'error'
          });
        })
        this.HeadResignInfoVisible=true
      },

      //状态改变
      dataChange(type){

        if(type!='page'){
          this.nowPage=1
        }

        var that=this;
        let status=this.HeadResignStatus=='all'||this.HeadResignStatus==''?'':'status='+this.HeadResignStatus+'&';

        let time_begin=this.time_range==null?'':'time_begin='+this.time_range[0]+'&';
        let time_end=this.time_range==null?'':'time_end='+this.time_range[1]+'&';

        let page=this.nowPage==0?'':'page='+this.nowPage+'&'



        this.axios.get('https://grouponapi.top/api/admin/head/apply/resign/list?include=headInfo&'+status+time_begin+time_end+page).then(res=>{
          that.HeadResignListData=res.data.data;
          that.paginationInfo=res.data.meta.pagination;
        }).catch(function(error){
          that.$message({
            message: error.response.data.message,
            type:'error'
          });
        })

      },

      //同意或拒绝离职请求
      disposeApply(row,status){
        var that=this;

        if(status){
          // 同意离职
          this.axios.patch('https://grouponapi.top/api/admin/head/'+row.headInfo.data.user_id+'/takeback').then(res=>{
            that.$message({
              message: '操作成功！',
              type:'success'
            });
            row.status=1;
            row.status_name='同意';
          }).catch(function(error){
            that.$message({
              message: error.response.data.message,
              type:'error'
            });
          })

        }else{
            // 拒绝离职
            this.axios.patch('https://grouponapi.top/api/admin/head/apply/resign/'+row.id+'/refuse').then(res=>{
              that.$message({
                message: '操作成功！',
                type:'success'
              });
              row.status=0;
              row.status_name='拒绝';
            }).catch(function(error){
              that.$message({
                message: error.response.data.message,
                type:'error'
              });
            })
        }
      },


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
