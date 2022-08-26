// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import router from './router'
import axios from './axios'
import ElementUI from 'element-ui'
import 'element-ui/lib/theme-chalk/index.css'
import 'element-ui/lib/theme-chalk/display.css'


Vue.prototype.axios=axios
Vue.use(ElementUI)
Vue.config.productionTip = false


router.beforeEach((to, from, next) => {
  if(to.meta.title){
    document.title = to.meta.title;
  }
  let type=to.meta.type;
  if(type === 'login'){
    if(localStorage.getItem('token')){
      var nowTime = +new Date();
      if(nowTime >= localStorage.getItem('expires_time')){
        axios.post('https://grouponapi.top/api/auth/refresh')
        .then(res => {
        localStorage.setItem('token',res.data.token_type+' '+res.data.access_token);
        localStorage.setItem('expires_time',+new Date() + res.data.expires_in);
        })
      }
      next();
    }else{
      next('/login')
    }
  }else{
    next()
  }
})

/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  components: { App },
  template: '<App/>'
})
