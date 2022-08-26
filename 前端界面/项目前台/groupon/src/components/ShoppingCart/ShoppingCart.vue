<!--  -->
<template>
  <div>
      <div class="shoppingCart_main">
          <Header selectLi="4"></Header>
          <div class="cart_content">
              <el-row class="top hidden-xs-only">
                  <el-col  :sm='{ span:8}' :md='{ span:8}' class="logo_img">
                      <img src="../../assets/images/home/logo.png" alt="">
                  </el-col>
                  <el-col :sm='{ span:16}'  :md='{ span:16}'>
                      <div class="search">
                          <input type="text" class="cart_search" v-model.trim="productName">
                          <input type="button" value="搜索" class="cart_search_btn" v-on:click="searchBtn">
                      </div>
                  </el-col>
              </el-row>
              <el-row class="myCart hidden-xs-only">
                  <el-col>
                      <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                      <span>我的购物车</span>
                  </el-col>
              </el-row>
              <el-row class="product_title hidden-xs-only">
                  <el-col :sm='{ span:2}' :md="{ span: 2 }">
                    <label><input name="check_all" type="checkbox" v-model="checkedAll" value="" v-on:click="changeAllChecked()" />&nbsp;全选</label>
                  </el-col>
                   <el-col :sm='{ span:8}' :md="{ span: 8 }">商品信息</el-col>
                   <el-col :sm='{ span:3}' :md="{ span: 3 }">单价</el-col>
                   <el-col :sm='{ span:5}' :md="{ span: 5 }">数量</el-col>
                   <el-col :sm='{ span:3}' :md="{ span: 3 }">金额</el-col>
                   <el-col :sm='{ span:3}' :md="{ span: 3 }">操作</el-col>
              </el-row>
              <div class="cart_infoBox hidden-xs-only" v-if="infoBox">
                <el-row class="cart_info" v-for="cartInfo in cartInfoBox" :key="cartInfo.id">
                    <el-col :sm='{ span:1}' :md="{ span: 1 }">
                        <input type="checkbox" v-model="checkedOnes" :value="cartInfo.id" />
                    </el-col>
                    <el-col :sm='{ span:9}' :md="{ span: 9 }">
                        <el-col :sm='{ span:5}' :md="{ span: 5 }">
                            <router-link :to="'/product/detail?productId='+cartInfo.product_id">
                                <img :src="cartInfo.product.data.picture_url" alt="">
                            </router-link>
                        </el-col>
                        <el-col :sm='{ span:18}' :md="{ span: 18 }" class="productName">
                            <router-link :to="'/product/detail?productId='+cartInfo.product_id">
                                <span>{{ cartInfo.product.data.title }}</span>
                            </router-link>
                        </el-col>
                    </el-col>
                    <el-col :sm='{ span:3}' :md="{ span: 3 }" class="size"><span>&yen;{{ cartInfo.product.data.price }}</span></el-col>
                    <el-col :sm='{ span:5}' :md="{ span: 5 }">
                        <el-input-number v-model="cartInfo.quantity" @change="handleChange(cartInfo.id,cartInfo.quantity)" :min="1" size="mini" label="加购数量"></el-input-number>
                    </el-col>
                    <el-col :sm='{ span:3}' :md="{ span: 3 }" class="size"><span>&yen;{{ (cartInfo.product.data.price*cartInfo.quantity).toFixed(2) }}</span></el-col>
                    <el-col :sm='{ span:3}' :md="{ span: 3 }" class="size">
                        <span v-on:click="deleteOne(cartInfo.id)">删除</span>
                    </el-col>
                </el-row>
              </div>
              <div class="message hidden-xs-only" v-else>您的购物车里什么都没有，快去加购吧~</div>
              <el-row id="father" class="bottomBox  hidden-xs-only">
                  <el-col :sm='{ span:2}' :md="{ span: 2 }">
                      <label><input name="check_all" v-model="checkedAll" type="checkbox" value="" v-on:click="changeAllChecked()" />&nbsp;全选</label>
                  </el-col>
                  <el-col :sm='{ span:4}' :md="{ span: 4 }">
                      <span class="delete" v-on:click="deleteArr">删除选中商品</span>
                  </el-col>
                  <el-col :sm='{ span:4,offset:6}' :md="{ span: 4, offset:6 }"  :lg="{ span: 4, offset:7 }" class="right">
                      已选&nbsp;<span class="color">{{ checkedOnes.length }}</span>&nbsp;件商品
                  </el-col>
                  <el-col :sm='{ span:5}' :md="{ span: 5 }"  :lg="{ span: 4 }" class="right">
                      合计：&yen;<span class="color">{{ amount }}</span>
                  </el-col>
                  <el-col :sm='{ span:3}' :md="{ span: 3 }" class="right">
                      <button :style="'background-color:'+ backgroundColor" id="son" type="button" :disabled="disabled" v-on:click="accountBtn">去结算</button>
                  </el-col>
              </el-row>

              <!-- 手机 -->
              <MobileMenu selectLi="3"></MobileMenu>
              <div class="mobile_main hidden-sm-and-up">
                <el-row class="Cart">
                    <el-col>
                        <span>购物车</span>
                    </el-col>
                </el-row>
                <div class="product_infoBox" v-if="infoBox">
                    <el-row class="productOne" v-for="cartInfo in cartInfoBox" :key="cartInfo.id">
                        <el-col class="input" :xs="{ span: 2 }">
                            <input name="" type="checkbox" v-model="checkedOnes" :value="cartInfo.id" />
                        </el-col>
                        <el-col class="img" :xs="{ span: 6, offset:1}">
                            <router-link :to="'/product/detail?productId='+cartInfo.product_id">
                                    <img :src="cartInfo.product.data.picture_url" alt="">
                            </router-link>
                        </el-col>
                        <el-col :xs="{ span: 15 }">
                            <el-row class="name">
                                <router-link :to="'/product/detail?productId='+cartInfo.product_id">
                                    <span>{{ cartInfo.product.data.title }}</span>
                                </router-link>
                            </el-row>
                            <el-row class="bottom">
                                <el-col class="price" :xs="{ span: 10 }">
                                    <span>&yen;{{ cartInfo.product.data.price }}</span>
                                </el-col>
                                <el-col :xs="{ span: 14 }">
                                    <el-input-number v-model="cartInfo.quantity" @change="handleChange(cartInfo.id,cartInfo.quantity)" :min="0" size="mini" label="加购数量"></el-input-number>
                                </el-col>
                            </el-row>
                        </el-col>
                    </el-row>
                </div>
                <div class="message" v-else>您的购物车里什么都没有<br/>快去加购吧~</div>
                <el-row class="fixed">
                    <el-col :xs="{ span: 4}" class="input_checkedAll">
                        <label><input name="check_all" v-model="checkedAll" type="checkbox" value="" v-on:click="changeAllChecked()" />&nbsp;全选</label>
                    </el-col>
                    <el-col class="amount" :xs="{ span: 12}">
                        合计：<span class="color">&yen;{{ amount }}</span>
                    </el-col>
                    <el-col :xs="{ span: 8}">
                        <button type="button" :style="'background-color:'+ backgroundColor" :disabled="disabled" v-on:click="accountBtn">去支付<span>（{{ checkedOnes.length }}）</span></button>
                    </el-col>
                </el-row>
              </div>
          </div>
      </div>
  </div>
</template>

<script>
import Header from '../conn/header/Header'
import MobileMenu from '../conn/mobileMenu/menu'
export default {
    name: 'ShoppingCart',
    components: {
        Header,
        MobileMenu
    },
    data() {
        return {
            infoBox:true,
            productName:'',
            cartInfoBox:'',
            cartIds:{data:[]},
            checkedAll:false,
            checkedOnes:[],
            checkedOneArr:[],
            disabled:true,
            backgroundColor:'#ccc',
            amount:0
        }
    },
    mounted() {
        document.querySelector('body').setAttribute('style', 'background-color:#e7f0e6');

        this.$nextTick(() => {
          // 动态高度
          var father = document.getElementById('father');
          var son = document.getElementById('son');
          father.style.lineHeight=son.offsetHeight+'px';

          // 动态高度发生变化
          window.addEventListener('resize', () => {
            var father = document.getElementById('father');
            var son = document.getElementById('son');
            father.style.lineHeight=son.offsetHeight+'px';
          })
        })


        let that=this;
        this.axios.get('https://grouponapi.top/api/shoppingcart?include=product')
         .then(res => {
             that.cartInfoBox=res.data.data;
             for(var i=0; i<this.cartInfoBox.length; i++){
                this.checkedOneArr.push(this.cartInfoBox[i].id);
             }
             if(that.cartInfoBox.length==0){
                 that.infoBox=false;
             }
         })
    },
    methods: {
        searchBtn(){
            if(this.productName != ''){
                this.$router.push('/search/list?searchTitle='+ this.productName);
            }else{
                this.$message({
                message: '搜索框不能为空！',
                type: 'error'
                })
            }
        },
        handleChange(cartId,value){
            let that=this;
            if(value == 0){
                this.$confirm('确定要删除该商品吗？', '', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                center:true,
                type: 'warning'
                }).then(() => {
                    that.cartIds.data.push(cartId);
                    that.axios.delete('https://grouponapi.top/api/shoppingcart/del',{
                        data:{
                            carts: JSON.stringify(this.cartIds)
                        }
                    }).then(res => {
                        that.$message({
                            message: '删除成功！',
                            type: 'success'
                        })
                        that.$router.go(0);
                    }).catch(function(error){
                        console.log(error);
                    })
                }).catch(() => {
                    this.$message({
                        type: 'info',
                        message: '已取消删除'
                    });
                    that.$router.go(0);
                });
            }else{

                //改变商品数时统计选中总金额
                let statistic=0;
                this.checkedOnes.forEach(function(item){
                    that.cartInfoBox.forEach(function(innerItem){
                        if(item == innerItem.id){
                            statistic+=(innerItem.product.data.price*innerItem.quantity);
                        }
                    })
                })
                this.amount= statistic.toFixed(2);

                //修改购物车内商品数量
                this.axios.patch('https://grouponapi.top/api/shoppingcart/'+cartId+ '?quantity='+value)
                .then(res => {

                }).catch(function(error){
                    console.log(error);
                    if(error.response.status == 400){
                        var arr=error.response.data.message.split('_');
                        that.$message({
                            message: arr[0],
                            type: 'error'
                        })
                        that.cartInfoBox.forEach(function (item) {
                            if(item.id == cartId){
                                item.quantity=arr[1];
                            }
                        })

                    }
                })
            }
        },
        changeAllChecked(){
            if(!this.checkedAll){
                this.checkedOnes=this.checkedOneArr;
            }else{
                this.checkedOnes=[];
            }
        },
        deleteOne(cartId){
            let that=this;
            this.cartIds.data.push(cartId);
            this.axios.delete('https://grouponapi.top/api/shoppingcart/del',{
                data:{
                    carts: JSON.stringify(this.cartIds)
                }
            }).then(res => {
                 that.$message({
                     message: '删除成功！',
                     type: 'success'
                 })
                 that.$router.go(0);
             }).catch(function(error){
                 console.log(error);
             })
        },
        deleteArr(){
            let that=this;
            this.checkedOnes.forEach(function (item) {
                that.cartIds.data.push(item);
            })
            if(this.cartIds.data.length==0){
                 that.$message({
                     message: '请选择需要删除的商品！',
                     type: 'error'
                 })
            }else{
                this.axios.delete('https://grouponapi.top/api/shoppingcart/del',{
                    data:{
                        carts: JSON.stringify(this.cartIds)
                    }
                }).then(res => {
                    that.$message({
                        message: '删除成功！',
                        type: 'success'
                    })
                    that.$router.go(0);
                }).catch(function(error){
                    console.log(error);
                })
            }

        },
        //结算
        accountBtn(){
            let that=this;
            let products = {data:[]};
            this.checkedOnes.forEach(function(item){
                that.cartInfoBox.forEach(function(innerItem){
                    if(innerItem.id==item){
                      let obj={};
                      obj.product_id=innerItem.product_id;
                      obj.quantity=innerItem.quantity;
                      products.data.push(obj);
                    }
                })
            })
            this.$router.push({
                path:'/order/view',
                query: {
                    products: JSON.stringify(products),
                    type:'cart'
                }
            })
        }
    },
    //监听checkedOnes（选中复选框的数组）
    watch: {
		"checkedOnes": function() {
			if (this.checkedOnes.length == this.checkedOneArr.length) {
				this.checkedAll = true;
			} else {
				this.checkedAll = false
			}
            if(this.checkedOnes.length){
                let that=this;
                let statistic=0;
                //计算总金额
                this.checkedOnes.forEach(function(item){
                    that.cartInfoBox.forEach(function(innerItem){
                        if(item == innerItem.id){
                            statistic+=(innerItem.product.data.price*innerItem.quantity);
                        }
                    })
                })
                //计算出的金额赋给绑定数据
                this.amount= statistic.toFixed(2);
                this.disabled=false;
                this.backgroundColor='#ff5000'
            }else{
                this.amount=0
                this.disabled=true;
                this.backgroundColor='#ccc'
            }
		}
	}
};
</script>
<style scoped>
/* @import url(); 引入css类 */
@import url('../../assets/css/shoppingCart.css');
</style>
