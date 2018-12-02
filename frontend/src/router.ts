import Vue from 'vue';
import Router from 'vue-router';
import Home from './views/Home.vue';

import store from './store/store';

Vue.use(Router);

export default new Router({
  mode: 'history',
  base: process.env.BASE_URL,
  routes: [
    {
      path: '/',
      name: 'home',
      component: Home,
    },
    {
      path: '/voorbeeldcode',
      name: 'voorbeeldcode',
      // route level code-splitting
      // this generates a separate chunk (about.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import(/* webpackChunkName: "about" */ './views/Voorbeeldcode.vue'),
    },
    {
      path: '/login',
      beforeEnter(to, from, next) {
        const callbackUrl = window.origin + '/login_callback';
        window.location.href = 'https://secure.uva.nl/cas/login?service=' + callbackUrl;
      },
    },
    {
      path: '/login_callback',
      name: 'login_callback',
      component: () => import('./views/LoginCallback.vue'),
    },

    {
      path: '/profile',
      name: 'Profile',
      component: () => import('./views/Profile.vue'),
    },
  ],
});
