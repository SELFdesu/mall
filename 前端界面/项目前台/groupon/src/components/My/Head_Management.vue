<!--  -->
<template>
  <div>
      <div class="headMngmt_main">
          <Header selectLi="5"></Header>
          <div class="search_fixed hidden-sm-and-up">
            <el-row class="titleBox">
                <el-col :xs='{span: 2}'>
                    <i class="fa fa-chevron-left" aria-hidden="true" v-on:click="back()"></i>
                </el-col>
                <el-col :xs='{span: 20}'>
                    <p>团长管理</p>
                </el-col>
            </el-row>
          </div>
          <div class="headMngmt_content">
                <el-tabs v-model="activeName" @tab-click="handleClick">
                    <el-tab-pane label="订单列表" name="first">
                        <div>
                            <el-row class="box" v-for="info in dataBox" :key="info.id">
                                <el-row class="essential_info">
                                    <el-col :xs="{ span: 6 }" :sm="{ span: 3 }">
                                        <p>客户ID</p>
                                    </el-col>
                                    <el-col :xs="{ span: 6 }" :sm="{ span: 3 }">
                                        <p>{{ info.user.data.id }}</p>
                                    </el-col>
                                    <el-col :xs="{ span: 6 }" :sm="{ span: 3 }">
                                        <p>客户名称</p>
                                    </el-col>
                                    <el-col :xs="{ span: 6 }" :sm="{ span: 3 }">
                                        <p>{{ info.user.data.username }}</p>
                                    </el-col>
                                    <el-col :xs="{ span: 6 }" :sm="{ span: 3 }">
                                        <p>订单ID</p>
                                    </el-col>
                                    <el-col :xs="{ span: 6 }" :sm="{ span: 3 }">
                                        <p>{{ info.orderDetail.data[0].order_id }}</p>
                                    </el-col>
                                    <el-col :xs="{ span: 6 }" :sm="{ span: 3 }">
                                        <p>订单状态</p>
                                    </el-col>
                                    <el-col :xs="{ span: 6 }" :sm="{ span: 3 }">
                                        <p>{{ info.status_name }}</p>
                                    </el-col>
                                </el-row>
                                <el-row style="background:#ccc" class="prodict_Info" v-for="product in info.orderDetail.data" :key="product.id">
                                    <el-col :xs="{ span: 6 }" :sm="{ span: 3 }" class="Img">
                                        <img :src="product.product.data.picture_url" alt="">
                                    </el-col>
                                    <el-col :xs="{ span: 13 }" :sm="{ span: 11 }" class="productName">
                                        <p>{{ product.product.data.title }}</p>
                                    </el-col>
                                    <el-col :xs="{ span: 5 }" :sm="{ span: 10 }" class="productQuantity">
                                        <p>{{ product.quantity }}</p>
                                    </el-col>
                                </el-row>
                            </el-row>
                        </div>
                    </el-tab-pane>
                    <el-tab-pane label="货物信息列表" name="second">
                        <div>
                            <el-row class="product">
                                <el-row class="productTitle">
                                    <el-col :xs="{ span: 5 }" :sm="{ span: 3 }">商品ID</el-col>
                                    <el-col :xs="{ span: 13 }" :sm="{ span: 13 }">商品信息</el-col>
                                    <el-col :xs="{ span: 6 }" :sm="{ span: 8 }">商品数量</el-col>
                                </el-row>
                                <el-row class="productList" v-for="product in productListBox" :key="product.id">
                                    <el-col :xs="{ span: 2 }" :sm="{ span: 3 }">
                                        <p>{{ product.product_id }}</p>
                                    </el-col>
                                    <el-col :xs="{ span: 6 }" :sm="{ span: 3 }" class="productListImg">
                                        <img :src="product.picture_url" alt="">
                                    </el-col>
                                    <el-col :xs="{ span: 14 }" :sm="{ span: 10 }" class="productListName">
                                        {{ product.title }}
                                    </el-col>
                                    <el-col :xs="{ span: 2 }" :sm="{ span: 8 }">
                                        <p>{{ product.quantity }}</p>
                                    </el-col>
                                </el-row>
                                <el-row class="confirm">
                                    <button class="btn" type="button" :style="'background:'+disabled_backgroundColor" :disabled="disabled_confirm_receipt" @click="confirm_receipt()">确认签收</button>
                                </el-row>
                            </el-row>
                        </div>
                    </el-tab-pane>
                    <el-tab-pane label="收益" name="third">
                        <div class="profit">
                            <p><span>本月预计收益：</span>{{ profit }}</p>
                            <button type="button" @click="profitBtn()">收益详情</button>
                        </div>
                        <el-dialog title="本月收益详情" :fullscreen='true' :close-on-click-modal='false' :visible.sync="profitShow" :before-close="handleClose">
                            <el-table :data="payrollData" height="400" border style="width: 100%">
                                <el-table-column prop="id" label="id" width="80" ></el-table-column>
                                <el-table-column prop="payment_method" label="打款方式"></el-table-column>
                                <el-table-column prop="payment_account" label="打款账号"></el-table-column>
                                <el-table-column prop="salary" label="收益"></el-table-column>
                                <el-table-column prop="date" label="日期"></el-table-column>
                                <el-table-column prop="status" label="状态" ></el-table-column>
                            </el-table>
                            <!-- 工资条分页 -->
                            <el-pagination
                                    id="pagination"
                                    @current-change="payrollPageChange"
                                    :page-size="payrollPagination.per_page"
                                    layout="prev, pager, next, jumper"
                                    :total="payrollPagination.total">
                            </el-pagination>
                        </el-dialog>
                    </el-tab-pane>

                    <el-tab-pane label="设置" name="fourth">
                        <div class="setUp">
                            <p>请选择修改信息或辞去身份</p>
                            <button type="button" @click="modify_info()">申请修改身份信息</button><br>
                            <button type="button" @click="resignation()">{{ Msg }}</button>
                        </div>
                        <el-dialog :title="dialogTitle" :fullscreen='fullscreen' :width="width" :close-on-click-modal='false' :visible.sync="dialogVisible" :before-close="handleClose">

                            <!-- 修改身份信息 -->
                            <div class="modify_info" v-show="modify">
                                <div>
                                    <el-form label-position="top" label-width="80px" :rules="rules" ref="ruleForm" :model="formLabelAlign">
                                        <el-form-item label="真实姓名" prop="name">
                                            <el-input v-model.trim="formLabelAlign.name" placeholder="请输入真实姓名"></el-input>
                                        </el-form-item>
                                        <el-form-item label="联系方式" prop="contact">
                                            <el-input v-model.trim="formLabelAlign.contact" placeholder="请输入手机号"></el-input>
                                        </el-form-item>
                                        <el-form-item label="身份证号" prop="idNum">
                                            <el-input v-model.trim="formLabelAlign.idNum" placeholder="请输入身份证号"></el-input>
                                        </el-form-item>
                                        <el-form-item label="身份" prop="radio">
                                            <el-radio-group v-model="formLabelAlign.radio">
                                                <el-radio label="0">便利店店长</el-radio>
                                                <el-radio label="1">其他</el-radio>
                                            </el-radio-group>
                                        </el-form-item>
                                        <el-form-item label="店铺名称/自提点名称" prop="storeName">
                                            <el-input v-model.trim="formLabelAlign.storeName" placeholder="请输入店铺名称/自提点名称"></el-input>
                                        </el-form-item>
                                        <el-form-item label="地址" prop="address">
                                            <el-input v-model="formLabelAlign.address" v-if="addres_show" disabled></el-input>
                                            <el-cascader v-else clearable show-all-levels  :props="props" style="width:100%;" @change="handleChange" size='medium'></el-cascader>
                                            <button type="button" v-show="addres_showBtn" class="addres_showBtn" @click="XGaddres">点击修改地址</button>
                                        </el-form-item>
                                        <el-form-item label="详细地址" prop="storeAddress">
                                            <el-input v-model.trim="formLabelAlign.storeAddress" placeholder="请输入详细地址"></el-input>
                                        </el-form-item>
                                        <el-form-item label="收款方式" prop="payment_method">
                                            <el-input v-model.trim="formLabelAlign.payment_method" placeholder="请输入收款方式"></el-input>
                                        </el-form-item>
                                        <el-form-item label="收款账户" prop="payment_account">
                                            <el-input v-model.trim="formLabelAlign.payment_account" placeholder="请输入收款账户"></el-input>
                                        </el-form-item>
                                        <el-form-item label="店铺图片（1 张）" prop="storeImg">
                                            <div v-if="storeImg_show">
                                                <img class="storeImg_showImg" :src="formLabelAlign.storeImg" alt=""><br>
                                                <button type="button" class="storeImgBtn" @click="storeImgBtn">点击更改图片</button>
                                            </div>
                                            <el-upload v-else
                                                action=""
                                                :limit="1"
                                                list-type="picture-card"
                                                :on-remove="handleRemove"
                                                :auto-upload="false"
                                                :on-change="uploadBefore"
                                                :show-file-list="true">
                                                <i class="el-icon-plus"></i>
                                            </el-upload>
                                            <el-button slot="trigger" size="small" type="primary">选取</el-button>
                                        </el-form-item>
                                    </el-form>
                                </div>
                                <div class="footerBtn">
                                    <span slot="footer" class="dialog-footer">
                                        <el-button @click="dialogVisible = false" class="cancelBtn">取 消</el-button>
                                        <el-button type="primary" @click="refundModify('ruleForm')" class="refundBtn">确 定</el-button>
                                    </span>
                                </div>
                            </div>


                            <!-- 辞去团长身份 -->
                            <div class="textarea" v-show="textarea">
                                <p>辞去原因</p>
                                <textarea v-model="textareaVal" name="" id=""></textarea><br>
                                <span slot="footer" class="dialog-footer">
                                    <el-button @click="dialogVisible = false" class="cancelBtn">取 消</el-button>
                                    <el-button type="primary"  v-on:click="resignationRefund()" class="refundBtn">确 定</el-button>
                                </span>
                            </div>
                        </el-dialog>
                    </el-tab-pane>
                </el-tabs>
          </div>
      </div>
  </div>
</template>

<script>
import Header from '../conn/header/Header'
export default {
    name:'headManagement',
    components: {
        Header
    },
  data() {
    return {
        disabled_confirm_receipt:false,
        disabled_backgroundColor:'',
        profit: '',
        profitShow:false,
        payrollData:[],
        payrollPagination:'',
        activeName: 'first',
        dataBox:'',
        productListBox:'',
        dialogVisible:false,
        dialogTitle:'',
        fullscreen:'',
        width:'',
        textarea:false,
        textareaVal:'',
        modify:false,
        addres_show:true,
        addres_showBtn:true,
        storeImg_show:true,
        flag: false,
        storePic:'',
        addressId:'',
        addressFlag:false,
        Msg:'申请辞去团长身份',
        formLabelAlign: {
          name: '',
          contact: '',
          idNum:'',
          radio:'',
          storeName:'',
          address:'',
          storeAddress:'',
          payment_method:'',
          payment_account:'',
          storeImg:''
        },
        props: {
            lazy: true,
            lazyLoad: this.lazyLoad
        },
        rules: {
          name: [
            { pattern: /^[\u0391-\uFFE5A-Za-z]+$/, required: true, message: '请输入正确的真实姓名', trigger: 'blur' },
            { min: 2, message: '最少 2 个字符', trigger: 'blur' }
          ],
          idNum: [
            { pattern:/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/, required: true, message: '请输入正确的身份证号', trigger: 'blur' }
          ],
          radio: [
            { required: true, message: '请选择身份', trigger: 'change' }
          ],
          address: [
            { required: true, message: '请选择地址', trigger: 'change' }
          ],
          storeAddress: [
            { required: true, message: '请输入详细地址', trigger: 'blur' }
          ],
          payment_method: [
            { required: true, message: '请输入收款方式', trigger: 'blur' }
          ],
          payment_account: [
            { required: true, message: '请输入收款账户', trigger: 'blur' }
          ],
          storeImg: [
            { required: true, message: '请添加 1 张店铺图片', trigger: 'change' }
          ]
        }
    };
  },
  mounted() {
      document.querySelector('body').setAttribute('style', 'background-color:#e7f0e6');

      let that=this;
      this.axios.get('https://grouponapi.top/api/head/order/list?include=user,orderDetail.product')
      .then(res => {

          that.dataBox=res.data.data;

      })
  },
  methods: {
      confirm_receipt(){
          let that=this;
          this.$confirm('是否确认签收?', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          this.axios.patch('https://grouponapi.top/api/head/cargo/signfor')
          .then(res => {
              that.$message({
                  message:'已成功签收！',
                  type: 'success'
              })
              history.go(0);
          }).catch(function(error){
              that.$message({
                  message:error.response.data.message,
                  type: 'error'
              })
          })
        }).catch(() => {
          this.$message({
            type: 'info',
            message: '已取消签收'
          });
        });

      },
      handleClick(tab, event) {
        let that=this;
        if(tab.name == 'second'){
            this.axios.get('https://grouponapi.top/api/head/cargo/list')
            .then(res => {
                that.productListBox=res.data;
                if(that.productListBox.length == 0){
                    that.disabled_confirm_receipt=true;
                    that.disabled_backgroundColor="#ccc";
                }else{
                    that.disabled_confirm_receipt=false;
                    that.disabled_backgroundColor="";
                }
            }).catch(function(error){
                console.log(error);
            })
        }
        if(tab.name == 'third'){
            this.axios.get('https://grouponapi.top/api/head/forecast/earnings')
            .then(res => {
                that.profit=res.data.amount;
            }).catch(function(error){
                console.log(error);
            })
        }
        if(tab.name == 'fourth'){
            this.axios.get('https://grouponapi.top/api/head/apply/status')
            .then(res => {

                if(res.data.status == 0){
                    that.Msg="已拒绝，从新提交申请";
                }else if(res.data.status == 1){
                    that.Msg="申请辞去团长身份";
                }else if(res.data.status == 2){
                    that.Msg="正在处理中......";
                }
            }).catch(function(error){
                console.log(error);
            })
        }
      },
      handleClose(done){
        this.$confirm('确认关闭？',{
            width:'50%',
            center: true
        })
          .then(_ => {
            done();
          })
          .catch(_ => {});
      },
      profitBtn(){
          let that=this;
          this.profitShow=true;
          this.axios.get('https://grouponapi.top/api/head/past/earnings')
          .then(res => {

              that.payrollData=res.data.data;
              that.payrollPagination=res.data.meta.pagination;
          }).catch(function(error){
              console.log(error);
          })
      },
      payrollPageChange(page){
          this.axios.get('https://grouponapi.top/api/head/past/earnings?page='+page)
          .then(res => {
              that.payrollData=res.data.data;
              that.payrollPagination=res.data.meta.pagination;
          }).catch(function(error){
              console.log(error);
          })
      },
      modify_info(){
          let that=this;
          this.dialogVisible=true;
          this.dialogTitle="申请修改身份信息";
          this.fullscreen=true;
          this.width="0";
          this.textarea=false;
          this.modify=true;
          this.axios.get('https://grouponapi.top/api/head/headinfo/message')
          .then(res => {

              that.formLabelAlign.name=res.data.applicant;
              that.formLabelAlign.contact=res.data.tel;
              that.formLabelAlign.idNum=res.data.id_number;
              that.formLabelAlign.radio=res.data.identity+'';
              that.formLabelAlign.storeName=res.data.store_name;
              that.formLabelAlign.address=res.data.address_name;
              that.addressId=res.data.address;
              that.formLabelAlign.storeAddress=res.data.store_address;
              that.formLabelAlign.payment_method=res.data.payment_method;
              that.formLabelAlign.payment_account=res.data.payment_account;
              that.formLabelAlign.storeImg=res.data.pic_url;
              that.storePic=res.data.pic;
          })
      },
      XGaddres(){
          this.addres_show=false;
          this.addres_showBtn=false;
      },
      storeImgBtn(){
          this.storeImg_show=false;
          this.flag=true;
      },
      uploadBefore(file) {
        //上传文件之前校验图片格式和大小
        const isJPG =
            file.raw.type === "image/jpeg" ||
            file.raw.type === "image/png" ||
            file.raw.type === "image/gif" ||
            file.raw.type === "image/jpg";
        const isLt2M = file.size / 1024 / 1024 < 2;
        if (!isJPG) {
            this.$message.error("上传图片只能是 JPG、JPEG、PNG、GIF 格式!");
            return false;
        }
        if (!isLt2M) {
            this.$message.error("上传图片大小不能超过2MB!");
            return false;
        }
        this.formLabelAlign.storeImg=file;
      },
      handleRemove(file, fileList) {
        this.formLabelAlign.storeImg=fileList;
      },
      lazyLoad(node, resolve) {
      const { level } = node;
      setTimeout(() => {
        // 第一级
        if (node.level == 0) {
          // Ajax请求数据，填充选择框
          this.axios
            .get("https://grouponapi.top/api/basic/region/list").then(response => {
              const nodes = response.data.map((item, index) => ({
                value: item.id,
                label: item.name,
                leaf: node.level >= 2
              }));
              resolve(nodes);
            });
        }
        // 第二级
        if (node.level == 1) {
          // Ajax请求数据，填充选择框，传递上一级参数
          this.axios.get("https://grouponapi.top/api/basic/region/list?pid=" + node.value)
            .then(response => {
              const nodes = response.data[0].children.map((item, index) => ({
                value: item.id,
                label: item.name,
                leaf: node.level >= 2
              }));
              resolve(nodes);
            });
        }
        // 第三级
        if (node.level == 2) {
          // Ajax请求数据，填充选择框，传递上一级参数
          this.axios.get("https://grouponapi.top/api/basic/region/list?pid=" + node.value)
            .then(response => {
              if(response.data[0].children.length == 0){
                var nodes = [{
                  value: node.value,
                  label: node.label,
                  leaf: node.level >= 2
                }]
              }else{
                var nodes = response.data[0].children.map((item, index) => ({
                  value: item.id,
                  label: item.name,
                  leaf: node.level >= 2
                }));
              }
              resolve(nodes);
            });
        }
      }, 100);
    },
    handleChange(value) {

        this.formLabelAlign.address=value[2];
        this.addressFlag=true;
    },
    refundModify(ruleForm){
        var that=this;
        this.$refs[ruleForm].validate((valid) => {
          if (!valid) {
            return false;
          } else {
            if(!this.addressFlag){
                that.formLabelAlign.address=that.addressId;
            }
            if(this.flag){
                if(this.formLabelAlign.storeImg != ''){
                    that.axios.get('https://grouponapi.top/api/auth/oss/token')
                    .then(res => {

                        var data = res.data;


                        var ossData = new FormData();
                        ossData.append("name", that.formLabelAlign.storeImg.raw.name);
                        //获取图片类型 后缀 jpg / png
                        let imgType = that.formLabelAlign.storeImg.raw.type.split("/")[1];
                        let filename = that.formLabelAlign.storeImg.raw.name; //md5对图片名称进行加密
                        let keyValue = "delivery/" + that.dataBox.id+ '.' + imgType;
                        //文件名
                        ossData.append("key", keyValue);
                        ossData.append("policy", data.policy);
                        ossData.append("OSSAccessKeyId", data.accessid);
                        ossData.append("success_action_status", 201);
                        ossData.append("signature", data.signature);
                        ossData.append("file", that.formLabelAlign.storeImg.raw, that.formLabelAlign.storeImg.raw.name);

                        that.axios.post(data.host, ossData, {
                            headers: {
                            "Content-Type": "multipart/form-data"
                            }
                        })
                        .then(res => {
                            if (res.status == 201) {
                                that.storePic=(keyValue.split("/")[1]);
                                that.axios.post('https://grouponapi.top/api/head/apply/modify/headinfo',{
                                    applicant: that.formLabelAlign.name,
                                    tel: that.formLabelAlign.contact,
                                    id_number: that.formLabelAlign.idNum,
                                    identity: that.formLabelAlign.radio,
                                    store_name: that.formLabelAlign.storeName,
                                    address: that.formLabelAlign.address,
                                    store_address: that.formLabelAlign.storeAddress,
                                    payment_method: that.formLabelAlign.payment_method,
                                    payment_account: that.formLabelAlign.payment_account,
                                    pic: that.storePic
                                }).then(res => {
                                    that.$message({
                                        message: '您的申请正在处理中，请耐心等待！',
                                        type: 'success'
                                    })
                                    that.modify=false;
                                    that.dialogVisible=false;
                                    history.go(0);
                                    that.activeName='fourth';
                                }).catch(function (error) {
                                
                                    that.$message({
                                        message: error.response.data.message,
                                        type: 'error'
                                    })
                                })
                            }
                        }).catch(function (error) {
                            console.log(error);
                        });
                    })
                }
            }else{
              that.axios.post('https://grouponapi.top/api/head/apply/modify/headinfo',{
                  applicant: that.formLabelAlign.name,
                  tel: that.formLabelAlign.contact,
                  id_number: that.formLabelAlign.idNum,
                  identity: that.formLabelAlign.radio,
                  store_name: that.formLabelAlign.storeName,
                  address: that.formLabelAlign.address,
                  store_address: that.formLabelAlign.storeAddress,
                  payment_method: that.formLabelAlign.payment_method,
                  payment_account: that.formLabelAlign.payment_account,
                  pic: that.storePic
              }).then(res => {
                  that.$message({
                      message: '您的申请正在处理中，请耐心等待！',
                      type: 'success'
                  })
                  that.modify=false;
                  that.dialogVisible=false;
                  history.go(0);
                  that.activeName='fourth';
              }).catch(function (error) {
              
                  that.$message({
                      message: error.response.data.message,
                      type: 'error'
                  })
              })
            }
          }
        });
      },
      resignation(){
          this.dialogVisible=true;
          this.dialogTitle="申请辞去团长身份";
          this.fullscreen=false;
          this.width="100%";
          this.modify=false;
          this.textarea=true;
      },
      resignationRefund(){
          let that=this;
          if(this.textareaVal != ''){
              this.$confirm('确定辞去团长身份吗？', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
              }).then(() => {
                that.axios.post('https://grouponapi.top/api/head/resign',{
                    cause: that.textareaVal
                }).then(res => {
                    that.$message({
                        type: 'success',
                        message: '您的申请正在处理中，请耐心等待！'
                    });
                    that.textarea=false;
                    that.dialogVisible=false;
                    history.go(0);
                    that.activeName='fourth';
                }).catch(function (error) {
                    that.$message({
                        type: 'error',
                        message: error.response.data.message
                    });
                })
              }).catch(() => {
                this.$message({
                    type: 'info',
                    message: '已取消'
                });
              });
          }else{
              this.$message({
                  message:'辞去原因不能为空',
                  type:'error'
              })
          }

      },
      back(){
        this.$router.back();
      }
  }
};
</script>
<style scoped>
/* @import url(); 引入css类 */
.headMngmt_main{
    width: 100%;
}
.headMngmt_content{
    width: 80%;
    margin: auto;
    padding: 0 0.5%;
    background-color: #fff;
}
/deep/ .el-tabs__item{
    font-size: 16px;
    font-weight: bold;
}
.box{
    margin-top: 1%;
}
.box:first-child{
    margin-top: 0;
}
.essential_info{
    border: 2px solid #2a9b00;
    text-align: center;
}
.essential_info div:nth-child(odd){
    background-color: #f0f0f0;
    font-size: 17px;
    font-weight: bold;
    padding: 0.5% 0;
}
.essential_info div:nth-child(even){
    padding: 0.5% 0;
    border-right: 1px solid #2a9b00;
}
.prodict_Info{
    border: 1px solid #2a9b00;
    border-top: none;
    text-align: center;
    padding: 1%;
}
.prodict_Info div:nth-child(2){
    text-align: left;
}
.prodict_Info img{
    width: 100%;
}
.productName{
    padding: 0 1%;
    font-size: 18px;
}
.productQuantity{
    padding: 5% 0;
    font-size: 20px;
}
.productTitle{
    font-size: 18px;
    text-align: center;
    border: 2px solid #2a9b00;
}
.productTitle div{
    padding: 0.5%;
}
.productTitle div{
    border-right: 1px solid #2a9b00;
}
.productTitle div:last-child{
    border-right: none;
}
.productList{
    border: 1px solid #2a9b00;
    border-top: none;
    background-color: #ccc;
    padding: 1% 0;
    font-size: 18px;
    text-align: center;
}
.productList div:nth-child(3){
    text-align: left;
}
.productListImg{
    padding: 0 1%;
}
.productListImg img{
    width: 100%;
}
.productListName{
    padding-right: 1%;
}
.productList div:first-child,
.productList div:last-child{
    padding: 4% 0;
    font-size: 20px;
}
.confirm{
    text-align: center;
}
.confirm .btn{
    margin: 2%;
    width: 15%;
    padding: 0.5% 0;
    font-size: 16px;
    background-color: #2a9b00;
    border: 1px solid #2a9b00;
    color: #fff;
    border-radius: 5px;
    cursor: pointer;
}
.setUp{
    padding: 5% 0;
    text-align: center;
    font-size: 18px;
    font-weight: bold;
}
.setUp button{
    margin-top: 2%;
    width: 25%;
    padding: 0.5% 0;
    background-color: #2a9b00;
    border: 1px solid #2a9b00;
    outline: none;
    color: #fff;
    font-size: 16px;
    border-radius: 5px;
    cursor: pointer;
}
.setUp button:last-child{
    background-color: #f51d1d;
    border: 1px solid #f51d1d;
}
.textarea{
    text-align: center;
    margin-bottom: 3%;
    font-size: 18px;
    color: #000;
}
.textarea textarea{
    width: 100%;
    height: 150px;
    padding: 1%;
    margin: 2% 0;
    outline: none;
    font-size: 16px;
    resize: none;
}
.textarea button{
    width: 20%;
    padding: 2% 0;
    font-size: 16px;
}
.footerBtn{
    text-align:center;
    margin-top: 8%;
}
.footerBtn .cancelBtn{
    width: 25%;
    margin-right: 2.5%;
}
.footerBtn .refundBtn{
    width: 25%;
    margin-left: 2.5%;
}
.addres_showBtn,
.storeImgBtn{
    width: 15%;
    padding: 0.5% 0;
    margin-left: 1%;
    background-color: #fff;
    color: #000;
    border: 1px solid #ccc;
    cursor: pointer;
    border-radius: 5px;
}
.storeImgBtn{
    margin-left: 0;
}
.storeImg_showImg{
    width: 12%;
    height: auto;
    aspect-ratio: 1/1;
}
.profit{
    text-align: center;
    font-size: 20px;
    padding: 5% 0;
}
.profit span{
    font-weight: bold;
}
.profit button{
    margin: 2% 0;
    width: 15%;
    padding: 0.8% 0;
    background-color: #ff5000;
    border: 1px solid #ff5000;
    border-radius: 5px;
    color: #fff;
    font-size: 16px;
    cursor: pointer;
}

@media only screen and (max-width: 767px) {
    .search_fixed{
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 999;
        background-color: #2a9b00;
    }
    .titleBox{
        width: 100%;
        height: 36px;
        line-height: 36px;
        margin:5px auto;
        font-size: 20px;
        font-weight: bold;
        color: #fff;
        text-align: center;
    }
    .headMngmt_content{
        width: 96%;
        margin: 12.5% auto;
        padding: 0;
    }
    .productTitle{
        font-size: 18px;
        text-align: center;
        border: 2px solid #2a9b00;
    }
    .productTitle div{
        padding: 1% 0;
    }

    .box{
        margin-top: 2%;
    }
    .essential_info div:nth-child(odd){
        font-size: 16px;
    }
    .essential_info div:nth-child(even){
        border-right: none;
        padding: 1%;
    }
    .prodict_Info{
        padding: 1% 1% 0 1%;
    }
    .productName{
        padding: 0 2%;
    }
    .productList div:first-child,
    .productList div:last-child{
        padding: 8% 0;
    }
    .confirm .btn{
        width: 40%;
        margin: 5%;
        padding: 2% 0;
    }
    .profit{
        font-size: 18px;
        padding: 0;
    }
    .profit button{
        margin: 5% 0 10%;
        width: 25%;
        padding: 0.8% 0;
    }
    .setUp{
        padding: 0 0 10% 0;
    }
    .setUp button{
        margin-top: 5%;
        width: 50%;
        padding: 2% 0;
    }
    .setUp button:last-child{
        background-color: #f51d1d;
        border: 1px solid #f51d1d;
    }
    .addres_showBtn,
    .storeImgBtn{
        width: 35%;
        padding: 1.5% 0;
        margin-left: 0;
    }
}
</style>
