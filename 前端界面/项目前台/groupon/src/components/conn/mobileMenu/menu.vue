<!--  -->
<template>
  <div>
      <div class="hidden-sm-and-up xs_nav">
        <el-row type="flex" justify="center">
          <el-col :span="6">
            <router-link to="/">
              <i class="fa fa-home" :style="selectLi ==1?'color:#ff5000':'color:#2a9b00'" aria-hidden="true"></i>
              <p>首页</p>
            </router-link>
          </el-col>
          <el-col :span="6">
            <router-link to="/classify">
              <i class="el-icon-menu" :style="selectLi ==2?'color:#ff5000':'color:#2a9b00'" aria-hidden="true"></i>
              <p>分类</p>
            </router-link>
          </el-col>
          <el-col :span="6">
            <router-link to="/shopping/cart">
              <i class="fa fa-shopping-cart" :style="selectLi ==3?'color:#ff5000':'color:#2a9b00'" aria-hidden="true"></i>
              <p>购物车</p>
            </router-link>
          </el-col>
          <el-col :span="6">
            <router-link to="/my">
              <i class="fa fa-user" :style="selectLi ==4?'color:#ff5000':'color:#2a9b00'" aria-hidden="true"></i>
              <p>我的</p>
            </router-link>
          </el-col>
        </el-row>
      </div>
  </div>
</template>

<script>
export default {
  props:['selectLi'],
  data() {
    return {};
  },
  //生命周期 - 创建完成（访问当前this实例）
  created() {},
  //生命周期 - 挂载完成（访问DOM元素）
  mounted() {}
};
</script>
<style scoped>
/* @import url(); 引入css类 */
.active{
  color: #ff5000;
}

/* 底部导航 开始 */
.xs_nav {
  position: fixed;
  width: 100%;
  left: 0;
  bottom: 0;
  text-align: center;
  padding: 5px 0;
  z-index: 999;
  background-color: snow;
  box-shadow: 0 0 2px rgba(0, 0, 0, 0.4);
}
.xs_nav a {
  text-decoration: none;
  color: #000;
}
.xs_nav i {
  font-size: 26px;
  color: #2a9b00;
}
/* 底部导航 结束 */
</style>
