<template>
	<div id="">
		<div class="main">
			<el-form label-position="top" :rules="rules" ref="ruleForm" label-width="80px" :model="formLabelAlign">
				<el-form-item label="图片(一次只能添加 1 张)" prop="picture">
					<el-upload action="" :limit="1" list-type="picture-card" :on-remove="handleRemove"
						:auto-upload="false" :on-change="uploadBefore" :show-file-list="true">
						<i class="el-icon-plus"></i>
					</el-upload>
				</el-form-item>
				<el-form-item label="分类级别" prop="level">
					<el-select v-model="formLabelAlign.level" @change="selectChanged" size="small" clearable placeholder="请选择">
					    <el-option label="一级菜单" value="0"></el-option>
						<el-option label="二级菜单" value="1"></el-option>
					</el-select>
				</el-form-item>
				<el-form-item label="父id" prop="pid" v-if="pidShow">
					<el-select v-model="formLabelAlign.pid" size="small" clearable placeholder="请选择">
					    <el-option :label="level.name" :value="level.id" v-for="level in levelBox" :key="level.id"></el-option>
					</el-select>
				</el-form-item>
				<el-form-item label="分类名称" prop="name">
					<el-input v-model="formLabelAlign.name"></el-input>
				</el-form-item>
				<div id="btn">
					<button type="button" class="btn" @click="addCategory('ruleForm')">添加</button>
				</div>
			</el-form>
		</div>
	</div>
</template>

<script>
	export default {
		name: "UserList",
		data() {
			return {
				levelBox:'',
				pidShow:false,
				formLabelAlign: {
					level: '',
					pid:'',
					name: '',
					picture:''
				},
				rules: {
					level: [{
						required: true,
						message: '请选择分类级别',
						trigger: 'change'
					}],
					pid: [{
						required: true,
						message: '请选择父id',
						trigger: 'change'
					}],
					name: [{
						required: true,
						message: '请输入分类名称',
						trigger: 'blur'
					}],
					picture: [{
						required: true,
						message: '请添加图片(1张)',
						trigger: 'blur'
					}]
				}
			};
		},
		mounted() {
			
		},
		methods: {
			selectChanged(value){
				var that=this;
				this.axios.get('https://grouponapi.top/api/admin/category')
				.then(res => {
					if(value == 0){
						that.formLabelAlign.pid=0;
						that.pidShow=false;
					}else{
						that.levelBox=res.data;
						that.pidShow=true;
					}
				})
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
			addCategory(ruleForm) {
				var that = this;
				this.$refs[ruleForm].validate((valid) => {
					if (!valid) {
						return false;
					} else {
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
								ossData.append("file", that.formLabelAlign.picture.raw, that.formLabelAlign
									.picture.raw.name);

								that.axios.post(data.host, ossData, {
										headers: {
											"Content-Type": "multipart/form-data"
										}
								}).then(res => {
									if (res.status == 201) {

										that.axios.post('https://grouponapi.top/api/admin/category',{
											name: that.formLabelAlign.name,
											pid: that.formLabelAlign.pid,
											pic: keyValue.split("/")[1]
										}).then(res => {
											that.$message({
												message: '添加成功！',
												type: 'success'
											})
											history.go(0);
										}).catch(function (error) {
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
						}

					}
				});
			}
		}
	}
</script>

<style>
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
