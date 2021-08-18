import Vue from 'vue'
// import VueRouter from 'vue-router'

import { Alert, Button, Loading, PageHeader, Table, TableColumn, Message } from 'element-ui'
import lang from 'element-ui/lib/locale/lang/vi'
import locale from 'element-ui/lib/locale'

import App from './vue/App.vue'
// import routes from './router/index'

import './scss/app.scss'

locale.use(lang)

// Vue.use(VueRouter)
Vue.use(Alert)
Vue.use(Button)
Vue.use(Loading)
Vue.use(PageHeader)
Vue.use(Table)
Vue.use(TableColumn)
Vue.prototype.$message = Message

// const router = new VueRouter({
//   routes,
// })

new Vue({
  el: '#vuj_coupon',
  // router,
  render: (h) => h(App),
})
