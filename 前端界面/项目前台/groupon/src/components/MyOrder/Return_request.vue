<!--  -->
<template>
  <div>
      <div class="returnRequest_main">
          <Header></Header>
          <div class="search_fixed hidden-sm-and-up">
            <el-row class="titleBox">
            <el-col :xs='{span: 2}'>
                <i class="fa fa-chevron-left" aria-hidden="true" v-on:click="back()"></i>
            </el-col>
            <el-col :xs='{span: 20}'>
                <p>商品详情</p>
            </el-col>
            </el-row>
          </div>
          <div class="returnRequest_content" v-show="returnRequest_show">
                <el-row class="product" v-for="data in dataBox" :key="data.id">
                <el-col :sm="{ span:6 }" class="txt first">
                    <span>退货商品：</span>
                </el-col>
                <el-col :sm="{ span:18 }" style="padding-top:1%;">
                    <el-col :xs="{ span:6 }" :sm="{ span:6}">
                        <img :src="data.product.data.picture_url" alt="">
                    </el-col>
                    <el-col :xs="{ span:17, offset:1 }" :sm="{ span:17, offset:1}" class="productName">
                        {{ data.product.data.title }}
                    </el-col>
                </el-col>
            </el-row>
            <el-row class="type">
                <el-col :xs="{ span:7 }" :sm="{ span:6 }" class="txt second">
                    <span>服务类型：</span>
                </el-col>
                <el-col :xs="{ span:17 }" :sm="{ span:18 }" class="typeInput">
                    <el-col :xs="{ span:10 }">
                        <label><input type="radio" name="return" value="1" v-model="type" > 仅退款</label><br>
                    </el-col>
                    <el-col :xs="{ span:12 }">
                        <label><input type="radio" name="return" value="2" v-model="type" style="margin-top: 1.5%"> 退货退款</label>
                    </el-col>
                </el-col>
            </el-row>
            <el-row class="refund">
                <el-col :xs="{ span:7 }" :sm="{ span:6 }" class="txt third">
                    <span>退款原因：</span>
                </el-col>
                <el-col :xs="{ span:17 }" :sm="{ span:18 }" class="refundSelect">
                    <el-select class="select" v-model="radio" size="small" placeholder="请选择">
                        <el-option label="做工问题" value="1"></el-option>
                        <el-option label="质量问题" value="2"></el-option>
                        <el-option label="配件问题" value="3"></el-option>
                        <el-option label="商品破损" value="4"></el-option>
                        <el-option label="未按时间发货" value="5"></el-option>
                        <el-option label="发错货物" value="6"></el-option>
                        <el-option label="少件漏发" value="7"></el-option>
                        <el-option label="快件丢失" value="8"></el-option>
                        <el-option label="其他" value="9"></el-option>
                        <el-option label="未发货商品申请退款" value="10"></el-option>
                    </el-select>
                </el-col>
            </el-row>
            <el-row class="money">
                <el-col :xs="{ span:7 }" :sm="{ span:6 }" class="txt fourth">
                    <span>退款金额：</span>
                </el-col>
                <el-col :xs="{ span:17 }" :sm="{ span:18 }" class="moneyTxt">
                    <div>
                        <span>&yen;</span>
                        <input type="text" :value="dataBox[0].order.data.amount" disabled>
                    </div>
                </el-col>
            </el-row>
            <el-row class="explain">
                <el-col :xs="{ span:7 }" :sm="{ span:6 }" class="txt fifth">
                    <span>退款说明：</span>
                </el-col>
                <el-col :xs="{ span:17 }" :sm="{ span:18 }" class="explainTxtarea">
                    <textarea v-model="txtareaVal" placeholder="退款说明"></textarea>
                </el-col>
            </el-row>
            <el-row class="images">
                <el-col :xs="{ span:7 }" :sm="{ span:6 }" class="txt sixth">
                    <span>上传图片：</span><br>
                    <span class="limit">（最多三张）</span>
                </el-col>
                <el-col :xs="{ span:17 }" :sm="{ span:18 }" class="upload">
                    <el-upload 
                        action=""
                        :limit="3"
                        list-type="picture-card"
                        :on-remove="handleRemove"
                        :auto-upload="false"
                        :on-change="uploadBefore"
                        :show-file-list="true">
                        <i class="el-icon-plus"></i>
                    </el-upload>
                    <el-button slot="trigger" size="small" type="primary">选取</el-button>
                </el-col>
            </el-row>
            <el-row class="btn">
                <button type="button" v-on:click="refund()">提交</button>
                <p>{{ reject_return }}</p>
            </el-row>
          </div>

          <div v-show="return_goods"  class="sendBack">
              <p style="text-align:center; margin-bottom:5%; color:#ff5000;font-size:18px;">商家已同意申请<br>请填写快递类型及其单号</p>
            <el-row class="express_type" type="flex" justify="center">
                <el-col :xs="{ span:9 }" :sm="{ span:6 }" class="info">
                    快递类型：
                </el-col>
                <el-col :xs="{ span:14 }" :sm="{ span:16 }">
                    <el-select class="select" v-model="express" size="small" placeholder="请选择">
                    <el-option label="圆通快递" value="YT"></el-option>
                    <el-option label="顺丰快递" value="SF"></el-option>
                    <el-option label="韵达快递" value="YD"></el-option>
                    </el-select>
                </el-col>
            </el-row>
            <el-row class="express_num" type="flex" justify="center">
                <el-col :xs="{ span:9 }" :sm="{ span:6 }" class="info">
                    快递单号：
                </el-col>
                <el-col :xs="{ span:14 }" :sm="{ span:16 }">
                <input type="text" v-model="orderVal">
                </el-col>
            </el-row>
            <el-row class="sendBackBtn">
                    <button type="button" v-on:click="sendBack()">寄回商品</button>
            </el-row>
          </div>

          <div v-show="return_status" class="return_status">{{ returnStatus }}</div>
      </div>
  </div>
</template>

<script>
import Header from '../conn/header/Header'
export default {
  Home: 'returnRequest',
  components: {
    Header
  },
  data() {
    return {
        returnRequest_show:true,
        return_goods:false,
        orderId:'',
        dataBox:'',
        type:'2',
        radio:'',
        txtareaVal:'',
        picList:[],
        picName:{
            urls:[]
        },
        pics:[],
        express:'',
        orderVal:'',
        return_status:false,
        returnStatus:'',
        reject_return:''
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

      this.axios.get('https://grouponapi.top/api/sales/return/'+ this.orderId + '/info')
      .then(res => {

          let status=res.data.data;
   
          if(status.status == 0){
              that.return_goods=false;
              that.returnRequest_show=true;
              that.reject_return='商家拒绝您的退货申请！';
          }else if(status.status == 1 && status.type == 2){
              that.returnRequest_show=false;
              that.return_goods=true;
          }else if(status.status == 2 || status.status == 3 || status.status==4 || status.status ==5){
              that.returnRequest_show=false;
              that.return_goods=false;
              that.return_status=true;
              that.returnStatus=status.status_name;
          }else if(status.type == 1){
              that.returnRequest_show=false;
              that.return_goods=false;
              that.return_status=true;
              that.returnStatus=status.status_name
          }
      })
  },
  methods: {
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
        this.pics.push(file);
       
    },
    handleRemove(file, fileList) {
  
        this.pics=file
   
    },
    refund(){
        let that=this;
        if(this.pics.length != 0){
            this.pics.forEach(function(item,index){
               
                that.axios.get('https://grouponapi.top/api/auth/oss/token')
                .then(res => {
                   

                    var data = res.data;
                   
                    
                    var ossData = new FormData();
                    ossData.append("name", item.raw.name);
                    //获取图片类型 后缀 jpg / png
                    let imgType = item.raw.type.split("/")[1];  
                    let filename = item.raw.name; //md5对图片名称进行加密
                    let keyValue = "sales_return/" + that.orderId + index +filename;
                    //文件名
                    ossData.append("key", keyValue);
                    ossData.append("policy", data.policy);
                    ossData.append("OSSAccessKeyId", data.accessid);
                    ossData.append("success_action_status", 201);
                    ossData.append("signature", data.signature);
                    ossData.append("file", item.raw, item.raw.name);
                  
                    that.axios.post(data.host, ossData, {
                        headers: {
                        "Content-Type": "multipart/form-data"
                        }
                    })
                    .then(res => {
                        if (res.status == 201) {
                           
                            that.picName.urls.push(keyValue.split("/")[1]);
                          
                        }
                    }).catch(error => {});
                })
            })
            setTimeout(function(){
                if(that.radio != ''){
                that.axios.post('https://grouponapi.top/api/sales/return/'+that.orderId +'/apply',{
                    type: that.type,
                    cause: that.radio,
                    describe: that.txtareaVal,
                    pics: JSON.stringify(that.picName)
                }).then(res => {
                 
                    that.$message({
                        message: '提交成功！',
                        type: 'success'
                    })
                    that.$router.push('/my/order');
                }).catch(function(error){
                  
                    that.$message({
                        message: error.response.data.message,
                        type: 'error'
                    })
                })
            }else{
                that.$message({
                    message: '退款原因不能为空！',
                    type: 'error'
                })
            }
            },1000)
            
        }else{
            if(that.radio != ''){
               
                that.axios.post('https://grouponapi.top/api/sales/return/'+that.orderId +'/apply',{
                    type: that.type,
                    cause: that.radio,
                    describe: that.txtareaVal,
                    pics: JSON.stringify(that.picName)
                }).then(res => {
                    
                    that.$message({
                        message: '提交成功！',
                        type: 'success'
                    })
                    that.$router.push('/my/order');
                }).catch(function(error){
             
                    that.$message({
                        message: error.response.data.message,
                        type: 'error'
                    })
                })
            }else{
                that.$message({
                    message: '退款原因不能为空！',
                    type: 'error'
                })
            }
        }
        
    },
    sendBack(){
        let that=this;
        if(that.express != '' && that.orderVal != ''){
            this.axios.patch('https://grouponapi.top/api/sales/return/'+that.orderId +'/send',{
                express_type: that.express,
                express_no: that.orderVal
            }).then(res => {
             
                that.$message({
                    message: '提交成功！',
                    type: 'success'
                })
                history.go(0);
            }).catch(function(error){
                console.log(error);
            })
        }else{
            this.$message({
                message: '快递类型与快递单号都不可为空！',
                type: 'error'
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
.returnRequest_main{
    width: 100%;
}
.returnRequest_content{
    width: 50%;
    margin: 0.2% auto;
    padding: 1%;
    border: 1px solid #2a9b00;
    border-radius: 10px;
    background-color: #fff;
}
.txt{
    text-align: right;
    font-size: 18px;
}
.product img{
    width: 100%;
    margin-left: 10%;
}
.first{
    padding: 2% 0;
}
.type,
.refund,
.money,
.explain,
.images{
    margin-top: 3.5%;
}
.typeInput input{
    width: 15px;
    height: 15px;
    margin-left: 1.5%;
}
.refundSelect .select{
    margin-left: 1.5%;
}
.moneyTxt div{
    width: 220px;
    margin-left: 1.5%;
    border: 1px solid #ccc;
    background-color: #fff;
    padding: 0.6% 0 0.5% 1%;
    border-radius: 4px;
}
.moneyTxt span{
    font-size: 18px;
    color: #ff5000;
    font-weight: bold;
}
.moneyTxt input{
    width: 90%;
    font-size: 18px;
    font-weight: bold;
    color: #ff5000;
    outline: none;
    border: none;
}
.explainTxtarea textarea{
    margin-left: 1.5%;
    width: 66%;
    height: auto;
    aspect-ratio: 10/3;
    resize: none;
    outline: none;
    border-radius: 4px;
    padding: 1%;
    font-size: 16px;
}
.upload{
    padding-left: 1%;
    padding-bottom: 8%;
}
.limit{
    font-size: 16px;
}
/deep/ .el-upload--picture-card {
    width: 80px;
    height: 80px;
}
/deep/ .el-icon-plus {
    font-size: 20px;
    color: #8c939d;
    position: relative;
    top: -30px;
    text-align: center;
}
 /deep/ .el-upload-list__item{
     width: 80px;
     height: 80px;
 }
.btn{
    text-align: center;
    padding: 5%;
}
.btn button{
    width: 20%;
    padding: 1%;
    background-color: #2a9b00;
    border: 1px solid #2a9b00;
    font-size: 18px;
    color: #fff;
    border-radius: 5px;
    cursor: pointer;
}
.sendBack{
    width: 60%;
    margin: 6% auto;
    padding: 2% 0 1%;
    background-color: #fff;
    border-radius: 5px;
}
.info{
    text-align: center;
    padding: 1% 0;
}
.express_num{
    margin-top: 2%;
}
.express_num input{
    width: 30%;
    min-width: 200px;
    padding: 1.5% 1%;
    outline: none;
    border: 1px solid #ccc;
    font-size: 16px;
}
.sendBackBtn{
    margin: 5%;
}
.sendBackBtn button{
    width: 25%;
    padding: 1% 0;
    margin-left: 30%;
    border-radius: 5px;
    outline: none;
    background-color: #2a9b00;
    border: 1px solid #2a9b00;
    color: #fff;
    font-size: 16px;
}
.return_status{
    width: 50%;
    height: 150px;
    line-height: 150px;
    font-size: 36px;
    font-weight: bold;
    color: #ff5000;
    margin: 5% auto;
    text-align: center;
    background-color: #fff;
    border-radius: 15px;
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
    .returnRequest_content{
        width: 96%;
        margin: 12% auto;
        padding: 1%;
        border: none;
        border-radius: 10px;
        background-color: #fff;
    }
    .txt{
        text-align: left;
        font-size: 16px;
        padding-left: 2%;
    }
    .type,
    .refund,
    .money,
    .explain,
    .images{
        margin-top: 5%;
        padding: 2% 0 0;
        border-top: 1px dashed #ccc;
    }
    .btn{
        position: fixed;
        left: 0;
        bottom: 0;
        width: 100%;
        text-align: center;
        padding: 3% 1%;
        background-color: #fff;
    }
    .limit{
        font-size: 13px;
    }
    .btn button{
        width: 100%;
        padding: 1%;
        background-color: #2a9b00;
        border: 1px solid #2a9b00;
        font-size: 18px;
        color: #fff;
        border-radius: 20px;
        cursor: pointer;
    }
    .sendBack{
        width: 96%;
        margin: 25% auto;
        padding: 10% 0 1%;
        background-color: #fff;
        border-radius: 5px;
    }
    .express_num{
        margin-top: 6%;
    }
    .express_num input{
        width: 98%;
        min-width: 175px;
        padding: 3% 1%;
        outline: none;
        border: 1px solid #ccc;
        font-size: 15px;
    }
    .sendBackBtn{
        margin: 8%;
    }
    .sendBackBtn button{
        width: 100%;
        padding: 3% 0;
        margin-left: 0;
        border-radius: 20px;
    }
    .return_status{
        width: 80%;
        height: 150px;
        line-height: 150px;
        font-size: 36px;
        font-weight: bold;
        color: #ff5000;
        margin: 20% auto;
        text-align: center;
        background-color: #fff;
        border-radius: 15px;
    }
}
</style>
