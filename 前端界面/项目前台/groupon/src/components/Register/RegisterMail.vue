<!-- 邮箱注册页面 -->
<template>
  <div>
    <Header></Header>
    <div class="all">
      <div class="register">
          <i class="fa fa-chevron-left chevronLeft" aria-hidden="true" v-on:click="back()"></i>
          <p>邮箱注册</p>
          <form action="#" method="post">
                <input type="text" v-model="username" name="" id="userName" placeholder="用户名" required="required" autocomplete="off" /><br>
                <div class="i">
                    <i class="fa fa-envelope" aria-hidden="true"></i>
                    <input type="email" v-model="mail" name="" id="mail" placeholder="邮箱" required="required" autocomplete="off" />
                </div>
                
                <div class="check_Code">
                    <input type="text" v-model="code" name="" id="code" placeholder="请输入验证码" required="required" autocomplete="off" />
                    <button type="button" v-on:click="sendMailSms()" v-bind:disabled="disabled">{{ send_code }}{{ s }}</button>
                </div>
			          <input type="password" v-model="password1" name="" class="pwd" placeholder="密码（至少8位）" required="required" autocomplete="off" /><br>
                <input type="password" v-model="password2" name="" class="pwd" placeholder="确认密码" required="required" autocomplete="off" /><br>
                <div id="sex">
                    <span class="sexTxt">性别：</span>
                    <el-radio v-model="radio" label="0">男</el-radio>
                    <el-radio v-model="radio" label="1">女</el-radio>
                </div>
				
                <div id="bottomText">
                  <div class="text01"><router-link class="text_left" to="/login">已有账号？马上登录</router-link></div>
                  <div class="text02"><router-link class="text_right" to="/register/mobile">手机注册</router-link></div>
                </div>
                <div class="clear">
                </div>
                <input type="button" name="" id="sub" v-on:click="register" value="注册" />
          </form>
      </div>
    </div>
  </div>
</template>

<script>
import Header from '../conn/header/Header'

export default {
  name:'RegisterMail',
  components: {
    Header
  },
  data() {
    return {
      username:'',
      mail: '',
      code: '',
      send_code: '发送验证码',
      s: '',
      disabled: false,
      password1: '',
      password2: '',
      radio: '0'
    };
  },
  methods: {
    sendMailSms(){
      var regEmail = /^([a-zA-Z]|[0-9])(\w|\-)+@[a-zA-Z0-9]+\.([a-zA-Z]{2,4})$/;
      if(!regEmail.test(this.mail)){
        this.$message({
          message: '邮箱格式有误！',
          type: 'error'
        })
      }else{
        let that=this;
        this.axios.post('https://grouponapi.top/api/auth/register/email/send',{
          email: this.mail
        }).then(res => {
          that.$message({
            message: '发送成功',
            type: 'success'
          })
          that.send_code=60;
          that.s=' s';
          that.disabled=true;
          let interval = setInterval(function(){
            that.send_code--;
            if(that.send_code == 0){
              that.disabled=false;
              that.s='';
              that.send_code='重新发送验证码';
              clearInterval(interval);
            }
          },1000)
        }).catch(function (error){
          console.log(error);
        })
      }
    },
    register(){
      var regEmail = /^([a-zA-Z]|[0-9])(\w|\-)+@[a-zA-Z0-9]+\.([a-zA-Z]{2,4})$/;
      if(this.username == ''){
        this.$message({
          message: '用户名不能为空！',
          type: 'error'
        })
      }else if(!regEmail.test(this.mail)){
        this.$message({
          message: '邮箱格式有误！',
          type: 'error'
        })        
      }else if(this.code == ''){
        this.$message({
          message: '验证码不能为空！',
          type: 'error'
        })
      }else if(this.password1 == ''){
        this.$message({
          message: '密码不能为空！',
          type: 'error'
        })
      }else if(this.password1.length < 8){
        this.$message({
          message: '密码长度不能少于8位！',
          type: 'error'
        })
      }else if(this.password2 == ''){
        this.$message({
          message: '确认密码不能为空！',
          type: 'error'
        })
      }else if(this.password1 != this.password2){
        this.$message({
          message: '两次输入密码不一样！',
          type: 'error'
        })
      }else{
        let that=this;
        this.axios.post('https://grouponapi.top/api/auth/register/email',{
          username: this.username,
          email: this.mail,
          code: this.code,
          password: this.password1,
          password_confirmation: this.password2,
          sex: this.radio
        }).then(res => {

          that.$message({
            message: '注册成功',
            type: 'success'
          })
          setTimeout(function () { 
            that.$router.push('/login');
          },1000)
        }).catch(function (error) {
          console.log(error);
          if(error.response.data.status_code == 422){
            that.$message({
              message: '验证码错误！',
              type: 'error'
            })
          }
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
@import url('../../assets/css/register.css');
</style>
