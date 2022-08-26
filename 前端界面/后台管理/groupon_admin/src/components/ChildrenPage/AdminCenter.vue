<template>
  <div id="admin_center">
      <el-row class="hello">
        <span>欢迎您！</span> {{mySession.username}}<span>，祝您拥有美好的一天！</span>
      </el-row>

      <el-row class="date">
        <el-col class="date_week" :span="7">
          <el-row class="date_title">今天是：</el-row>
          <el-row class="date_week">{{Week}}</el-row>
        </el-col>

        <el-col :offset="1" class="date_week" :span="7">
          <el-row class="date_title date_title2">{{Month}}月</el-row>
          <el-row class="date_week">{{Day}}日</el-row>
        </el-col>


      </el-row>

  </div>
</template>

<script>
  export default {
    name:"AdminPanel",
    data(){
      return {
        mySession:'',
        Week:0,
        Month:0,
        Day:0

      };
    },
    mounted(){
      var that=this;
        this.axios.get('https://grouponapi.top/api/auth/me').then(res=>{
            that.mySession=res.data.data;
            console.log(that.mySession)
        })

      let weeks = ['星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六']
      let months = ['一','二', '三', '四', '五', '六','七','八','九','十','十一','十二']
      let dataObj=new Date();
      let nowWeek=dataObj.getDay();
      this.Week=weeks[nowWeek];
      this.Month=months[dataObj.getMonth()];
      this.Day=dataObj.getDate();

    }


  }
</script>

<style>
  #admin_center{
    width: 100%;
    height: 100%;
    background-color: #ffffff;
    padding: 1rem;
    box-sizing: border-box;
    border-radius: 25px;
  }
  .hello{
    height: 8rem;
    border-radius: 15px;
    background-color: rgba(236,245,255,0.5);

    box-shadow: 0 0 5px 1px rgba(0,0,0,0.1);
    padding: 1rem;
    font-size: large;
    color: #409EFF;
  }
  .date{
    margin-top: 1rem;
  }
  .date_week{

    background-color: #2A9B00;
    border-radius:25px;
  }
  .date_title{
    border-radius:25px 25px 0 0;
    height: 50px;
    line-height: 50px;
    background-color: #303132;
    color: white;
    text-align: center;
    font-size: medium;
    font-weight: 900;
  }
  .date_week{
    height: 150px;
    line-height: 150px;
    text-align: center;
    font-size: larger;
    font-weight: bolder;
    background-color: #ecf5ff;
  }
  .date_title2{
    background-color: #e6a23c;
  }
</style>
