<template>
  <div id="">

   <el-row id="status_bar">
      <el-col :span="5">
        <el-select @change="dataChange" v-model="CommentStatus" placeholder="状态">
          <el-option label="全部" value="all"></el-option>
          <el-option label="正常" value="1"></el-option>
          <el-option label="禁用" value="0"></el-option>
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


    <el-table :data="CommentListData" height="530" border style="width: 100%">

        <el-table-column prop="id" label="id" width="80" ></el-table-column>
        <el-table-column prop="product.data.title" label="商品名"></el-table-column>
        <el-table-column prop="product.data.id" label="商品id"></el-table-column>
        <el-table-column prop="user.data.username" label="用户名"></el-table-column>
        <el-table-column prop="user.data.id" label="用户id" ></el-table-column>
        <el-table-column prop="created_at" label="发表时间" ></el-table-column>
		<el-table-column prop="status" label="评论状态" ></el-table-column>
        <el-table-column label="操作" >
          <template slot-scope="CommentListRow">
            <el-button @click="getCommentInfo(CommentListRow.row)" type="primary" size="small">详情</el-button>

            <el-popconfirm @confirm="commentStatus(CommentListRow.row)" title="确定要禁用该评论吗？">
              <el-button v-if="CommentListRow.row.status==1" slot="reference" type="danger" size="small" >禁用</el-button>
              <el-button v-else slot="reference" type="danger" size="small" >解禁</el-button>
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

    <el-dialog title="评论详情" v-if="CommentInfoVisible" :visible.sync="CommentInfoVisible">

		<el-row>
		  <el-col :span="4"><span>商品名称：</span></el-col>
		 <el-col :span="20"><div>{{CommentInfo.product.data.title}}</div></el-col>
		</el-row>
		
		<el-row>
		  <el-col :span="4"><span>商品id：</span></el-col>
		  <el-col :span="20"><div>{{CommentInfo.product.data.id}}</div></el-col>
		</el-row>
		
		<el-row>
		  <el-col :span="4"><span>用户名：</span></el-col>
		  <el-col :span="20"><div>{{CommentInfo.user.data.username}}</div></el-col>
		</el-row>
		
		<el-row>
		  <el-col :span="4"><span>用户id：</span></el-col>
		  <el-col :span="20"><div>{{CommentInfo.user.data.id}}</div></el-col>
		</el-row>
		
		<el-row>
		  <el-col :span="4"><span>角色：</span></el-col>
		  <el-col :span="20"><div>{{CommentInfo.user.data.userRole}}</div></el-col>
		</el-row>
		
		<el-row>
		  <el-col :span="4"><span>评论内容：</span></el-col>
		  <el-col :span="20"><div>{{CommentInfo.content}}</div></el-col>
		</el-row>
		
		<el-row>
		   <el-col :span="4"><span>评级：</span></el-col>
		   <el-col :span="20"><div>{{CommentInfo.grade}}</div></el-col>
		</el-row>
		
		<el-row>
		   <el-col :span="4"><span>发表时间：</span></el-col>
		   <el-col :span="20"><div>{{CommentInfo.created_at}}</div></el-col>
		</el-row>
		
		<el-row>
		   <el-col :span="4"><span>评论状态：</span></el-col>
		   <el-col :span="20"><div>{{CommentInfo.status==1?'正常':'已禁用'}}</div></el-col>
		</el-row>
    </el-dialog>

  </div>
</template>

<script>
  export default {
    name:"UserList",
    data(){
      return {
        CommentListData:[],
		Commentid:'',
        CommentInfo:'',
        CommentInfoVisible:false,
        CommentStatus:'',
        time_range:null,
        paginationInfo:'',
        nowPage:1
      };
    },
    mounted(){
      var that=this;
      this.axios.get('https://grouponapi.top/api/admin/comment?include=user,product').then(res=>{
        that.CommentListData=res.data.data;
        that.paginationInfo=res.data.meta.pagination
      }).catch(function(error){
        that.$message({
          message: error.response.data.message,
          type:'error'
        });
      })
	  
    },
    methods:{

      //评论详情
      getCommentInfo(row){
        var that=this;

        this.axios.get('https://grouponapi.top/api/admin/comment/'+row.id+'?include=user,product').then(res=>{
            that.CommentInfo=res.data.data;

        }).catch(function(error){
          that.$message({
            message: error.response.data.message,
            type:'error'
          });
        })
        this.CommentInfoVisible=true
      },

      //是否禁用评论
      commentStatus(row){
        var that=this;
        this.axios.patch('https://grouponapi.top/api/admin/comment/status/'+row.id).then(res=>{
          that.$message({
            message: '操作成功！',
            type:'success'
          });
          row.status=row.status==1?0:1;
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
        let status=this.CommentStatus=='all'||this.CommentStatus==''?'':'status='+this.CommentStatus+'&';

        let time_begin=this.time_range==null?'':'time_begin='+this.time_range[0]+'&';
        let time_end=this.time_range==null?'':'time_end='+this.time_range[1]+'&';

        let page=this.nowPage==0?'':'page='+this.nowPage+'&'


        this.axios.get('https://grouponapi.top/api/admin/comment?include=user,product&'+status+time_begin+time_end+page).then(res=>{
          that.CommentListData=res.data.data;
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
