<template>
  <div id="app">
    <div id="header">
      <div id="logo">
        <div>{{ course.name }}</div>
      </div>
    </div>
    <div id="nav">
    <ul id="menu">
      <li><router-link to="/">Home</router-link></li>


        <li><router-link to="/voorbeeldcode">Handleiding</router-link></li>

      <template v-if="user.is_teacher || user.is_assistant">
        <li><router-link to="/staff/students">Studenten</router-link></li>
        <li><router-link to="/staff/groups">Groepen</router-link></li>
      </template>

      <template v-if="user.is_student">
        <li><router-link to="/student/project">Project</router-link></li>
        <li><router-link to="/student/server">Server</router-link></li>
      </template>

      <li><router-link to="/admin/servers" v-if="user.is_admin">Servers</router-link></li>

      <li><a href="/api/login" v-if="!isLoggedIn">Inloggen</a></li>
      <li><router-link to="/profile" v-if="isLoggedIn">{{ user.firstname }}</router-link></li>
      <li><router-link to="/logout" v-if="isLoggedIn">Uitloggen</router-link></li>
    </ul>
    </div>
    <div id="main">
      <router-view />
    </div>
  </div>
</template>


<script lang="ts">
import Vue from 'vue';
import { AxiosResponse, AxiosError } from 'axios';
import { mapGetters } from 'vuex';

Vue.directive('title', {
  inserted: (el, binding) => document.title = binding.value,
  update: (el, binding) => document.title = binding.value,
});

export default Vue.extend({

  computed: {
    ...mapGetters(['isLoggedIn', 'user', 'course']),
  },

  mounted() {

    if (this.$store.state.token) {
      this.$http.defaults.headers.common.Authorization = 'Bearer ' + this.$store.state.token.access_token;

      this.$http.get('/api/user')
      .then((response: AxiosResponse) => {
          this.$store.commit('user', response.data);
      }).catch((error: AxiosError) => {
        /* Token invalid or expired */
        this.$store.commit('token', null);
        this.$store.commit('user', { });
      });
    }


    this.$http.get('/api/course')
    .then((response: AxiosResponse) => {
        this.$store.commit('course', response.data);
    });
  },

  watch: {
    course(newCourse, oldCourse) {
      document.title = this.$store.state.course.name;
    },
  },

});
</script>
