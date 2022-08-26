<template>
  <div id="">




    <el-table :data="DeliveryListData" height="530" border style="width: 100%">

        <el-table-column prop="id" label="id" width="80" ></el-table-column>
        <el-table-column prop="store_name" label="店铺名"></el-table-column>
        <el-table-column prop="email" label="店铺图片">
          <template slot-scope="deliveryListRow">
              <el-col :span="20"><el-avatar shape="square" :size="50" :src="deliveryListRow.row.pic_url"></el-avatar></el-col>
          </template>
        </el-table-column>
        <el-table-column prop="head_name" label="团长姓名"></el-table-column>
        <el-table-column prop="tel" label="电话" ></el-table-column>
        <el-table-column prop="order_count" label="订单总数" ></el-table-column>
        <el-table-column label="操作" >
          <template slot-scope="deliveryListRow">
            <el-button @click="getDeliveryInfo(deliveryListRow.row)" type="primary" size="small">详情</el-button>
            <el-button v-if="deliveryListRow.row.order_count!=0" @click="fillSendSession(deliveryListRow.row)" slot="reference" type="danger" size="small" >发货</el-button>


          </template>
        </el-table-column>

    </el-table>

    <!-- 分页 -->
    <el-pagination
          id="pagination"
          @current-change="dataChange('page')"
          :current-page.sync="nowPage"
          :page-size="paginationInfo.per_page"
          layout="prev, pager, next, jumper"
          :total="paginationInfo.total">
    </el-pagination>

    <el-dialog title="用户详情" v-if="DeliveryInfoVisible" :visible.sync="DeliveryInfoVisible">

        <el-row>
          <el-row>
             <div id="">
                 自提点信息：
             </div>
          </el-row>
          <el-row  style="border: 1px dotted black; padding: 1rem;">

            <el-row>
             <el-col :span="19"><el-avatar shape="square" :size="100" :src="DeliveryInfo.pic_url"></el-avatar></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>自提点名：</span></el-col>
              <el-col :span="20"><div>{{DeliveryInfo.store_name}}</div></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>地区：</span></el-col>
              <el-col :span="20"><div>{{DeliveryInfo.address_name}}</div></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>详细地址：</span></el-col>
              <el-col :span="20"><div>{{DeliveryInfo.address_info}}</div></el-col>
            </el-row>

            <el-row>
              <el-col :span="6"><span>自提点状态：</span></el-col>
              <el-col :span="18"><div>{{DeliveryInfo.status}}</div></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>团长id：</span></el-col>
              <el-col :span="20"><div>{{DeliveryInfo.head_id}}</div></el-col>
            </el-row>


          </el-row>
        </el-row>


        <el-row>
          <el-row>
             <div id="">
                 商品信息：
             </div>
          </el-row>
          <el-row v-for="item in DeliveryInfo.products" :key="item.product_id"  style="border: 1px dotted black; padding: 1rem;">

            <el-row>
             <el-col :span="19"><el-avatar shape="square" :size="100" :src="item.picture_url"></el-avatar></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>商品id：</span></el-col>
              <el-col :span="20"><div>{{item.product_id}}</div></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>商品名：</span></el-col>
              <el-col :span="20"><div>{{item.title}}</div></el-col>
            </el-row>


            <el-row>
              <el-col :span="4"><span>数量：</span></el-col>
              <el-col :span="20"><div>{{item.quantity}}</div></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>总金额：</span></el-col>
              <el-col :span="20"><div>{{item.total_amount}}</div></el-col>
            </el-row>



          </el-row>
        </el-row>


    </el-dialog>


    <el-dialog title="填写发货信息" v-if="sendDeliveryVisible" :visible.sync="sendDeliveryVisible">
          <el-row class="updateRow">
              <el-input placeholder="请输入内容"  v-model="express_type">
                <template slot="prepend">快递类型</template>
              </el-input>
          </el-row>
          <el-row class="updateRow">
              <el-input placeholder="请输入内容"  v-model="express_number">
                <template slot="prepend">快递单号</template>
              </el-input>
          </el-row>
       <el-popconfirm @confirm="sendDelivery(delivery_id)" title="确定要发货吗？">
          <el-button v-if="delivery_id.order_count!=0" slot="reference" type="danger" size="small" >发货</el-button>
        </el-popconfirm>
    </el-dialog>

  </div>
</template>

<script>
  export default {
    name:"UserList",
    data(){
      return {
        DeliveryListData:[],
        DeliveryInfo:[],
        DeliveryInfoVisible:false,

        userStatus:'',
        userSex:'',
        time_range:null,
        paginationInfo:'',
        nowPage:1,


        sendDeliveryVisible:false,

        delivery_id:'',

        express_type:'',
        express_number:''
      };
    },
    mounted(){
      var that=this;
      this.axios.get('https://grouponapi.top/api/admin/delivery/list').then(res=>{

        that.DeliveryListData=res.data.data
        that.paginationInfo=res.data.meta.pagination
      }).catch(function(error){
        that.$message({
          message: error.response.data.message,
          type:'error'
        });
      })
    },
    methods:{

      //自提点详情
      getDeliveryInfo(row){
        var that=this;
        this.axios.get('https://grouponapi.top/api/admin/delivery/info/'+row.id).then(res=>{


            that.DeliveryInfo=res.data.data;
            this.DeliveryInfoVisible=true

        }).catch(function(error){
          that.$message({
            message: error.response.data.message,
            type:'error'
          });
        })
      },

      fillSendSession(row){

          this.sendDeliveryVisible=true
          this.delivery_id=row
      },

      //发货
      sendDelivery(row){

        var that=this;
        this.axios.patch('https://grouponapi.top/api/admin/delivery/'+row.id+'/order',{
          express_type:that.express_type,
          express_no:that.express_number
        }).then(res=>{
          that.$message({
            message: '操作成功！',
            type:'success'
          });
          row.order_count=0;
          sendDeliveryVisible=false;
        }).catch(function(error){
          that.$message({
            message: error.response.data.message,
            type:'error'
          });
        })
      },

      //状态改变
      dataChange(type){

        if(type!='page'){
          this.nowPage=1
        }

        var that=this;


        let page=this.nowPage==0?'':'page='+this.nowPage+'&'


        this.axios.get('https://grouponapi.top/api/admin/delivery/list?'+page).then(res=>{
          that.DeliveryListData=res.data.data;
          that.paginationInfo=res.data.meta.pagination;
        }).catch(function(error){
          that.$message({
            message: error.response.data.message,
            type:'error'
          });
        })

      },


    }
  }
</script>

<style>

  #pagination{
    text-align: center;
    margin-top: 0.2rem;
  }
</style>
