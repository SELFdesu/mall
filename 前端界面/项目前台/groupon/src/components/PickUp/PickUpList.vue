<!-- 自提点列表 -->
<template>
  <div>
    <div class="all">
      <div class="topTitle">
        <i class="fa fa-chevron-left" aria-hidden="true" v-on:click="back()"></i>
        <span>选择自提点</span>
      </div>
      <div class="pickupList">
        <el-row>
          <el-col :xs="{ span: 24 }" :sm="{ span: 6 }" class="pickupEveryOne" v-for="pickup in pickupBox" :key='pickup.id'>
            <el-row>
              <el-col class="pickupImg" :xs="{ span: 6 }" :sm="{ span: 7 }">
                <img :src="pickup.pic_url" alt="">
              </el-col>
              <el-col :xs="{ span: 17, offset: 1 }" :sm="{ span: 16, offset: 1 }">
                    <el-row class="pickupTxt1">
                        <p>{{ pickup.store_name }}</p>
                    </el-row>
                    <el-row class="pickupTxt2">
                        <p>{{ pickup.address_name }}</p>
                        <p class="nowrap">{{ pickup.address_info }}</p>
                    </el-row>
              </el-col>
            </el-row>
            <el-row class="button">
                <el-col :span='12'>
                    <button type="button" class="delbtncol" v-on:click='del(pickup.id)'>删除</button>
                </el-col>
                <el-col :span='12'>
                    <button type="button" class="chobtncol" v-on:click='cho(pickup)'>选择</button>
                </el-col>
            </el-row>
          </el-col>
        </el-row>
      </div>
      <div class="choicePickUp">
          <router-link to="/pickup/add"><button>选择其他自提点</button></router-link>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
        pickupBox:[]
    };
  },
  mounted() {
      document.querySelector('body').setAttribute('style', 'background-color:#e7f0e6');

      let that=this;
      this.axios.get('https://grouponapi.top/api/user/delivery/list')
       .then(res => {
           that.pickupBox=res.data.data;
       }).catch(function(error){
           console.log(error);
       })
  },
  methods: {
      del(pickupId){
          let that=this;
          this.axios.delete('https://grouponapi.top/api/user/delivery/'+pickupId+'/del')
           .then(res => {
               if(localStorage.getItem('pickupId') == pickupId){
                   localStorage.removeItem('pickupId');
                   localStorage.removeItem('pickupName');
                   localStorage.removeItem('pickupPic');
               }
               that.$router.go();
               that.$message({
                   message: '删除成功！',
                   type: 'success'
               })
           }).catch(function(error){
               that.$message({
                   message: '删除失败！',
                   type: 'error'
               })
           })
      },
      cho(pickup){
          localStorage.setItem('pickupId',pickup.id);
          localStorage.setItem('pickupName',pickup.store_name);
          localStorage.setItem('pickupPic',pickup.pic_url);
          this.$message({
              message: '选择成功！',
              type: 'success'
          })
          this.$router.back();
      },
      back(){
          this.$router.back();
      }
  }
};
</script>
<style scoped>
/* @import url(); 引入css类 */
@import url('../../assets/css/pickuplist.css');
</style>
