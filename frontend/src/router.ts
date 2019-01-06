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
      path: '/handleiding',
      name: 'handleiding',
      component: () => import('./views/Handleiding.vue'),
    },
    {
      path: '/login_callback',
      name: 'login_callback',
      component: () => import('./views/LoginCallback.vue'),
    },

    {
      path: '/logout',
      name: 'logout',
      component: () => import('./views/Logout.vue'),
    },

    {
      path: '/profile',
      name: 'Profile',
      component: () => import('./views/Profile.vue'),
    },

    {
      path: '/student/project',
      name: 'Project',
      component: () => import('./views/student/Project.vue'),
    },

    {
      path: '/student/server',
      name: 'StudentServer',
      component: () => import('./views/student/Server.vue'),
    },

    {
      path: '/staff/students',
      name: 'Students',
      component: () => import('./views/staff/Students.vue'),
    },

    {
      path: '/staff/groups',
      name: 'Groups',
      component: () => import('./views/staff/Groups.vue'),
    },

    {
      path: '/admin/servers',
      name: 'Servers',
      component: () => import('./views/admin/Servers.vue'),
    },

    {
      path: '/admin/servers/:id',
      name: 'AdminServer',
      component: () => import('./views/admin/Server.vue'),
    },

  ],
});
