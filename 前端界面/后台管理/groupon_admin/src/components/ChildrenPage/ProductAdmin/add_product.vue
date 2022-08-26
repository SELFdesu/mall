<template>
	<div id="">
		<div class="main">
			<el-form label-position="top" :rules="rules" ref="ruleForm" label-width="80px" :model="formLabelAlign">

        <el-form-item label="商品名" prop="title">
        	<el-input v-model="formLabelAlign.title"></el-input>
        </el-form-item>

				<el-form-item label="商品图片(限一张)" prop="picture">
					<el-upload action="" :limit="1" list-type="picture-card" :on-remove="handleRemove"
						:auto-upload="false" :on-change="uploadBefore" :show-file-list="true">
						<i class="el-icon-plus"></i>
					</el-upload>
					<el-button slot="trigger" size="small" type="primary">选取</el-button>
				</el-form-item>

				<el-form-item label="分类" prop="category_id">
					<el-cascader
					    @change="confirmCategory"
              v-model="formLabelAlign.category_id"
					    placeholder="试试搜索：指南"
					    :options="category_list"
					    filterable>
					</el-cascader>
				</el-form-item>


        <el-form-item label="商品介绍图(最多5张)"  prop="picture">
        	<el-upload action="" :limit="5" list-type="picture-card" :on-remove="handlePicRemove"
        		:auto-upload="false" :on-change="uploadPicBefore" :show-file-list="true">
        		<i class="el-icon-plus"></i>
        	</el-upload>
        	<el-button slot="trigger" size="small" type="primary">选取</el-button>
        </el-form-item>

        <el-form-item label="价格" prop="price">
        	<el-input v-model="formLabelAlign.price"></el-input>
        </el-form-item>

        <el-form-item label="库存" prop="stock">
        	<el-input v-model="formLabelAlign.stock"></el-input>
        </el-form-item>


        <el-form-item label="商品描述" prop="description">
        	<el-input v-model="formLabelAlign.description"></el-input>
        </el-form-item>

        <el-form-item label="商品属性" prop="attribute" >
          <el-row class="updateRow">
              <el-input placeholder="请输入内容" v-model="formLabelAlign.attribute.brand" >
                <template slot="prepend">品牌</template>
              </el-input>
          </el-row>
          <el-row class="updateRow">
              <el-input placeholder="请输入内容"  v-model="formLabelAlign.attribute.place">
                <template slot="prepend">产地</template>
              </el-input>
          </el-row>
          <el-row class="updateRow">
              <el-input placeholder="请输入内容"  v-model="formLabelAlign.attribute.size">
                <template slot="prepend">型号</template>
              </el-input>
          </el-row>
          <el-row class="updateRow">
              <el-input placeholder="请输入内容"  v-model="formLabelAlign.attribute.production_date">
                <template slot="prepend">生产日期</template>
              </el-input>
          </el-row>
          <el-row class="updateRow">
              <el-input placeholder="请输入内容"  v-model="formLabelAlign.attribute.shelf_life">
                <template slot="prepend">保质期</template>
              </el-input>
          </el-row>
          <el-row class="updateRow">
              <el-input placeholder="请输入内容"  v-model="formLabelAlign.attribute.storage_method">
                <template slot="prepend">存储方式</template>
              </el-input>
          </el-row>

        </el-form-item>


				<div id="btn">
					<button type="button" class="btn" @click="addProduct('ruleForm')">添加</button>
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
				formLabelAlign: {
          attribute:{
            brand:'',
            place:'',
            size:'',
            production_date:'',
            shelf_life:'',
            storage_method:''
          }
				},
				rules: {
					title: [{
						required: true,
						message: '请输入商品名称',
						trigger: 'blur'
					}, ],
					category_id: [{
						required: true,
						message: '请选择分类',
						trigger: 'blur'
					}],
          price: [{
          	required: true,
          	message: '请输入价格',
          	trigger: 'blur'
          }],
          stock: [{
          	required: true,
          	message: '请输入库存',
          	trigger: 'blur'
          }],
          description: [{
          	required: true,
          	message: '请输入商品描述',
          	trigger: 'blur'
          }],
					picture: [{
						required: true,
						message: '请添加图片',
						trigger: 'blur'
					}],
          attribute:[{
						required: true,
						message: '此项不能为空',
						trigger: 'blur'
					}

          ]
				},
				random_Num:[],

        category_list:[],
        checkedCategory:'',

        uploadPicture:'',
        uploadPics:[],


			};
		},
		mounted() {
      var that=this
      this.axios.get('https://grouponapi.top/api/basic/category/list').then(res=>{
        res.data.forEach(function(item,index){
          var options={};
          options.label=item.name;
          options.value=item.id;
          options.children=[];
          item.children.forEach(function(innerItem){
              var options_child={};
              options_child.label=innerItem.name;
              options_child.value=innerItem.id;
              options.children.push(options_child)
          })
          that.category_list.push(options)
        })

      })

		},
		methods: {
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


      uploadPicBefore(file,fileList) {
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
      	this.formLabelAlign.pics = fileList;

      },
      handlePicRemove(file, fileList) {
      	this.formLabelAlign.pics = fileList;
      },


			addProduct(ruleForm) {
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
								let keyValue = "product_picture/picture_" + time + random_Num[0] + random_Num[1] + random_Num[2] + random_Num[3] + '.' + imgType;
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

                    that.uploadPicture=keyValue.split("/")[1]

                    if(that.formLabelAlign.pics!=''){
                      that.formLabelAlign.pics.forEach(function(item,index){
                        that.axios.get('https://grouponapi.top/api/auth/oss/token')
                        .then(res=>{
                            var innerData = res.data;


                            var time= +new Date();
                            var random_Num=[];
                            for(var i=0; i<4; i++){
                              random_Num.push(Math.round(Math.random()*100))
                            }
                            var ossData = new FormData();
                            ossData.append("name", item.raw.name);
                            //获取图片类型 后缀 jpg / png
                            let imgType = item.raw.type.split("/")[1];
                            let filename = item.raw.name; //md5对图片名称进行加密
                            let keyValue = "product_picture/pics_"+index+'_' + time + random_Num[0] + random_Num[1] + random_Num[2] + random_Num[3] + '.' + imgType;
                            //文件名
                            ossData.append("key", keyValue);
                            ossData.append("policy", innerData.policy);
                            ossData.append("OSSAccessKeyId", innerData.accessid);
                            ossData.append("success_action_status", 201);
                            ossData.append("signature", innerData.signature);
                            ossData.append("file", item.raw, item.raw.name);

                            that.axios.post(innerData.host, ossData, {
                                headers: {
                                  "Content-Type": "multipart/form-data"
                                }
                            }).then(res=>{

                              that.uploadPics.push(keyValue.split("/")[1])
 
                              if(index==that.formLabelAlign.pics.length-1){

   

                                that.axios.post('https://grouponapi.top/api/admin/product',{
                                	title: that.formLabelAlign.title,
                                	picture: that.uploadPicture,
                                	pics: JSON.stringify({urls:that.uploadPics}),
                                  category_id:that.formLabelAlign.category_id[1],
                                  price:that.formLabelAlign.price,
                                  stock:that.formLabelAlign.stock,
                                  description:that.formLabelAlign.description,
                                  attribute:JSON.stringify(that.formLabelAlign.attribute),

                                }).then(res => {
                                	that.$message({
                                		message: '添加成功！',
                                		type: 'success'
                                	})
                                	// history.go(0);
                                }).catch(function (error) {
                                	that.$message({
                                		message: error.response.data.message,
                                		type: 'error'
                                	})
                                })

                              }

                            })


                        })

                      })
                    }



                    ///

                    ///


									}
								}).catch(function(error) {
									console.log(error);
								});
							})
						}

					}
				});
			},
      confirmCategory(val){
        this.checkedCategory=val[1];
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
