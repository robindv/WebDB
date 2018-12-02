import Vue from 'vue';
import Vuex, {StoreOptions} from 'vuex';
import createPersistedState from 'vuex-persistedstate';


Vue.use(Vuex);

import _ from 'lodash';

const store = new Vuex.Store({

  getters: {
    isLoggedIn(state) {
      return !_.isEmpty(state.user);
    },

    course(state): any {
      return state.course;
    },

    user(state): any {
      return state.user;
    },

  },

  state: {
    course: { },
    user: { },
    token: { },
  },
  mutations: {
    course(state, course) {
      state.course = course;
    },

    token(state, token) {
      state.token = token;
    },

    user(state, user) {
      state.user = user;
    },

  },
  actions: {

  },

  plugins: [ createPersistedState({paths: ['token'] }) ],

});

export default store;
