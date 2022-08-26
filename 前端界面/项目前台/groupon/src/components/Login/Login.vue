<!-- 登录页面 -->
<template>
  <div>
    <Header selectLi="2"></Header>
    <div class="all">
      <div class="login">
          <i class="fa fa-chevron-left chevronLeft" aria-hidden="true" v-on:click="back()"></i>
          <p>鲜橙团购</p>
          <form action="#" method="post">
            <input type="text" v-model="user" name="" id="user" placeholder="手机号/邮箱" required="required" autocomplete="off" /><br>
			      <input type="password" v-model="pwd" name="" id="pwd" placeholder="密码" required="required" autocomplete="off" /><br>
            <div class="codeImg">
              <input type="text" v-model="code" name="" id="code" placeholder="输入验证码" required="required" autocomplete="off" />
              <img v-bind:src="verifyImg" v-on:click="sendSms()"/>
            </div>
				    <div id="bottomText">
					    <div class="text01"><router-link class="text_left" to="/register/mobile">注册账号</router-link></div>
					    <div class="text02"><router-link class="text_right" to="/updatepwd/mobile">忘记密码</router-link></div>
				    </div>
            <div class="clear"></div>
            <input type="button" name="" id="sub" value="登录" v-on:click="login" />
          </form>
      </div>
    </div>
  </div>
</template>

<script>
import Header from '../conn/header/Header'

export default {
  name: 'Login',
  components: {
    Header
  },
  data() {
    return {
      user:'',
      pwd:'',
      code:'',
      code_key:'',
      verifyImg:''

    };
  },
  methods: {
    sendSms(){
      let that=this;
      let url='https://grouponapi.top/api/auth/captcha';
      this.axios.get(url).then(res => {
        that.code_key=res.data.url.key;
        that.verifyImg=res.data.url.img;
      })
    },
    login(){
      var that=this;
      if(this.user == ''){
        this.$message({
          message:'账号不能为空！',
          type:'error'
        })
      }else if(this.pwd == ''){
        this.$message({
          message:'密码不能为空！',
          type:'error'
        })
      }else if(this.pwd.length < 8){
        this.$message({
          message:'密码长度不能少于8位！',
           type:'error'
        })
      }else if(this.code == ''){
        this.$message({
          message:'验证码不能为空！',
          type:'error'
        })
      }else{
        this.axios.post('https://grouponapi.top/api/auth/login',
        {
          account:this.user,
          password:this.pwd,
          captcha:this.code,
          captcha_key:this.code_key
        }).then(res => {
          if(res.status == 200){
            that.$message({
            message: '登录成功',
            type: 'success'
            })
            localStorage.setItem('token',res.data.token_type+' '+res.data.access_token);
            localStorage.setItem('expires_time',+new Date() + res.data.expires_in);
            this.$router.push('/');
          }
        }).catch(function (error) {
          if(error.response.status == 400){
            that.$message({
              message: error.response.data.message,
              type:'error'
            })
          }else if(error.response.status == 401){
            that.$message({
              message:'密码有误！',
              type:'error'
            })
          }else{
            that.$message({
              message:'登录失败。',
              type:'error'
            })
          }
          let url='https://grouponapi.top/api/auth/captcha';
          that.axios.get(url).then(res => {
            that.code_key=res.data.url.key;
            that.verifyImg=res.data.url.img;
          })
        })
      }
    },
    back(){
      this.$router.back();
    }
  },
  mounted() {
    let that = this;
    let url='https://grouponapi.top/api/auth/captcha';
    this.axios.get(url).then(res => {
      that.code_key=res.data.url.key;
      that.verifyImg=res.data.url.img;
    })
  }
};
</script>
<style scoped>
/* @import url(); 引入css类 */
@import url('../../assets/css/login.css');
</style>
