import Vue from 'vue';
import App from './App.vue';
import router from './router';
import store from './store';

import axios, { AxiosStatic } from 'axios';
Vue.prototype.$http = axios;

import './assets/main.scss';

declare module 'vue/types/vue' {
  interface Vue {
    $http: AxiosStatic;
  }
}

Vue.config.productionTip = false;

new Vue({
  router,
  store,
  render: (h) => h(App),
}).$mount('#app');
