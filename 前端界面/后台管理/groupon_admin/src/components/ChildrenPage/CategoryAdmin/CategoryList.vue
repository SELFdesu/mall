<template>
  <div id="">

   <el-row id="status_bar">
      <el-col :span="5">
        <el-select @change="dataChange" v-model="CategoryStatus" placeholder="分类状态">
          <el-option label="全部" value="1"></el-option>
          <el-option label="启用" value="0"></el-option>
        </el-select>
      </el-col>
	  
	  <el-col :span="5">
	    <el-select @change="dataChange" v-model="CategoryLevel" placeholder="分类级别">
	      <el-option label="所有" value="0"></el-option>
	      <el-option label="一级" value="1"></el-option>
	    </el-select>
	  </el-col>
    </el-row>


    <el-table :data="CategoryListData" height="530" border style="width: 100%">
		<el-table-column type="expand">
			<template slot-scope="CategoryListRow">
				<el-table :data="CategoryListRow.row.children" border style="width: 100%">
				    <el-table-column prop="id" label="id" width="180"></el-table-column>
				    <el-table-column prop="name" label="二级分类名称" width="180"></el-table-column>
				    <el-table-column prop="pid" label="父id"></el-table-column>
					<el-table-column label="状态" >
						<template slot-scope="ChildrenRow">
							<el-popconfirm @confirm="updateStatus(ChildrenRow.row)" title="确定要修改状态吗？">
							  <el-button slot="reference" type="danger" size="small" >{{ ChildrenRow.row.status==1?"启用":"禁用"  }}</el-button>
							</el-popconfirm>
						</template>
					</el-table-column>
					<el-table-column label="操作" >
					  <template slot-scope="ChildrenRow">
					    <el-button @click="getCategoryInfo(ChildrenRow.row)" type="primary" size="small">详情</el-button>
					    <el-button slot="reference" type="danger" size="small"  @click="CategoryUpdate(ChildrenRow.row)">修改</el-button>
					  </template>
					</el-table-column>
				</el-table>
			</template>
		</el-table-column>

        <el-table-column prop="id" label="id" width="80" ></el-table-column>
        <el-table-column prop="name" label="一级分类名称"></el-table-column>
        <el-table-column prop="pid" label="父id"></el-table-column>
        <el-table-column label="状态" >
			<template slot-scope="CategoryListRow">
				<el-popconfirm @confirm="updateStatus(CategoryListRow.row)" title="确定要修改状态吗？">
				  <el-button slot="reference" type="danger" size="small" >{{ CategoryListRow.row.status==1?"启用":"禁用"  }}</el-button>
				</el-popconfirm>
			</template>
		</el-table-column>
        <el-table-column label="操作" >
          <template slot-scope="CategoryListRow">
            <el-button @click="getCategoryInfo(CategoryListRow.row)" type="primary" size="small">详情</el-button>

            <el-button slot="reference" type="danger" size="small"  @click="CategoryUpdate(CategoryListRow.row)">修改</el-button>

          </template>
        </el-table-column>
		
		
    </el-table>

    <el-dialog title="分类详情" v-if="CategoryInfoVisible" :before-close="handleClose" :visible.sync="CategoryInfoVisible">
		<div class="info" v-show="info">
			<el-row>
			  <el-col :span="4"><span>id：</span></el-col>
			  <el-col :span="20"><div>{{CategoryInfo.id}}</div></el-col>
			</el-row>
			
			
			<el-row>
			  <el-col :span="4"><span>分类级别：</span></el-col>
			  <el-col :span="20"><div>{{CategoryInfo.level}}</div></el-col>
			</el-row>
			<el-row>
			   <el-col :span="4"><span>父id：</span></el-col>
			   <el-col :span="20"><div>{{CategoryInfo.pid}}</div></el-col>
			</el-row>
			<el-row>
			  <el-col :span="4"><span>分类名称：</span></el-col>
			  <el-col :span="20"><div>{{CategoryInfo.name}}</div></el-col>
			</el-row>
			<el-row>
			  <el-col :span="4"><span>图片：</span></el-col>
			 <el-col :span="20"><el-image style="width: 100px; height: 100px" :src="CategoryInfo.pic_url" fit="fill"></el-image></el-col>
			</el-row>
			<el-row>
			   <el-col :span="4"><span>创建时间：</span></el-col>
			   <el-col :span="20"><div>{{CategoryInfo.created_at}}</div></el-col>
			</el-row>
			<el-row>
			   <el-col :span="4"><span>修改时间：</span></el-col>
			   <el-col :span="20"><div>{{CategoryInfo.updated_at}}</div></el-col>
			</el-row>
			<el-row>
			   <el-col :span="4"><span>状态：</span></el-col>
			   <el-col :span="20"><div>{{CategoryInfo.status==1?'启用':'禁用'}}</div></el-col>
			</el-row>
		</div>
		
        <div class="update" v-show="update">
        	<el-form label-position="top" :rules="rules" ref="ruleForm" label-width="80px" :model="formLabelAlign">
				<el-form-item label="图片(一次只能添加 1 张)" prop="picture">
					<div v-if="imgUpdate">
						<el-image style="width: 100px; height: 100px" :src="formLabelAlign.picture" fit="fill"></el-image>
						<button type="button" @click="imgUpdate=false;" style="cursor: pointer;">点击修改图片</button>
					</div>
					<div v-else>
						<el-upload action="" :limit="1" list-type="picture-card" :on-remove="handleRemove"
							:auto-upload="false" :on-change="uploadBefore" :show-file-list="true">
							<i class="el-icon-plus"></i>
						</el-upload>
					</div>
					
				</el-form-item>
				
        		<el-form-item label="分类名称" prop="name">
        			<el-input v-model="formLabelAlign.name"></el-input>
        		</el-form-item>
				<el-form-item label="父id" prop="pid">
					<el-input v-model="formLabelAlign.pid" :disabled="disabled"></el-input>
				</el-form-item>
        		<div id="btn">
        			<button type="button" class="btn" @click="RefundUpdate(formLabelAlign.id,'ruleForm')">确认修改</button>
        		</div>
        	</el-form>
        </div>
    </el-dialog>

  </div>
</template>

<script>
  export default {
    name:"UserList",
    data(){
      return {
        CategoryListData:[],
        CategoryInfo:'',
        CategoryInfoVisible:false,
		info:false,
		update:false,
		imgUpdate:true,
		formLabelAlign: {
			id: '',
			pic: '',
			name:'',
			pid:'',
			picture:''
		},
		rules: {
			name: [{
				required: true,
				message: '请输入分类名称',
				trigger: 'blur'
			}],
			pid: [{
				required: true,
				message: '请输入父id',
				trigger: 'blur'
			}],
			picture: [{
				required: true,
				message: '请添加图片(1张)',
				trigger: 'blur'
			}]
		},
		disabled:'',
        CategoryStatus:'',
		CategoryLevel:''
      };
    },
    mounted(){
      var that=this;
      this.axios.get('https://grouponapi.top/api/admin/category').then(res=>{

        that.CategoryListData=res.data
      }).catch(function(error){
        that.$message({
          message: error.response.data.message,
          type:'error'
        });
      })
    },
    methods:{
		// 状态改变
		updateStatus(row){
			var that=this;
			this.axios.patch('https://grouponapi.top/api/admin/category/'+ row.id +'/status')
			.then(res => {

				row.status=row.status?0:1

			}).catch(function(error){
				console.log(error)
			})
		},
		
      //一级分类详情
      getCategoryInfo(row){
        var that=this;
        this.axios.get('https://grouponapi.top/api/admin/category/'+row.id).then(res=>{

            that.CategoryInfo=res.data;

        }).catch(function(error){
          that.$message({
            message: error.response.data.message,
            type:'error'
          });
        })
        this.CategoryInfoVisible=true;
		this.info=true;
		this.update=false;
      },

      //修改分类
      CategoryUpdate(row){
        var that=this;

		this.axios.get('https://grouponapi.top/api/admin/category/'+row.id).then(res=>{
			that.formLabelAlign.id=res.data.id;
			that.formLabelAlign.name=res.data.name;
			that.formLabelAlign.pid=res.data.pid;
			that.formLabelAlign.picture=res.data.pic_url;
			that.formLabelAlign.pic=res.data.pic;
			if(row.pid != 0){
				that.disabled=false;
			}else{
				that.disabled=true;
			}
		}).catch(function(error){
		  that.$message({
		    message: error.response.data.message,
		    type:'error'
		  });
		})
		this.CategoryInfoVisible=true;
		this.update=true;
		this.info=false;
      },
	  
	  uploadBefore(file) {
	  	//上传文件之前校验图片格式和大小
	  	const isJPG =
	  		file.raw.type === "image/jpeg" ||
	  		file.raw.type === "image/png" ||
	  		file.raw.type === "image/gif" ||
	  		file.raw.type === "image/jpg";
	  	const isLt5M = file.size / 1024 / 1024 < 5;
	  	if (!isJPG) {
	  		this.$message.error("上传图片只能是 JPG、JPEG、PNG、GIF 格式!");
	  		return false;
	  	}
	  	if (!isLt5M) {
	  		this.$message.error("上传图片大小不能超过5MB!");
	  		return false;
	  	}
	  	this.formLabelAlign.picture = file;
	  },
	  handleRemove(file, fileList) {
	  	this.formLabelAlign.picture = fileList;
	  },
	  // 确认修改
	  RefundUpdate(CategoryId,ruleForm){
		  var that=this;
		  this.$refs[ruleForm].validate((valid) => {
		  	if (!valid) {
		  		return false;
		  	} else {
			  that.$confirm('确定要修改该信息吗？','提示',{
				  confirmButtonText: '确定',
				  cancelButtonText: '取消',
				  type: 'warning'
			  }).then(() => {
				  if(that.imgUpdate==false){
					  if (this.formLabelAlign.picture != '') {
						that.axios.get('https://grouponapi.top/api/auth/oss/token')
						.then(res => {
					  
							var data = res.data;

							
							var time= +new Date();
							var random_Num=[];
							for(var i=0; i<4; i++){
								random_Num.push(Math.round(Math.random()*100))
							}

							var ossData = new FormData();
							ossData.append("name", that.formLabelAlign.picture.raw.name);
							//获取图片类型 后缀 jpg / png
							let imgType = that.formLabelAlign.picture.raw.type.split("/")[1];
							let filename = that.formLabelAlign.picture.raw.name; //md5对图片名称进行加密
							let keyValue = "category/" + time + random_Num[0] + random_Num[1] + random_Num[2] + random_Num[3] + '.' + imgType;
							//文件名
							ossData.append("key", keyValue);
							ossData.append("policy", data.policy);
							ossData.append("OSSAccessKeyId", data.accessid);
							ossData.append("success_action_status", 201);
							ossData.append("signature", data.signature);
							ossData.append("file", that.formLabelAlign.picture.raw, that.formLabelAlign.picture.raw.name);

							that.axios.post(data.host, ossData, {
									headers: {
										"Content-Type": "multipart/form-data"
									}
							}).then(res => {
								if (res.status == 201) {

									that.axios.patch('https://grouponapi.top/api/admin/category/'+CategoryId,{
										  name: that.formLabelAlign.name,
										  pid: that.formLabelAlign.pid,
										  pic: keyValue.split("/")[1]
									}).then(res => {
										  that.$message({
											  message: '修改成功！',
											  type: 'success'
										  })
										  history.go(0);
									}).catch(function(error){
										  that.$message({
											  message: error.response.data.message,
											  type: 'error'
										  })
									})
								}
							}).catch(function(error) {
								console.log(error);
							});
						})
					  }else{
						  that.$message({
							  message: '请选择 1 张图片',
							  type: 'error'
						  })
					  }
				  }else{
					  that.axios.patch('https://grouponapi.top/api/admin/category/'+CategoryId,{
						  name: that.formLabelAlign.name,
						  pid: that.formLabelAlign.pid,
						  pic: that.formLabelAlign.pic
					  }).then(res => {
						  that.$message({
							  message: '修改成功！',
							  type: 'success'
						  })
						  setTimeout(function(){
							  history.go(0);
						  },500)
					  }).catch(function(error){
						  that.$message({
							  message: error.response.data.message,
							  type: 'error'
						  })
					  })
				  }
				  
				  
				  
				  

			  }).catch(() => {
				  that.$message({
					  message: '已取消更改',
					  type: 'info'
				  })
			  })
			}
		  })
	  },
	  handleClose(done) {
		  this.$confirm('确认关闭？')
			.then(_ => {
			  done();
			  this.imgUpdate=true;
			})
			.catch(_ => {});
	  },

      //状态改变
      dataChange(){
        
        var that=this;
        let queryall=this.CategoryStatus=='1'||this.CategoryStatus==''?'queryall=1&':'queryall=0&';
		let onlyOne=this.CategoryLevel=='0'||this.CategoryLevel==''?'onlyOne=0&':'onlyOne=1&';

        this.axios.get('https://grouponapi.top/api/admin/category?'+queryall+onlyOne).then(res=>{
          that.CategoryListData=res.data;
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
  
  #btn {
  	text-align: center;
  	margin: 5% 0;
  }
  
  .btn {
  	margin: auto;
  	width: 20%;
  	padding: 1% 0;
  	font-size: 18px;
  	color: #fff;
  	background-color: #2A9B00;
  	border: 1px solid #2A9B00;
  	border-radius: 2px;
  	cursor: pointer;
  }
</style>
