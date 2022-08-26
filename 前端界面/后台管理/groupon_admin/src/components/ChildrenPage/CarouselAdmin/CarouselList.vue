<template>
  <div id="">

   <el-row id="status_bar">
      <el-col :span="5">
        <el-select @change="dataChange" v-model="carouselStatus" placeholder="状态">
          <el-option label="全部" value="all"></el-option>
          <el-option label="启用" value="1"></el-option>
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


    <el-table :data="CarouselListData" height="530" border style="width: 100%">

        <el-table-column prop="id" label="id" width="80" ></el-table-column>
        <el-table-column prop="name" label="轮播图名称"></el-table-column>
        <el-table-column prop="img" label="图片名称"></el-table-column>
        <el-table-column prop="url" label="链接地址" ></el-table-column>
        <el-table-column label="状态" >
			<template slot-scope="CarouselListRow">
				<el-popconfirm @confirm="updateStatus(CarouselListRow.row)" title="确定要修改状态吗？">
				  <el-button slot="reference" type="danger" size="small" >{{ CarouselListRow.row.status==1?"启用":"禁用"  }}</el-button>
				</el-popconfirm>
			</template>
		</el-table-column>
        <el-table-column label="操作" >
          <template slot-scope="CarouselListRow">
            <el-button @click="getCarouselInfo(CarouselListRow.row)" type="primary" size="small">详情</el-button>

            <el-button slot="reference" type="danger" size="small"  @click="CarouselUpdate(CarouselListRow.row)">更新</el-button>

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

    <el-dialog title="轮播图详情" v-if="CarouselInfoVisible" :visible.sync="CarouselInfoVisible">
		<div class="info" v-show="info">
			<el-row>
			  <el-col :span="4"><span>图片：</span></el-col>
			 <el-col :span="20"><el-image style="width: 100px; height: 100px" :src="CarouselInfo.img_url" fit="fill"></el-image></el-col>
			</el-row>
			
			<el-row>
			  <el-col :span="4"><span>轮播图名称：</span></el-col>
			  <el-col :span="20"><div>{{CarouselInfo.name}}</div></el-col>
			</el-row>
			
			<el-row>
			  <el-col :span="4"><span>图片名称：</span></el-col>
			  <el-col :span="20"><div>{{CarouselInfo.img}}</div></el-col>
			</el-row>
			
			<el-row>
			   <el-col :span="4"><span>链接地址：</span></el-col>
			   <el-col :span="20"><div>{{CarouselInfo.url}}</div></el-col>
			</el-row>
			<el-row>
			   <el-col :span="4"><span>状态：</span></el-col>
			   <el-col :span="20"><div>{{CarouselInfo.status==1?'启用':'禁用'}}</div></el-col>
			</el-row>
		</div>
		
        <div class="update" v-show="update">
        	<el-form label-position="top" :rules="rules" ref="ruleForm" label-width="80px" :model="formLabelAlign">
        		<el-form-item label="图片(一次只能添加 1 张)" prop="picture">
					<div v-if="imgUpdate">
						<el-image style="width: 100px; height: 100px" :src="formLabelAlign.img_url" fit="fill"></el-image>
						<button type="button" @click="imgUpdate=false; flag=true;" style="cursor: pointer;">点击修改图片</button>
					</div>
					<div v-else>
						<el-upload action="" :limit="1" list-type="picture-card" :on-remove="handleRemove"
							:auto-upload="false" :on-change="uploadBefore" :show-file-list="true">
							<i class="el-icon-plus"></i>
						</el-upload>
					</div>
        			
        		</el-form-item>
        		<el-form-item label="轮播图名称" prop="imageName">
        			<el-input v-model="formLabelAlign.imageName"></el-input>
        		</el-form-item>
				<el-form-item label="图片名称">
					<el-input v-model="formLabelAlign.name" disabled></el-input>
				</el-form-item>

        		<el-form-item label="链接地址" prop="image_url">
        			<el-input v-model="formLabelAlign.image_url"></el-input>
        		</el-form-item>
        		<div id="btn">
        			<button type="button" class="btn" @click="RefundUpdate(formLabelAlign.id)">修改</button>
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
        CarouselListData:[],
        CarouselInfo:'',
        CarouselInfoVisible:false,
		info:false,
		update:false,
		imgUpdate:true,
		formLabelAlign: {
			id: '',
			picture: '',
			imageName: '',
			name:'',
			image_url: '', //链接地址
			img_url: ''  //图片地址
		},
		rules: {
			picture: [{
				required: true,
				message: '请添加图片(1张)',
				trigger: 'blur'
			}],
			imageName: [{
				required: true,
				message: '请输入轮播图名称',
				trigger: 'blur'
			}],
			image_url: [{
				required: true,
				message: '请输入链接地址',
				trigger: 'blur'
			}]
		},
		flag:false,
        carouselStatus:'',
        time_range:null,
        paginationInfo:'',
        nowPage:1
      };
    },
    mounted(){
      var that=this;
      this.axios.get('https://grouponapi.top/api/admin/carousel').then(res=>{

        that.CarouselListData=res.data.data
        that.paginationInfo=res.data.meta.pagination
      }).catch(function(error){
        that.$message({
          message: error.response.data.message,
          type:'error'
        });
      })
    },
    methods:{
		updateStatus(row){
			var that=this;
			this.axios.patch('https://grouponapi.top/api/admin/carousel/'+ row.id +'/status')
			.then(res => {
				row.status=row.status?0:1
			}).catch(function(error){
				console.log(error)
			})
		},
		
      //轮播图详情
      getCarouselInfo(row){
        var that=this;
        this.axios.get('https://grouponapi.top/api/admin/carousel/'+row.id).then(res=>{

            that.CarouselInfo=res.data.data;

        }).catch(function(error){
          that.$message({
            message: error.response.data.message,
            type:'error'
          });
        })
        this.CarouselInfoVisible=true;
		this.info=true;
		this.update=false;
      },

      //更新轮播图
      CarouselUpdate(row){
        var that=this;

		this.axios.get('https://grouponapi.top/api/admin/carousel/'+row.id).then(res=>{
			that.formLabelAlign.id=res.data.data.id;
			that.formLabelAlign.img_url=res.data.data.img_url;
			that.formLabelAlign.imageName=res.data.data.name;
			that.formLabelAlign.name=res.data.data.img;
			that.formLabelAlign.image_url=res.data.data.url;
		}).catch(function(error){
		  that.$message({
		    message: error.response.data.message,
		    type:'error'
		  });
		})
		this.CarouselInfoVisible=true;
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
	  RefundUpdate(CarouselInfoId){
		  var that=this;
		  this.$confirm('确定要更改图片信息吗？','提示',{
			  confirmButtonText: '确定',
			  cancelButtonText: '取消',
			  type: 'warning'
		  }).then(() => {
			  if(that.flag==true){
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
				  		let keyValue = "carousel/" + time + random_Num[0] + random_Num[1] + random_Num[2] + random_Num[3] + '.' + imgType;
				  		//文件名
				  		ossData.append("key", keyValue);
				  		ossData.append("policy", data.policy);
				  		ossData.append("OSSAccessKeyId", data.accessid);
				  		ossData.append("success_action_status", 201);
				  		ossData.append("signature", data.signature);
				  		ossData.append("file", that.formLabelAlign.picture.raw, that.formLabelAlign
				  			.picture.raw.name);

				  		that.axios.post(data.host, ossData, {
				  				headers: {
				  					"Content-Type": "multipart/form-data"
				  				}
				  		}).then(res => {
				  			if (res.status == 201) {

				  				that.axios.patch('https://grouponapi.top/api/admin/carousel/'+CarouselInfoId,{
									  name: that.formLabelAlign.imageName,
									  url: that.formLabelAlign.image_url,
									  img: keyValue.split("/")[1]
				  				}).then(res => {

									  that.$message({
										  message: '更改成功！',
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
				  that.axios.patch('https://grouponapi.top/api/admin/carousel/'+CarouselInfoId,{
					  name: that.formLabelAlign.imageName,
					  url: that.formLabelAlign.image_url,
					  img: that.formLabelAlign.name
				  }).then(res => {

					  that.$message({
						  message: '更改成功！',
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
		  }).catch(() => {
			  that.$message({
				  message: '已取消更改',
				  type: 'info'
			  })
		  })
	  },

      //状态改变
      dataChange(type){
        
        if(type!='page'){
          this.nowPage=1
        }
        
        var that=this;
        let status=this.carouselStatus=='all'||this.carouselStatus==''?'':'status='+this.carouselStatus+'&';

        let time_begin=this.time_range==null?'':'time_begin='+this.time_range[0]+'&';
        let time_end=this.time_range==null?'':'time_end='+this.time_range[1]+'&';

        let page=this.nowPage==0?'':'page='+this.nowPage+'&'


        this.axios.get('https://grouponapi.top/api/admin/carousel?'+status+time_begin+time_end+page).then(res=>{
          that.CarouselListData=res.data.data;
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
