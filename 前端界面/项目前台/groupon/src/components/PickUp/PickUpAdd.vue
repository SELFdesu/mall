<!--  -->
<template>
  <div>
    <div class="all">
      <div class="topTitle">
        <i class="fa fa-chevron-left" aria-hidden="true" v-on:click="back()"></i>
        <span>切换自提点</span>
      </div>
      <el-cascader clearable show-all-levels :props="props" style="width:100%;" @change="handleChange" size='medium'></el-cascader>
      <div class="pickupList">
        <el-row>
          <el-col :xs="{ span: 24 }" :sm="{ span: 6 }" class="pickupEveryOne" v-for="pickup in pickupBox" :key='pickup.id'>
            <el-col class="pickupImg" :xs="{ span: 6 }" :sm="{ span: 7 }">
              <img :src="pickup.pic_url" alt="">
            </el-col>
            <el-col :xs="{ span: 17, offset: 1 }" :sm="{ span: 16, offset: 1 }">
              <el-row class="pickupTxt1">
                <el-col>
                  <p>{{ pickup.store_name }}</p>
                </el-col>
              </el-row>
              <el-row class="pickupTxt2">
                <el-col :xs="{ span: 17}" :sm="{ span: 16}">
                  <p>{{ pickup.address_name }}</p>
                  <p class="nowrap">{{ pickup.address_info }}</p>
                </el-col>
                <el-col class="pickupBtn" :xs="{ span: 6, offset: 1 }" :sm="{ span: 7, offset: 1 }">
                  <button type="button" v-on:click="choicePickup(pickup.id)">选择</button>
                </el-col>
              </el-row>
            </el-col>
          </el-col>
        </el-row>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      props: {
        lazy: true,
        lazyLoad: this.lazyLoad
      },
      pickupBox:[]
    };
  },
  methods: {
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
      let that=this;
      if(value[2] != undefined){
        this.axios.get('https://grouponapi.top/api/basic/delivery/list?address_id='+ value[2])
        .then(res => {
          that.pickupBox=res.data.data;
        }).catch(function(error) {
          console.log(error);
        })
      }
    },
    choicePickup(pickupId){
      let that=this;
      this.axios.post('https://grouponapi.top/api/user/delivery/add',{
        delivery_id:pickupId
      }).then(res => {
        that.$message({
          message: '选择成功！',
          type: 'success'
        })
        that.$router.back();
      }).catch(function(error){
        if(error.response.status == 400)
        that.$message({
          message: error.response.data.message,
          type: 'error'
        })
      })
    },
    back(){
      this.$router.back();
    }
  },

  mounted() {
    document.querySelector('body').setAttribute('style', 'background-color:#e7f0e6');
  }
};
</script>
<style scoped>
/* @import url(); 引入css类 */
@import url('../../assets/css/pickupadd.css');
</style>
