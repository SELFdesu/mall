<!-- Header -->
<template>
  <div>
    <el-row class="navTop" type="flex" justify="end">
      <el-col :sm="14" :md="12" :lg="10" class="hidden-xs-only">
        <router-link to="/"><span :class="selectLi ==1?'active':''">首页</span></router-link>
        <span>|</span>
        <router-link to="/login" v-if="is_login"><span :class="selectLi ==2?'active':''">登录</span></router-link>
        <router-link to="" v-else>
          <el-dropdown placement="bottom">
            <span class="el-dropdown-link">
              <span :class="selectLi ==2?'active':''">{{ username }}</span>
            </span>
            <el-dropdown-menu slot="dropdown">
              <el-dropdown-item><router-link to="/my" class="personal">个人中心</router-link></el-dropdown-item>
              <span v-on:click="exit()"><el-dropdown-item divided>退出登录</el-dropdown-item></span>
            </el-dropdown-menu>
          </el-dropdown>
        </router-link>
        <span>|</span>
        <router-link to="/my/order"><span :class="selectLi ==3?'active':''">我的订单</span></router-link>
        <span>|</span>
        <router-link to="/shopping/cart"><span :class="selectLi ==4?'active':''">购物车</span></router-link>
        <span>|</span>
        <router-link to="/head/management" v-if="userRole"><span :class="selectLi ==5?'active':''">团长</span></router-link>
        <span v-if="userRole">|</span>
        <a href="#" onclick='return false'>
          <el-dropdown placement='bottom'>
            <span class="el-dropdown-link">
              <i class="el-icon-mobile-phone"></i> 手机访问
            </span>
            <el-dropdown-menu slot="dropdown">
              <el-dropdown-item>
                <div id="qrcode" ref="qrcode"></div>
              </el-dropdown-item>
            </el-dropdown-menu>
          </el-dropdown>
        </a>
      </el-col>
    </el-row>
  </div>
</template>

<script>
import QRCode from 'qrcodejs2'

export default {
  name: 'Header',
  props: ['selectLi'],
  data() {
    return {
        is_login:true,
        userRole:false,
        username:'',
    }
  },

  methods: {
    exit(){
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
                history.go(0);
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
    qrcode(){
      let qrcode=new QRCode('qrcode', {
        width: 100,
        height: 100,
        text: window.location.href
      })
    }
  },

  created () {
      let that=this;
      
      this.$nextTick(() => {
        this.qrcode()
      })

      // 判断token是否过期，过期刷新token
      if(localStorage.getItem('token') != null){
        var nowTime = +new Date();
        if(nowTime >= localStorage.getItem('expires_time')){
          this.axios.post('https://grouponapi.top/api/auth/refresh')
          .then(res => {
          localStorage.setItem('token',res.data.token_type+' '+res.data.access_token);
          localStorage.setItem('expires_time',+new Date() + res.data.expires_in);
          })
        }
      }

      if(localStorage.getItem('token') != null){
        this.is_login=false;
        this.axios.get('https://grouponapi.top/api/auth/me')
        .then(res => {
          this.username=res.data.data.username;
          if(res.data.data.userRole == '团长'){
            this.userRole=true;
          }else{
            this.userRole=false;
          }
        })
      }else{
          this.is_login=true;
      }
  }
};
</script>
<style scoped>
/* @import url(); 引入css类 */
.active{
  color: #f98d1f;
}
.navTop {
  color: #fff;
  font-size: 14px;
  font-weight: bold;
  line-height: 40px;
  background-color: #2a9b00;
  cursor: default;
}
.navTop a {
  margin: 0 1.6%;
  color: #fff;
  text-decoration: none;
}
.el-dropdown-link{
  color: #fff;
}
.navTop a:hover,
.el-dropdown-link:hover {
  color: #dadfd9;
}
.personal{
  text-decoration: none;
}
</style>
