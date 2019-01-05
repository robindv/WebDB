import Vue from 'vue';
import App from './App.vue';
import router from './router';
import store from './store/store';

import axios, { AxiosStatic } from 'axios';
Vue.prototype.$http = axios;

import lodash, {LoDashStatic } from 'lodash';
Object.defineProperty(Vue.prototype, '$lodash', { value: lodash });

import './assets/main.scss';

declare module 'vue/types/vue' {
  interface Vue {
    $http: AxiosStatic;
    $lodash: LoDashStatic;
  }
}

Vue.config.productionTip = false;

new Vue({
  router,
  store,
  render: (h) => h(App),
}).$mount('#app');
