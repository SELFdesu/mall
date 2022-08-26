<!--  -->
<template>
  <div>
      <div class="comment_main">
          <Header></Header>
          <div class="search_fixed hidden-sm-and-up">
            <el-row class="titleBox">
            <el-col :xs='{span: 2}'>
                <i class="fa fa-chevron-left" aria-hidden="true" v-on:click="back()"></i>
            </el-col>
            <el-col :xs='{span: 20}'>
                <p>商品评论</p>
            </el-col>
            </el-row>
          </div>
          <div class="comment_content">
            <el-row class="comment_title">商品评论</el-row>
            <div class="box">
                <el-row class="info" v-for="(data,index) in dataBox" :key="data.id">
                    <el-col :xs="{ span:10 }" :sm="{ span:3 }">
                        <img :src="data.product.data.picture_url" alt="">
                    </el-col>
                    <el-col :xs="{ span:14 }" :sm="{ span:9 }" class="product_title">
                        <span>{{ data.product.data.title }}</span>
                    </el-col>
                    <el-col :sm="{ span:12 }">
                        <el-row class="star">
                            <el-rate v-model="value[index]" show-score text-color="#ff9900"></el-rate>
                        </el-row>
                        <el-row class="txtarea">
                            <textarea placeholder="评论内容......" v-model="txtarea[index]"></textarea>
                        </el-row>
                    </el-col>
                </el-row>
              </div>
              <el-row class="btn">
                  <button type="button" v-on:click="publish">发表评论</button>
              </el-row>
          </div>
      </div>
  </div>
</template>

<script>
import Header from '../conn/header/Header'
export default {
  Home: 'comment',
  components: {
    Header
  },
  data() {
    return {
        orderId:'',
        dataBox:'',
        value:[],
        txtarea:[]
    };
  },
  mounted() {
      document.querySelector('body').setAttribute('style', 'background-color:#e7f0e6');

      let that=this;
      this.orderId=this.$route.query.orderId;
      this.axios.get('https://grouponapi.top/api/userorder/'+this.orderId +'?include=order,product')
      .then(res => {
          that.dataBox=res.data.data;

      })
  },
  methods: {
    publish(){
        let that=this;
        let data=[];
        let comment={};
        let flag=0;
        that.dataBox.forEach(function (item,index) {
            let obj={};
            obj.product_id=item.product_id;
            obj.content=that.txtarea[index]||'';
            obj.grade=that.value[index];
            data.push(obj);
            if(that.value[index]==0){
                flag=1
            }
        })
        if(flag){
            this.$message({
                message: '评分不能为 0 哦！'
            })
            return 0;
        }else{
          comment.data=data;
          
          this.axios.post('https://grouponapi.top/api/comment/add/'+this.orderId,{
              comments: JSON.stringify(comment)
          }).then(res => {
          
              that.$message({
                  message: '发表成功！',
                  type: 'success'
              })
              that.$router.push('/my/order')
          }).catch(function(error){
              console.log(error);
          })
        }

    }
  }
};
</script>
<style scoped>
/* @import url(); 引入css类 */
.comment_main{
    width: 100%;
}
.comment_content{
    width: 66%;
    margin: 0.1% auto 0;
}
.comment_title{
    padding: 1%;
    background-color: rgb(228, 227, 227);
    border: 1px solid #2a9b00;
}
.box{
    border: 1px solid #2a9b00;
    border-top: none;
}
.info{
    padding: 1%;
    background-color: #fff;
    border-bottom: 1px dashed #ccc;
}
.info:last-child{
    border-bottom: none;
}
.info img{
    width: 100%;
}
.product_title{
    font-size: 18px;
    padding: 0 1%;
}
/deep/ .el-rate__icon{
    font-size: 26px;
}
.txtarea{
    margin-top: 3%;
}
textarea{
    width: 100%;
    height: auto;
    aspect-ratio: 3/1;
    resize: none;
    outline: none;
    font-size: 17px;
    padding: 1%;
    box-sizing: border-box;
}
.btn{
    text-align: center;
    padding: 1% 0 2%;
    background-color: #fff;
}
.btn button{
    width: 15%;
    font-size: 16px;
    padding: 0.5%;
    border-radius: 5px;
    background-color: #2a9b00;
    border: 1px solid #2a9b00;
    outline: none;
    color: #fff;
}
@media only screen and (max-width: 767px){
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
    .comment_content{
    width: 96%;
    margin: 13% auto 0;
    }
    .product_title{
    font-size: 18px;
    padding: 0 2%;
}
.info{
    padding: 2%;
    background-color: #fff;
    border-bottom: 2px dashed #ccc;
}
.star{
    padding:2%;
}
textarea{
    width: 100%;
    height: auto;
    aspect-ratio: 2/1;
}
.btn{
    text-align: center;
    padding: 3% 0 3%;
    background-color: #fff;
}
.btn button{
    width: 35%;
    font-size: 16px;
    padding: 1.5%;
    border-radius: 22px;
    background-color: #2a9b00;
    border: 1px solid #2a9b00;
    outline: none;
    color: #fff;
}
}
</style>
