import Vue from 'vue'
import Router from 'vue-router'
import axios from 'axios'

Vue.use(Router)

Vue.prototype.axios=axios

import Adminlogin from '../components/AdminLogin/AdminLogin'
import AdminPanel from '../components/AdminPanel/AdminPanel'

export default new Router({
  mode:'history',
  routes: [
    {
        path: '/admin/login',
        name: 'Adminlogin',
        component: Adminlogin,
        meta: {
          title: '管理登录',
          type: ''
        }
    },
    {
        path: '/admin/panel',
        name: 'AdminPanel',
        component: AdminPanel,
        meta: {
          title: '管理面板',
          type: 'adminlogin'
        }
    }
  ]
})
