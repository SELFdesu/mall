<!-- 手机找回密码页面 -->
<template>
  <div>
    <Header></Header>
    <div class="all">
      <div class="update">
          <i class="fa fa-chevron-left chevronLeft" aria-hidden="true" v-on:click="back()"></i>
          <p>邮箱找回密码</p>
          <form action="#" method="post">
                <div class="i">
                    <i class="fa fa-envelope" aria-hidden="true"></i>
                    <input type="email" v-model="mail" name="" id="mail" placeholder="邮箱" required="required" autocomplete="off" />
                </div>
                <div class="check_Code">
                    <input type="text" v-model="code" name="" id="code" placeholder="请输入验证码" required="required" autocomplete="off" />
                    <button type="button" v-on:click="sendMobileSms()" v-bind:disabled="disabled">{{ send_code }}{{ s }}</button>
                </div>
			          <input type="password" v-model="password1" name="" class="pwd" placeholder="新密码（至少8位）" required="required" autocomplete="off" /><br>
                <input type="password" v-model="password2" name="" class="pwd" placeholder="确认新密码" required="required" autocomplete="off" /><br>
                
				
				<div id="bottomText">
					<div class="text01"><router-link class="text_left" to="/login">想起密码？马上登录</router-link></div>
					<div class="text02"><router-link class="text_right" to="/updatepwd/mobile">手机找回</router-link></div>
				</div>
                <div class="clear"></div>
                <input type="button" name="" id="sub" v-on:click="updatePwd" value="立即修改" />
          </form>
      </div>
    </div>
  </div>
</template>

<script>
import Header from '../conn/header/Header'

export default {
  name:'UpdatePasswordMail',
  components: {
    Header
  },
  data() {
    return {
      mail: '',
      code: '',
      disabled: false,
      send_code: '发送验证码',
      s: '',
      password1: '',
      password2: ''

    };
  },
  methods: {
    sendMobileSms(){
      var regEmail = /^([a-zA-Z]|[0-9])(\w|\-)+@[a-zA-Z0-9]+\.([a-zA-Z]{2,4})$/;
      if(!regEmail.test(this.mail)){
        this.$message({
          message: '邮箱格式有误！',
          type: 'error'
        })
      }else{
        let that=this;
        this.axios.post('https://grouponapi.top/api/auth/resetpassword/email/send',{
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
          if(error.response.status == 422){
            that.$message({
              message: '邮箱不存在！',
              type: 'error'
            })
          }
        })
      }
    },
    updatePwd(){
      var regEmail = /^([a-zA-Z]|[0-9])(\w|\-)+@[a-zA-Z0-9]+\.([a-zA-Z]{2,4})$/;
      if(!regEmail.test(this.mail)){
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
          message: '新密码不能为空！',
          type: 'error'
        })
      }else if(this.password1.length < 8){
        this.$message({
          message: '新密码长度不能少于8位！',
          type: 'error'
        })
      }else if(this.password2 == ''){
        this.$message({
          message: '确认新密码不能为空！',
          type: 'error'
        })
      }else if(this.password1 != this.password2){
        this.$message({
          message: '两次输入密码不一样！！',
          type: 'error'
        })
      }else{
        let that=this;
        this.axios.patch('https://grouponapi.top/api/auth/resetpassword/email',{
          email: this.mail,
          code: this.code,
          newpassword: this.password1,
          newpassword_confirmation: this.password2
        }).then(res => {
          that.$message({
            message: '修改成功',
            type: 'success'
          })
          setTimeout(function () { 
            that.$router.push('/login');
          },1000)
        }).catch(function(error) {

          if(error.response.status == 422){
            that.$message({
              message: error.response.data.message,
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
