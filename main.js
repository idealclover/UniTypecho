import Vue from 'vue'
import App from './App'

import list from './pages/list/list.vue'
Vue.component('list',list)

import rank from './pages/rank/rank.vue'
Vue.component('rank',rank)

import about from './pages/about/about.vue'
Vue.component('about',about)

Vue.config.productionTip = false

App.mpType = 'app'

const app = new Vue({
    ...App
})
app.$mount()
