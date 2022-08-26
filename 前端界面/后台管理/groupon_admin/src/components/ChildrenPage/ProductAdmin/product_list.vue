<template>
  <div id="">

   <el-row id="status_bar">
      <el-col :span="5">
        <el-select @change="dataChange" v-model="onStatus" placeholder="启用状态">
          <el-option label="全部" value="all"></el-option>
          <el-option label="禁用" value="0"></el-option>
          <el-option label="启用" value="1"></el-option>
        </el-select>
      </el-col>

      <el-col :span="5">
        <el-select @change="dataChange" v-model="recommendStatus" placeholder="推荐状态">
          <el-option label="全部" value="all"></el-option>
          <el-option label="不推荐" value="0"></el-option>
          <el-option label="推荐" value="1"></el-option>
        </el-select>
      </el-col>

      <el-col :span="10">
        <el-date-picker
          @change="dataChange"
          v-model="time_range"
          type="daterange"
          value-format="yyyy-MM-dd HH:mm:ss"
          start-placeholder="开始日期"
          end-placeholder="结束日期">
        </el-date-picker>
      </el-col>
    </el-row>


    <el-table :data="productListData" height="530" border style="width: 100%">

        <el-table-column prop="id" label="id" width="80" ></el-table-column>

        <el-table-column prop="picture" label="图片" width="80" >
          <template slot-scope="productPicture">
            <el-avatar shape="square" :size="pictureSize" :src="productPicture.row.picture_url"></el-avatar>
          </template>
        </el-table-column>

        <el-table-column prop="title" label="商品名"></el-table-column>
        <el-table-column prop="category_name" label="分类"></el-table-column>
        <el-table-column prop="price" label="价格"></el-table-column>
        <el-table-column prop="commentnum" label="评价数" ></el-table-column>
        <el-table-column prop="sales" label="销量" ></el-table-column>
        <el-table-column prop="is_recommend" label="是否推荐" width="90" >
          <template slot-scope="productListRow">
            <el-popconfirm @confirm="product_recommend(productListRow.row)" title="确定要推荐/不推荐该商品吗？">
              <el-button v-if="productListRow.row.is_recommend==1" slot="reference" type="success" size="small" >推荐</el-button>
              <el-button v-else slot="reference" type="danger" size="small" >不推荐</el-button>
            </el-popconfirm>
          </template>
        </el-table-column>
        <el-table-column prop="is_on" label="是否上架" width="80" >
          <template slot-scope="productListRow">
            <el-popconfirm @confirm="product_on(productListRow.row)" title="确定要下架/上架该商品吗？">
              <el-button v-if="productListRow.row.is_on==0" slot="reference" type="danger" size="small" >下架</el-button>
              <el-button v-else slot="reference" type="success" size="small" >上架</el-button>
            </el-popconfirm>
          </template>
        </el-table-column>
        <el-table-column prop="stock" label="库存" ></el-table-column>
        <el-table-column label="操作" width="150" >
          <template slot-scope="productListRow">
            <el-button @click="getProductInfo(productListRow.row)" type="primary" size="small">详情</el-button>

            <el-button type="success" @click="modifyProduct(productListRow.row)" size="small" >编辑</el-button>

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

    <el-dialog title="商品详情" v-if="ProductInfoVisible" :visible.sync="ProductInfoVisible">
        <el-row>
          <el-row>
             <div id="">
                 商品基本信息：
          </div>
          </el-row>
          <el-row  style="border: 1px dotted black; padding: 1rem;">
            <el-row>
              <el-col :span="4"><span>商品图：</span></el-col>
              <el-col :span="20"><el-avatar :size="100" :src="ProductInfo.picture_url"></el-avatar></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>商品id：</span></el-col>
              <el-col :span="20"><div>{{ProductInfo.id}}</div></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>商品名：</span></el-col>
              <el-col :span="20"><div>{{ProductInfo.title}}</div></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>分类：</span></el-col>
              <el-col :span="20"><div>{{ProductInfo.category_name}}</div></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>价格：</span></el-col>
              <el-col :span="20"><div>{{ProductInfo.price}}</div></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>库存：</span></el-col>
              <el-col :span="20"><div>{{ProductInfo.stock}}</div></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>销量：</span></el-col>
              <el-col :span="20"><div>{{ProductInfo.sales}}</div></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>评价数：</span></el-col>
              <el-col :span="20"><div>{{ProductInfo.commentnum}}</div></el-col>
            </el-row>

            <el-row>
               <el-col :span="4"><span>商品描述：</span></el-col>
               <el-col :span="20"><div>{{ProductInfo.description}}</div></el-col>
            </el-row>
            <el-row>
               <el-col :span="4"><span>是否推荐：</span></el-col>
               <el-col :span="20"><div>{{ProductInfo.is_recommend?'是':'否'}}</div></el-col>
            </el-row>
            <el-row>
               <el-col :span="4"><span>是否上架：</span></el-col>
               <el-col :span="20"><div>{{ProductInfo.is_on?'是':'否'}}</div></el-col>
            </el-row>
          </el-row>
        </el-row>


        <el-row>
          <el-row>
             <div id="">
                 商品属性：
             </div>
          </el-row>
          <el-row  style="border: 1px dotted black; padding: 1rem;">

            <el-row>
              <el-col :span="4"><span>品牌：</span></el-col>
              <el-col :span="20"><div>{{ProductInfo.attribute.brand}}</div></el-col>
            </el-row>
            <el-row>
              <el-col :span="4"><span>产地：</span></el-col>
              <el-col :span="20"><div>{{ProductInfo.attribute.place}}</div></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>生产日期：</span></el-col>
              <el-col :span="20"><div>{{ProductInfo.attribute.production_date}}</div></el-col>
            </el-row>

            <el-row>
              <el-col :span="4"><span>保质期：</span></el-col>
              <el-col :span="20"><div>{{ProductInfo.attribute.shelf_life}}</div></el-col>
            </el-row>

            <el-row>
               <el-col :span="4"><span>型号：</span></el-col>
               <el-col :span="20"><div>{{ProductInfo.attribute.size}}</div></el-col>
            </el-row>

            <el-row>
               <el-col :span="4"><span>保存方法：</span></el-col>
               <el-col :span="20"><div>{{ProductInfo.attribute.storage_method}}</div></el-col>
            </el-row>
          </el-row>
        </el-row>


        <el-row>
          <el-row>
             <div id="">
                 商品展示图：
             </div>
          </el-row>
          <el-row  style="border: 1px dotted black; padding: 1rem;">

            <el-row v-for="item in ProductInfo.pics_url" :key="item">
              <el-col :span="20"><div><img style="width: 100%;" :src="item" ></div></el-col>
            </el-row>


          </el-row>
        </el-row>

    </el-dialog>

    <el-dialog title="更新" v-if="ProductModifyVisible" :visible.sync="ProductModifyVisible">
        <el-row class="updateRow">
            <el-input placeholder="请输入内容" v-model="productModifySession.title">
              <template slot="prepend">商品名</template>
            </el-input>
        </el-row>

       <el-row class="updateRow">
            <el-input placeholder="请输入内容" :disabled="inputDisabled" v-model="productModifySession.picture">
              <template slot="prepend">图片</template>
              <template slot="append">
                 <el-upload
                    :disabled="changePictureDis"
                    action=""
                    :limit="1"
                    :auto-upload="false"
                    :show-file-list="false"
                    :on-change="upLoadPictureChange">
                    <el-button size="small" :disabled="changePictureDis" type="primary">点击修改</el-button>
                  </el-upload>
              </template>
            </el-input>
        </el-row>

      <el-row class="updateRow">
            展示图：
            <el-upload
               action=""
               :limit="5"
               :multiple="true"
               :auto-upload="false"
               :on-change="upLoadPicsChange">
               <el-button size="small" type="primary">点击修改</el-button>
            </el-upload>
        </el-row>

        <el-row class="updateRow">
            <el-input :disabled="inputDisabled" placeholder="请输入内容" v-model="productModifySession.category_name">
              <template slot="prepend">分类</template>
              <template slot="append">
                <el-button size="small" @click="changeCategory" type="primary">点击修改</el-button>
              </template>
            </el-input>
        </el-row>

        <el-row class="updateRow">
            <el-input placeholder="请输入内容" v-model="productModifySession.price">
              <template slot="prepend">价格</template>
            </el-input>
        </el-row>

        <el-row class="updateRow">
            <el-input placeholder="请输入内容" v-model="productModifySession.stock">
              <template slot="prepend">库存量</template>
            </el-input>
        </el-row>

        <el-row class="updateRow">
            <el-input placeholder="请输入内容" v-model="productModifySession.description">
              <template slot="prepend">详情</template>
            </el-input>
        </el-row>

        <el-row>
          <el-row>
             <div id="">
                 商品属性：
             </div>
          </el-row>
          <el-row  style="border: 1px dotted black; padding: 1rem;">

            <el-row class="updateRow">
                <el-input placeholder="请输入内容" v-model="productModifySession.attribute.brand">
                  <template slot="prepend">品牌</template>
                </el-input>
            </el-row>

            <el-row class="updateRow">
                <el-input placeholder="请输入内容" v-model="productModifySession.attribute.place">
                  <template slot="prepend">产地</template>
                </el-input>
            </el-row>

            <el-row class="updateRow">
                <el-input placeholder="请输入内容" v-model="productModifySession.attribute.production_date">
                  <template slot="prepend">生产日期</template>
                </el-input>
            </el-row>

            <el-row class="updateRow">
                <el-input placeholder="请输入内容" v-model="productModifySession.attribute.shelf_life">
                  <template slot="prepend">保质期</template>
                </el-input>
            </el-row>

            <el-row class="updateRow">
                <el-input placeholder="请输入内容" v-model="productModifySession.attribute.size">
                  <template slot="prepend">型号</template>
                </el-input>
            </el-row>

            <el-row class="updateRow">
                <el-input placeholder="请输入内容" v-model="productModifySession.attribute.storage_method">
                  <template slot="prepend">存储方式</template>
                </el-input>
            </el-row>

          </el-row>
        </el-row>

        <el-row class="updateRow">
            <el-button type="danger" @click="updateProduct" size="small" >提交更新</el-button>
        </el-row>
    </el-dialog>

    <el-dialog title="更改分类" v-if="changeCategoryVisible" :visible.sync="changeCategoryVisible">
      <el-cascader
          @change="confirmCategory"
          v-model="checkedCategory"
          placeholder="试试搜索：指南"
          :options="category_list"
          filterable>
      </el-cascader>
    </el-dialog>

  </div>
</template>

<script>
  export default {
    name:"UserList",
    data(){
      return {
        productListData:[],
        ProductInfo:[],
        ProductInfoVisible:false,
        ProductModifyVisible:false,
        onStatus:'',
        recommendStatus:'',
        time_range:null,
        paginationInfo:'',
        nowPage:1,
        pictureSize:55,
        inputDisabled:true,

        productModifySession:'',

        uploadPicture:'',
        uploadPictureSwitch:false,

        uploadPics:'',
        uploadPicsSwitch:false,

        changePictureDis:false,

        changeCategoryVisible:false,

        category_list:[],
        checkedCategory:''
      };
    },
    mounted(){
      var that=this;
      this.axios.get('https://grouponapi.top/api/admin/product').then(res=>{

        that.productListData=res.data.data
        that.paginationInfo=res.data.meta.pagination
      }).catch(function(error){
        that.$message({
          message: error.response.data.message,
          type:'error'
        });
      })
    },
    methods:{

      //商品详情
      getProductInfo(row){
        var that=this;
        this.axios.get('https://grouponapi.top/api/admin/product/'+row.id).then(res=>{
            that.ProductInfo=res.data.data;
            this.ProductInfoVisible=true


        }).catch(function(error){
          that.$message({
            message: error.response.data.message,
            type:'error'
          });
        })
      },

      //修改商品信息
      modifyProduct(row){
        var that=this;

        this.axios.get('https://grouponapi.top/api/admin/product/'+row.id).then(res=>{
            that.productModifySession=res.data.data;
            that.ProductModifyVisible=true;


        }).catch(function(error){
          that.$message({
            message: error.response.data.message,
            type:'error'
          });
        })

      },

      //是否上架
      product_on(row){
        var that=this;
        this.axios.patch('https://grouponapi.top/api/admin/product/'+row.id+'/on').then(res=>{
          that.$message({
            message: '操作成功！',
            type:'success'
          });
          row.is_on=row.is_on==0?1:0;
        }).catch(function(error){
          that.$message({
            message: error.response.data.message,
            type:'error'
          });
        })
      },

      //是否推荐
      product_recommend(row){
        var that=this;
        this.axios.patch('https://grouponapi.top/api/admin/product/'+row.id+'/recommend').then(res=>{
          that.$message({
            message: '操作成功！',
            type:'success'
          });
          row.is_recommend=row.is_recommend==0?1:0;
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
        let is_on=this.onStatus=='all'||this.onStatus==''?'':'is_on='+this.onStatus+'&';
        let is_recommend=this.recommendStatus=='all'||this.recommendStatus==''?'':'is_recommend='+this.recommendStatus+'&';

        let time_begin=this.time_range==null?'':'time_begin='+this.time_range[0]+'&';
        let time_end=this.time_range==null?'':'time_end='+this.time_range[1]+'&';

        let page=this.nowPage==0?'':'page='+this.nowPage+'&'


        this.axios.get('https://grouponapi.top/api/admin/product?'+is_on+is_recommend+time_begin+time_end+page).then(res=>{
          that.productListData=res.data.data;
          that.paginationInfo=res.data.meta.pagination;
        }).catch(function(error){
          that.$message({
            message: error.response.data.message,
            type:'error'
          });
        })

      },


      upLoadPictureChange(file){
        this.changePictureDis=true;
        this.uploadPictureSwitch=true;
        this.uploadPicture=file;
        this.productModifySession.picture=file.name;

      },

      upLoadPicsChange(file,fileList){

        this.uploadPicsSwitch=true
        this.uploadPics=fileList

      },


      updateProduct(){
        var that=this;
        if(this.uploadPictureSwitch){

          that.axios.get('https://grouponapi.top/api/auth/oss/token')
          .then(res => {

              var data = res.data;


              var ossData = new FormData();
              ossData.append("name", that.uploadPicture.raw.name);
              //获取图片类型 后缀 jpg / png
              let imgType = that.uploadPicture.raw.type.split("/")[1];
              let filename = that.uploadPicture.raw.name; //md5对图片名称进行加密
              let keyValue = "product_picture/picture_" + that.productModifySession.id+ '.' + imgType;
              //文件名
              ossData.append("key", keyValue);
              ossData.append("policy", data.policy);
              ossData.append("OSSAccessKeyId", data.accessid);
              ossData.append("success_action_status", 201);
              ossData.append("signature", data.signature);
              ossData.append("file", that.uploadPicture.raw, that.uploadPicture.raw.name);
              that.axios.post(data.host, ossData, {
                  headers: {
                  "Content-Type": "multipart/form-data"
                  }
              })
              .then(res => {
                  if (res.status == 201) {
                      that.productModifySession.picture=(keyValue.split("/")[1]);
                  }
              }).catch(function (error) {
                  console.log(error);
              });
          })

        }

        if(this.uploadPicsSwitch){
          this.productModifySession.pics=[];

          this.uploadPics.forEach(function(item,index){

            that.axios.get('https://grouponapi.top/api/auth/oss/token')
            .then(res => {

                var data = res.data;


                var ossData = new FormData();
                ossData.append("name", item.raw.name);
                //获取图片类型 后缀 jpg / png
                let imgType = item.raw.type.split("/")[1];
                let filename = item.raw.name; //md5对图片名称进行加密
                let keyValue = "product_picture/pics_"+index+'_'+ that.productModifySession.id+ '.' + imgType;
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
                      that.productModifySession.pics.push(keyValue.split("/")[1]);
                    }
                }).catch(function (error) {
                    console.log(error);
                });
            })

          })

        }

        setTimeout(function(){
            that.axios.patch('https://grouponapi.top/api/admin/product/'+that.productModifySession.id,{

              title:that.productModifySession.title,
              picture:that.productModifySession.picture,
              pics:JSON.stringify({urls:that.productModifySession.pics}),
              stock:that.productModifySession.stock,
              category_id:that.productModifySession.category_id,
              price:that.productModifySession.price,
              description:that.productModifySession.description,
              attribute:JSON.stringify(that.productModifySession.attribute)

            }).then(res=>{

              that.$message({
                message: '修改成功！',
                type:'success'
              });

            }).catch(function(error){
              that.$message({
                message: error.response.data.message,
                type:'error'
              });
            })
        },1000)


      },
      //改变分类
      changeCategory(){
        var that=this;
        this.changeCategoryVisible=true;
        this.axios.get('https://grouponapi.top/api/basic/category/list').then(res=>{
          res.data.forEach(function(item,index){
            var options={};
            options.label=item.name;
            options.value=item.id;
            options.children=[];
            item.children.forEach(function(innerItem){
                var options_child={};
                options_child.label=innerItem.name;
                options_child.value=innerItem.id;
                options.children.push(options_child)
            })
            that.category_list.push(options)
          })

        })

      },
      confirmCategory(val){
        this.productModifySession.category_id=val[1];
        var label_name='';
        this.category_list.forEach(function(item){
            item.children.forEach(function(innerItem){
              if(innerItem.value==val[1]){
                label_name=innerItem.label;
              }
            })
        })
        this.productModifySession.category_name=label_name;
      }


    }
  }
</script>

<style>

  #pagination{
    text-align: center;
    margin-top: 0.2rem;
  }
  .updateRow{
    margin-bottom: 0.5rem;
  }
</style>
