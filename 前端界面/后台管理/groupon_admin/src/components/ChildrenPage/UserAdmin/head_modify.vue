<template>
  <div id="">

   <el-row id="status_bar">
      <el-col :span="5">
        <el-select @change="dataChange" v-model="HeadModifyStatus" placeholder="处理状态">
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


    <el-table :data="HeadModifyListData" height="530" border style="width: 100%">

        <el-table-column prop="id" label="id" width="80" ></el-table-column>
        <el-table-column prop="headInfo.data.name" label="姓名"></el-table-column>
        <el-table-column prop="headInfo.data.identity_name" label="身份"></el-table-column>
        <el-table-column prop="headInfo.data.tel" label="手机号"></el-table-column>
        <el-table-column prop="status_name" label="状态" ></el-table-column>
        <el-table-column label="操作" >
          <template slot-scope="HeadModifyListRow">
            <el-button @click="getHeadModifyInfo(HeadModifyListRow.row)" type="primary" size="small">详情</el-button>

            <el-popconfirm v-if="HeadModifyListRow.row.status==2" @confirm="disposeApply(HeadModifyListRow.row,1)" title="确认要同意该团长修改信息吗？">
              <el-button slot="reference" type="success" size="small" >同意</el-button>
            </el-popconfirm>

            <el-popconfirm v-if="HeadModifyListRow.row.status==2" @confirm="disposeApply(HeadModifyListRow.row,0)" title="确认要拒绝该团长修改信息吗？">
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

    <el-dialog title="详情" v-if="HeadModifyInfoVisible" :visible.sync="HeadModifyInfoVisible">

        <el-row>
          <el-col :span="4"><span>店铺图片：</span></el-col>
         <el-col :span="20"><el-avatar :size="100" :src="HeadModifyInfo.pic_url"></el-avatar></el-col>
        </el-row>


        <el-row>
          <el-col :span="4"><span>团长id：</span></el-col>
          <el-col :span="20"><div>{{HeadModifyInfo.head_id}}</div></el-col>
        </el-row>

        <el-row>
          <el-col :span="4"><span>姓名：</span></el-col>
          <el-col :span="20"><div>{{HeadModifyInfo.applicant}}</div></el-col>
        </el-row>

        <el-row>
          <el-col :span="4"><span>团长身份：</span></el-col>
          <el-col :span="20"><div>{{HeadModifyInfo.identity?'其他':'便利店店长'}}</div></el-col>
        </el-row>

        <el-row>
          <el-col :span="4"><span>店铺名：</span></el-col>
          <el-col :span="20"><div>{{HeadModifyInfo.store_name}}</div></el-col>
        </el-row>

        <el-row>
          <el-col :span="4"><span>地区：</span></el-col>
          <el-col :span="20"><div>{{HeadModifyInfo.address_name}}</div></el-col>
        </el-row>

        <el-row>
          <el-col :span="4"><span>详细地址：</span></el-col>
          <el-col :span="20"><div>{{HeadModifyInfo.store_address}}</div></el-col>
        </el-row>

        <el-row>
           <el-col :span="4"><span>身份证号：</span></el-col>
           <el-col :span="20"><div>{{HeadModifyInfo.id_number}}</div></el-col>
        </el-row>
        <el-row>
           <el-col :span="4"><span>手机号：</span></el-col>
           <el-col :span="20"><div>{{HeadModifyInfo.tel}}</div></el-col>
        </el-row>
        <el-row>
           <el-col :span="4"><span>收款方式：</span></el-col>
           <el-col :span="20"><div>{{HeadModifyInfo.payment_method}}</div></el-col>
        </el-row>
        <el-row>
           <el-col :span="4"><span>收款账号：</span></el-col>
           <el-col :span="20"><div>{{HeadModifyInfo.payment_account}}</div></el-col>
        </el-row>
        <el-row>
           <el-col :span="4"><span>处理状态：</span></el-col>
           <el-col :span="20"><div>{{HeadModifyInfo.status}}</div></el-col>
        </el-row>


    </el-dialog>

  </div>
</template>

<script>
  export default {
    name:"UserList",
    data(){
      return {
        HeadModifyListData:[],
        HeadModifyInfo:[],
        HeadModifyInfoVisible:false,
        HeadModifyStatus:'',
        time_range:null,
        paginationInfo:'',
        nowPage:1
      };
    },
    mounted(){
      var that=this;
      this.axios.get('https://grouponapi.top/api/admin/head/modify/headinfo/list?include=headInfo').then(res=>{

        that.HeadModifyListData=res.data.data
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
      getHeadModifyInfo(row){
        var that=this;
        this.axios.get('https://grouponapi.top/api/admin/head/modify/headinfo/info/'+row.id).then(res=>{

            that.HeadModifyInfo=res.data.data;

        }).catch(function(error){
          that.$message({
            message: error.response.data.message,
            type:'error'
          });
        })
        this.HeadModifyInfoVisible=true
      },

      //状态改变
      dataChange(type){
        if(type!='page'){
          this.nowPage=1
        }

        var that=this;
        let status=this.HeadModifyStatus=='all'||this.HeadModifyStatus==''?'':'status='+this.HeadModifyStatus+'&';

        let time_begin=this.time_range==null?'':'time_begin='+this.time_range[0]+'&';
        let time_end=this.time_range==null?'':'time_end='+this.time_range[1]+'&';

        let page=this.nowPage==0?'':'page='+this.nowPage+'&'



        this.axios.get('https://grouponapi.top/api/admin/head/modify/headinfo/list?include=headInfo&'+status+time_begin+time_end+page).then(res=>{
          that.HeadModifyListData=res.data.data;
          that.paginationInfo=res.data.meta.pagination;
        }).catch(function(error){
          that.$message({
            message: error.response.data.message,
            type:'error'
          });
        })

      },

      //同意或拒绝修改请求
      disposeApply(row,status){
        var that=this;

          // 同意修改
          this.axios.patch('https://grouponapi.top/api/admin/head/modify/headinfo/'+row.id+'/dispose',{
            status:status
          }).then(res=>{
            that.$message({
              message: '操作成功！',
              type:'success'
            });
            row.status=row.status?1:0;
            row.status_name=row.status?'同意':'拒绝';
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
  #status_bar{
    margin-bottom: 1rem;
  }
  #pagination{
    text-align: center;
    margin-top: 0.2rem;
  }
</style>
