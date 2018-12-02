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
      <li><router-link to="/voorbeeldcode">Voorbeeldcode</router-link></li>
      <li><router-link to="/login" v-if="!isLoggedIn">Inloggen</router-link></li>
      <li><router-link to="/profile" v-if="isLoggedIn">{{ user.firstname }}</router-link></li>
      <li><router-link to="/logout" v-if="isLoggedIn">Uitloggen</router-link></li>
    </ul>
    </div>
    <div id="main">
      <router-view/>
    </div>
  </div>
</template>


<script lang="ts">
import Vue from 'vue';
import { AxiosResponse } from 'axios';
import { mapGetters } from 'vuex';

export default Vue.extend({

  computed: {
    ...mapGetters(['isLoggedIn', 'user', 'course']),
  },

  // data() {

  // },

  mounted() {

    if (this.$store.state.token) {
      this.$http.defaults.headers.common.Authorization = 'Bearer ' + this.$store.state.token.access_token;

      this.$http.get('api/user')
      .then((response: AxiosResponse) => {
          this.$store.commit('user', response.data);
      });
    }


    this.$http.get('api/course')
    .then((response: AxiosResponse) => {
        this.$store.commit('course', response.data);
    });
  },

});
</script>
