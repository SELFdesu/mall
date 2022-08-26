<!--  -->
<template>
  <div>
      <div class="my_main">
          <Header selectLi="2"></Header>
          <MobileMenu selectLi="4"></MobileMenu>
          <div class="my_content">
              <div class="product">
                  <el-row>
                    <el-col :xs="{ span:24 }" :sm="{ span:8 }" class="userBox">
                        <router-link to="">
                            <el-col :xs="{ span:5 }" :sm="{ span:8 }" class="head_portrait">
                                <img :src="dataBox.avatar_url" alt="">
                            </el-col>
                            <el-col :xs="{ span:19 }" :sm="{ span:16}" class="user_name">
                                {{ dataBox.username}}
                            </el-col>
                        </router-link>
                    </el-col>
                    <el-col :sm="{ span:15, offset:1 }" class="right">
                        <div class="display">
                          <el-badge class="item" is-dot="true" :hidden="!tipFlag[1]">
                            <router-link to="/my/order?tabName=unpaid">
                                <div>
                                    <i class="fa fa-credit-card-alt"></i>
                                </div>
                                <p>待付款</p>
                            </router-link>
                          </el-badge>
                        </div>
                        <div class="display">
                          <el-badge  class="item" is-dot="true" :hidden="!tipFlag[2]">
                            <router-link to="/my/order?tabName=waitdeliver">
                                <div>
                                    <i class="fa fa-archive"></i>
                                </div>
                                <p>待发货</p>
                            </router-link>
                          </el-badge>
                        </div>
                        <div class="display">
                          <el-badge class="item" is-dot="true" :hidden="!tipFlag[4]">
                            <router-link to="/my/order?tabName=waitpick">
                                <div>
                                    <i class="fa fa fa-truck"></i>
                                </div>
                                <p>待签收</p>
                            </router-link>
                          </el-badge>
                        </div>
                        <div class="display">
                          <el-badge class="item" is-dot="true" :hidden="!tipFlag[5]">
                            <router-link to="/my/order?tabName=waitcomment">
                                <div>
                                    <i class="fa fa-commenting-o"></i>
                                </div>
                                <p>待评价</p>
                            </router-link>
                          </el-badge>
                        </div>
                        <div class="display">
                            <router-link to="/my/order?tabName=salesreturn">
                                <div>
                                    <i class="fa fa-money"></i>
                                </div>
                                <p>已退货商品</p>
                            </router-link>
                        </div>
                    </el-col>
                  </el-row>
                  <el-row class="operation">
                    <el-col class="operationBox">
                        <div class="operation_i">
                            <router-link to="/pickup/list">
                                <div>
                                    <i class="fa fa-shopping-basket"></i>
                                </div>
                                <p>自提点管理</p>
                            </router-link>
                        </div>
                        <div class="operation_i">
                            <a href="javascript:void(0);" v-on:click="avatarChange()">
                                <div>
                                    <i class="fa fa-user-plus "></i>
                                </div>
                                <p>修改头像</p>
                            </a>
                        </div>
                        <div class="operation_i" v-if="userRole" >
                            <a href="javascript:void(0);" v-on:click="becomeHead()">
                                <div>
                                    <i class="fa fa-plus-circle"></i>
                                </div>
                                <p>成为团长</p>
                            </a>
                        </div>
                        <div class="operation_i" v-else>
                            <a href="javascript:void(0);" @click="headManagement()">
                                <div>
                                    <i class="fa fa-group"></i>
                                </div>
                                <p>团长管理</p>
                            </a>
                        </div>
                        <div class="operation_i" v-show="tel">
                            <a href="javascript:void(0);" v-on:click="bindTel()">
                                <div>
                                    <i class="fa fa-mobile"></i>
                                </div>
                                <p>绑定手机号</p>
                            </a>
                        </div>
                        <div class="operation_i" v-show="email">
                            <a href="javascript:void(0);" v-on:click="bindEmail()">
                                <div>
                                    <i class="fa fa-envelope"></i>
                                </div>
                                <p>绑定邮箱</p>
                            </a>
                        </div>
                        <div class="operation_i">
                            <a href="javascript:void(0);" v-on:click="Exit()">
                                <div>
                                    <i class="fa fa-sign-out"></i>
                                </div>
                                <p>退出登录</p>
                            </a>
                        </div>
                    </el-col>
                  </el-row>
              </div>
              <div id="calendar" class="hidden-sm-and-down">
				<div class="calTitle">我的日历</div>
				<div id="allDate">
					<div class="week">
						<p id="weekInner"></p>
					</div>
					<div class="date">
						<div id="day"></div>
						<div id="year"></div>
					</div>
				</div>
			  </div>
              <div class="clear"></div>
          </div>
          <el-dialog :title="dialogTitle" :fullscreen='true' :close-on-click-modal='false' :visible.sync="dialogVisible" :before-close="handleClose">
            <!-- 修改头像   -->
            <div class="avatarChange" v-show="avatar">
                <el-row>
                    <el-upload
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
                </el-row>
                <div class="footerBtn">
                    <span slot="footer" class="dialog-footer">
                        <el-button @click="dialogVisible = false" class="cancelBtn">取 消</el-button>
                        <el-button type="primary"  v-on:click="refund()" class="refundBtn">确 定</el-button>
                    </span>
                </div>
            </div>

            <!-- 成为团长 -->
            <div class="become_head" v-show="become_head">
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
                        <el-form-item label="申请人身份" prop="radio">
                            <el-radio-group v-model.trim="formLabelAlign.radio">
                                <el-radio label="0">便利店店长</el-radio>
                                <el-radio label="1">其他</el-radio>
                            </el-radio-group>
                        </el-form-item>
                        <el-form-item label="店铺名称/自提点名称" prop="storeName">
                            <el-input v-model.trim="formLabelAlign.storeName" placeholder="请输入店铺名称/自提点名称"></el-input>
                        </el-form-item>
                        <el-form-item label="申请地址" prop="address">
                            <el-cascader clearable show-all-levels  :props="props" style="width:100%;" @change="handleChange" size='medium'></el-cascader>
                        </el-form-item>
                        <el-form-item label="店铺详细地址/自提点详细地址" prop="storeAddress">
                            <el-input v-model.trim="formLabelAlign.storeAddress" placeholder="请输入店铺详细地址/自提点详细地址"></el-input>
                        </el-form-item>
                        <el-form-item label="店铺图片（1 张）" prop="storeImg">
                            <el-upload
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
                        <el-button type="primary"  v-on:click="refundBecomeHead('ruleForm')" class="refundBtn">确 定</el-button>
                    </span>
                </div>
            </div>

            <!-- 绑定手机号 -->
            <div class="bind_tel" v-show="bind_tel">
                <el-row>
                    <input type="text" v-model.trim="telVal" placeholder="请输入绑定手机号" autocomplete="off">
                    <input type="text" v-model.trim="codeVal" placeholder="请输入绑定手机验证码" autocomplete="off"><br>
                    <button type="button" class="send" @click="sendTelCode()" :disabled="disabled" >{{ telTime }}{{ s }}</button>
                </el-row>
                <div class="footerBtn">
                    <span slot="footer" class="dialog-footer">
                        <el-button @click="dialogVisible = false" class="cancelBtn">取 消</el-button>
                        <el-button type="primary"  v-on:click="refundBindTel()" class="refundBtn">确 定</el-button>
                    </span>
                </div>
            </div>
            <!-- 绑定邮箱 -->
            <div class="bind_tel" v-show="bind_email">
                <el-row>
                    <input type="email" v-model.trim="emailVal" placeholder="请输入绑定邮箱账号" autocomplete="off">
                    <input type="text" v-model.trim="emailCode" placeholder="请输入绑定邮箱验证码" autocomplete="off"><br>
                    <button type="button" class="send" @click="sendEmailCode()" :disabled="disabled" >{{ emailTime }}{{ s }}</button>
                </el-row>
                <div class="footerBtn">
                    <span slot="footer" class="dialog-footer">
                        <el-button @click="dialogVisible = false" class="cancelBtn">取 消</el-button>
                        <el-button type="primary"  v-on:click="refundBindEmail()" class="refundBtn">确 定</el-button>
                    </span>
                </div>
            </div>

          </el-dialog>

          <el-dialog title="请补全收款信息，否则会影响以后的收益" :close-on-click-modal='false' :visible.sync="head_management" :show-close="false">
            <!-- 团长管理 -->
            <div>
                <el-form label-position="top" label-width="80px" :model="formLabelAlign">
                    <el-form-item label="收款方式">
                        <el-input v-model.trim="payment_method"></el-input>
                    </el-form-item>
                    <el-form-item label="收款账户">
                        <el-input v-model.trim="payment_account"></el-input>
                    </el-form-item>
                </el-form>
                <div class="footerBtn">
                    <span slot="footer" class="dialog-footer">
                        <el-button type="primary"  v-on:click="refundHeadManagement()" class="refundBtn">确 定</el-button>
                    </span>
                </div>
            </div>

          </el-dialog>
      </div>
  </div>
</template>

<script>
import Header from '../conn/header/Header'
import MobileMenu from '../conn/mobileMenu/menu'
export default {
    Home: 'My',
    components: {
        Header,
        MobileMenu
    },
  data() {
    return {
        tipFlag:[],
        dataBox:'',
        userRole: true,
        tel: false,
        email: true,
        dialogVisible: false,
        avatar: false,
        dialogTitle:'',
        pics:'',
        bind_tel:false,
        telVal:'',
        codeVal:'',
        disabled: false,
        telTime: '发送手机验证码',
        s:'',
        bind_email: false,
        emailTime: '发送邮箱验证码',
        emailVal:'',
        emailCode:'',
        become_head:false,
        formLabelAlign: {
          name: '',
          contact: '',
          idNum:'',
          radio:'',
          storeName:'',
          address:'',
          storeAddress:'',
          storeImg:''
        },
        head_management: false,
        payment_method:'',
        payment_account:'',
        props: {
            lazy: true,
            lazyLoad: this.lazyLoad
        },
        rules: {
          name: [
            { pattern: /^[\u0391-\uFFE5A-Za-z]+$/, required: true, message: '请输入正确的真实姓名', trigger: 'blur' },
            { min: 2, message: '最少 2 个字符', trigger: 'blur' }
          ],
          contact: [
            { pattern:/^((0\d{2,3}-\d{7,8})|(1[34578]\d{9}))$/, required: true, message: '请输入正确的手机号', trigger: 'blur' }
          ],
          idNum: [
            { pattern:/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/, required: true, message: '请输入正确的身份证号', trigger: 'blur' }
          ],
          radio: [
            { required: true, message: '请选择申请人身份', trigger: 'change' }
          ],
          storeName: [
            { required: true, message: '请输入店铺名称或自提点名称', trigger: 'blur' }
          ],
          address: [
            { required: true, message: '请选择申请地址', trigger: 'change' }
          ],
          storeAddress: [
            { required: true, message: '请输入店铺详细地址或自提点详细地址', trigger: 'blur' }
          ],
          storeImg: [
            { required: true, message: '请添加 1 张店铺图片', trigger: 'change' }
          ]
        }
    };

  },
  created(){
    var that=this
    this.axios.get('https://grouponapi.top/api/userorder?type=all').then(res=>{

        res.data.data.forEach(function(item){
            if(item.status!=99){
              that.tipFlag[item.status]=true
            }
        })

    }).catch(function(error){
          that.$message({
            type: 'error',
            message: '订单信息加载失败！'
          });
    })
  },
  mounted() {
      document.querySelector('body').setAttribute('style', 'background-color:#e7f0e6');

      let that=this;
      var week=document.getElementById('weekInner');
      var day=document.getElementById('day');
      var year=document.getElementById('year');
      var weekNum;
      var weekStr;
      var yearNum;
      var monthNum;
      var dateStr;

      var date=new Date();
      weekNum=date.getDay();
      switch(weekNum){
        case 0:weekStr='周日';break;
        case 1:weekStr='周一';break;
        case 2:weekStr='周二';break;
        case 3:weekStr='周三';break;
        case 4:weekStr='周四';break;
        case 5:weekStr='周五';break;
        case 6:weekStr='周六';break;
      }
      week.innerText=weekStr;
      day.innerText=date.getDate();
      yearNum=date.getFullYear();
      monthNum=date.getMonth()+1;
      dateStr=yearNum+'年'+monthNum+'月';
      year.innerText=dateStr;

      this.axios.get('https://grouponapi.top/api/auth/me')
      .then(res => {
          that.dataBox=res.data.data;
          if(res.data.data.userRole != "团长"){
              that.userRole=true;
          }else{
              that.userRole=false;
          }

          if(res.data.data.email != null && res.data.data.tel != null){
              that.tel=false;
              that.email=false;
          }else if(res.data.data.tel != null){
              that.tel=false;
              that.email=true;
          }else if(res.data.data.email != null){
              that.tel=true;
              that.email=false;
          }
      })



  },
  methods: {
    Exit(){
        let that=this;
        this.$confirm('确定退出登陆吗?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
        }).then(() => {
            that.axios.post('https://grouponapi.top/api/auth/logout')
            .then(res => {

                that.$message({
                    message:'退出成功！',
                    type: 'success'
                })
                localStorage.removeItem('token');
                that.$router.push('/');
            }).catch(function(error){
                console.log(error);
            })
        }).catch(() => {
            this.$message({
                type: 'info',
                message: '已取消退出登录'
            });
        });
    },
    handleClose(done){
        this.$confirm('确认关闭？')
          .then(_ => {
            done();
          })
          .catch(_ => {});
    },

    avatarChange(){
        this.bind_email=false;
        this.bind_tel=false;
        this.dialogVisible=true;
        this.dialogTitle="更换头像";
        this.avatar=true;

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
        this.pics=file;
        this.formLabelAlign.storeImg=file;
    },
    handleRemove(file, fileList) {
        this.pics=fileList;
        this.formLabelAlign.storeImg=fileList;
    },
    refund(){
        let that=this;
        if(this.pics != ''){
            that.axios.get('https://grouponapi.top/api/auth/oss/token')
            .then(res => {

                var data = res.data;


                var ossData = new FormData();
                ossData.append("name", that.pics.raw.name);
                //获取图片类型 后缀 jpg / png
                let imgType = that.pics.raw.type.split("/")[1];
                let filename = that.pics.raw.name; //md5对图片名称进行加密
                let keyValue = "avatar/" + that.dataBox.id+ '.' + imgType;
                //文件名
                ossData.append("key", keyValue);
                ossData.append("policy", data.policy);
                ossData.append("OSSAccessKeyId", data.accessid);
                ossData.append("success_action_status", 201);
                ossData.append("signature", data.signature);
                ossData.append("file", that.pics.raw, that.pics.raw.name);

                that.axios.post(data.host, ossData, {
                    headers: {
                    "Content-Type": "multipart/form-data"
                    }
                })
                .then(res => {
                    if (res.status == 201) {

                        that.axios.patch('https://grouponapi.top/api/auth/avatar/modify',{
                            avatar: keyValue.split("/")[1]
                        }).then(res => {
                            that.$message({
                                message: '提交成功！',
                                type: 'success'
                            })
                            that.avatar=false;
                            that.dialogVisible = false;
                            history.go(0);
                        }).catch(function(error){

                            that.$message({
                                message: error.response.data.message,
                                type: 'error'
                            })
                        })
                    }
                }).catch(error => {});
            })
        }

    },

    becomeHead(){
        this.avatar=false;
        this.bind_email=false;
        this.bind_tel=false;
        this.dialogVisible=true;
        this.dialogTitle="申请成为团长";
        this.become_head=true;
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
    },
    refundBecomeHead(ruleForm){
        var that=this;
        this.$refs[ruleForm].validate((valid) => {
          if (!valid) {
            return false;
          } else {

            if(this.pics != ''){
                that.axios.get('https://grouponapi.top/api/auth/oss/token')
                .then(res => {

                    var data = res.data;


                    var ossData = new FormData();
                    ossData.append("name", that.pics.raw.name);
                    //获取图片类型 后缀 jpg / png
                    let imgType = that.pics.raw.type.split("/")[1];
                    let filename = that.pics.raw.name; //md5对图片名称进行加密
                    let keyValue = "delivery/" + that.dataBox.id+ '.' + imgType;
                    //文件名
                    ossData.append("key", keyValue);
                    ossData.append("policy", data.policy);
                    ossData.append("OSSAccessKeyId", data.accessid);
                    ossData.append("success_action_status", 201);
                    ossData.append("signature", data.signature);
                    ossData.append("file", that.pics.raw, that.pics.raw.name);

                    that.axios.post(data.host, ossData, {
                        headers: {
                        "Content-Type": "multipart/form-data"
                        }
                    })
                    .then(res => {
                        if (res.status == 201) {

                            that.axios.post('https://grouponapi.top/api/head/apply',{
                                applicant: that.formLabelAlign.name,
                                tel: that.formLabelAlign.contact,
                                id_number: that.formLabelAlign.idNum,
                                identity: that.formLabelAlign.radio,
                                store_name: that.formLabelAlign.storeName,
                                address: that.formLabelAlign.address,
                                store_address: that.formLabelAlign.storeAddress,
                                pic: keyValue.split("/")[1]
                            }).then(res => {
                                that.$message({
                                    message: '您的申请正在处理中，请耐心等待！',
                                    type: 'success'
                                })
                                that.become_head=false;
                                that.dialogVisible=false;
                                history.go(0);
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
          }
        });
    },
    headManagement(){
        let that=this;
        this.axios.get('https://grouponapi.top/api/head/check/payment/term')
        .then(res => {

            if(res.data.status == 1){
                that.$router.push('/head/management')
            }else{
                that.head_management=true;
            }
        })
    },
    refundHeadManagement(){
        let that=this;
        if(this.payment_method != '' && this.payment_amount != ''){
            this.axios.post('https://grouponapi.top/api/head/fill/payment/term',{
                payment_method: that.payment_method,
                payment_account: that.payment_account
            }).then(res => {

                that.$message({
                    message: '已成功补全！',
                    type: 'success'
                })
                that.$router.push('/head/management');
            }).catch(function(error){
                that.$message({
                    message: error.response.data.message,
                    type: 'error'
                })
            })
        }else{
            this.$message({
                message: '收款方式或收款账户为空',
                type: 'error'
            })
        }
    },
    bindTel(){
        this.avatar=false;
        this.become_head=false;
        this.dialogVisible=true;
        this.dialogTitle="绑定手机号";
        this.bind_tel=true;
    },
    sendTelCode(){
        let that=this;
        var numReg = /^[0-9]*$/;
        var numRe = new RegExp(numReg)
        if(this.telVal == "" || this.telVal.length != 11 || !numRe.test(this.telVal)){
            this.$message({
                message: '手机号输入有误！',
                type: 'error'
            })
        }else{
            this.axios.post('https://grouponapi.top/api/auth/bind/phone/send',{
                phone: that.telVal
            }).then(res => {
                that.$message({
                    message: '验证码发送成功！',
                    type: 'success'
                })
                that.disabled=true;
                that.telTime=60;
                that.s='s';
                let interval=setInterval(function(){
                    that.telTime--;
                    if(that.telTime == 0){
                        that.disabled=false;
                        that.s='';
                        that.telTime='重新发送手机验证码';
                        clearInterval(interval);
                    }
                },1000)
            }).catch(function (error) {

                if(error.response.status == 400){
                    that.$message({
                    message: error.response.data.message,
                    type: 'error'
                    })
                }
            })
        }
    },
    refundBindTel(){
        let that=this;
        var numReg = /^[0-9]*$/;
        var numRe = new RegExp(numReg)
        if(this.telVal =="" || this.telVal.length != 11 || !numRe.test(this.telVal)){
            this.$message({
                message: '输入的手机号有误！请重新绑定',
                type: 'error'
            })
        }else{
            this.axios.patch('https://grouponapi.top/api/auth/bind/phone',{
                phone: that.telVal,
                code: that.codeVal
            }).then(res => {

                that.$message({
                    message: '绑定成功！',
                    type: 'success'
                })
                that.bind_tel=false;
                that.dialogVisible=false;
                history.go(0);
            }).catch(function(error){

                if(error.response.status == 422){
                    that.$message({
                        message: '验证码错误！',
                        type: 'error'
                    })
                }else if(error.response.status == 400){
                    that.$message({
                        message: error.response.data.message,
                        type: 'error'
                    })
                }
            })
        }
    },
    bindEmail(){
        this.avatar=false;
        this.become_head=false;
        this.dialogVisible=true;
        this.dialogTitle="绑定邮箱";
        this.bind_email=true;
    },
    sendEmailCode(){
        let that=this;
        var regEmail = /^([a-zA-Z]|[0-9])(\w|\-)+@[a-zA-Z0-9]+\.([a-zA-Z]{2,4})$/;
        if(!regEmail.test(this.emailVal)){
            this.$message({
                message: '邮箱输入有误！',
                type:'error'
            })
        }else{
            this.axios.post('https://grouponapi.top/api/auth/bind/email/send',{
                email: that.emailVal
            }).then(res => {

                that.$message({
                    message: '验证码发送成功！',
                    type: 'success'
                })
                that.disabled=true;
                that.emailTime=60;
                that.s='s';
                let interval=setInterval(function(){
                    that.emailTime--;
                    if(that.emailTime == 0){
                        that.s='',
                        that.emailTime='重新发送邮箱验证码';
                        that.disabled=false;
                        clearInterval(interval);
                    }
                },1000)
            }).catch(function(error){

                if(error.response.status == 400){
                    that.$message({
                    message: error.response.data.message,
                    type: 'error'
                    })
                }
            })
        }
    },
    refundBindEmail(){
        let that=this;
        var regEmail = /^([a-zA-Z]|[0-9])(\w|\-)+@[a-zA-Z0-9]+\.([a-zA-Z]{2,4})$/;
        if(!regEmail.test(this.emailVal)){
            this.$message({
                message: '邮箱输入有误！',
                type: 'error'
            })
        }else{
            this.axios.patch('https://grouponapi.top/api/auth/bind/email',{
                email: that.emailVal,
                code: that.emailCode
            }).then(res => {

                that.$message({
                    message: '邮箱绑定成功',
                    type: 'success'
                })
                that.bind_email=false;
                that.dialogVisible=false;
                history.go(0);
            }).catch(function(error){

                if(error.response.status == 422){
                    that.$message({
                    message: "验证码错误！",
                    type: 'error'
                    })
                }else if(error.response.status == 400){
                    that.$message({
                    message: error.response.data.message,
                    type: 'error'
                    })
                }

            })
        }
    }
  }
};
</script>
<style scoped>
/* @import url(); 引入css类 */
.clear{
    clear: both;
}
.my_main{
    width: 100%;
}
.my_content{
    width: 80%;
    margin: 2% auto;
}
.product{
    float: left;
    width: 70%;
}
.product a{
    color: #000;
    text-decoration: none;
}
.product a:hover p{
    color: #ff5000;
}
.userBox{
    background-color: #fff;
    padding: 0 1%;
    border-radius: 15px;
}
.head_portrait{
    height: 150px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.head_portrait img{
    width: 100%;
    height: auto;
    aspect-ratio: 1/1;
    border-radius: 50px;
}
.user_name{
    padding-left: 2.5%;
    font-size: 20px;
    line-height: 150px;
}
.right{
    display: flex;
    justify-content: space-around;
    background-color: #fff;
    border-radius: 15px;
    margin-left: 2.5%;
    text-align: center;
}
.display{
    height: 150px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.right i{
    font-size: 300%;
}
#calendar {
	border-radius: 15px;
	float: right;
	width: 29.5%;
	height: 445px;
	background-color: white;
}
.calTitle {
	border-radius: 15px 15px 0 0;
	color: white;
	width: 100%;
	height: 13%;
	line-height: 57.85px;
	font-size: 20px;
	font-weight: bolder;
	text-align: center;
	background-color: rgba(0, 0, 0, 0.7);
}
#allDate {
	height: 87%;
	border: 2px solid rgba(0, 0, 0, 0.3);
	border-top: none;
	border-radius: 0 0 15px 15px;
}
.week {
	width: 100%;
	height: 30%;
	border-bottom: 2px solid rgba(0, 0, 0, 0.2);
}
#weekInner {
	color: #E65C5C;
	width: 100%;
	height: 100%;
	text-align: center;
	line-height: 116.15px;
	font-size: 40px;
	font-weight: bolder;
}
.date {
	width: 100%;
	height: 70%;
}
#day {
	color: #99D5EB;
	padding-top: 35px;
	width: 100%;
	height: 40%;
	font-size: 80px;
	text-align: center;
}
#year {
	width: 100%;
	height: 60%;
	font-size: 40px;
	text-align: center;
	margin-top: 0.3em;
	border-radius: 0 0 15px 15px;
}
.operation{
    padding-right: 1.8%;
}
.operationBox{
    margin-top: 3%;
    display: flex;
    flex-direction: row;
    justify-content: space-around;
    text-align: center;
    background-color: #fff;
    border-radius: 15px;
}
.operation i{
    font-size: 300%;
}
.operation_i{
    height: 150px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.footerBtn{
    text-align:center;
    margin-top: 5%;
}
.footerBtn .cancelBtn{
    width: 25%;
    margin-right: 2.5%;
}
.footerBtn .refundBtn{
    width: 25%;
    margin-left: 2.5%;
}
.avatarChange{
    text-align: center;
    margin-top: 5%;
}
/deep/ .el-upload--picture-card {
    width: 100px;
    height: 100px;
}
/deep/ .el-icon-plus {
    font-size: 20px;
    color: #8c939d;
    position: relative;
    top: -22px;
    text-align: center;
}
 /deep/ .el-upload-list__item{
     width: 100px;
     height: 100px;
 }
.bind_tel{
    text-align: center;
}
.bind_tel input{
    width: 50%;
    padding: 2%;
    margin-bottom: 3%;
    outline: none;
    border-radius: 5px;
    font-size: 16px;
    border: 1px solid #ccc;
}
.send{
    width: 55%;
    padding: 1.5%;
    font-size: 16px;
    background-color: #2a9b00;
    border: 1px solid #2a9b00;
    color: #fff;
    border-radius: 5px;
}

/* 对话框 el-dialog */
/deep/ .el-dialog__title{
    font-size: 22px;
    font-weight: bold;
}
@media only screen and (max-width: 767px) {
    .my_content{
        width: 96%;
        margin: 0.5% auto;
    }
    .product{
        float: none;
        width: 100%;
    }
    .userBox{
        padding: 2.5% 1.5%;
        border-radius: 5px;
    }
    .head_portrait{
        height: auto;
    }
    .user_name{
        font-size: 18px;
        line-height: 55px;
    }
    .right{
        border-radius: 5px;
        margin-left: 0;
        margin-top: 3%;
        padding: 5% 0;
    }
    .display{
        height: auto;
    }
    .right i{
        font-size: 180%;
    }
    .operation{
        padding-right: 0;
    }
    .operationBox{
        border-radius: 5px;
        padding: 5% 0;
    }
    .operation i{
        font-size: 180%;
    }
    .operation_i{
        height: auto;
    }
}
</style>
