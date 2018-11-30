<template>
  <div class="about">
    <h2>This is an about page</h2>

    <p>Data: {{ info}}</p>

  </div>
</template>

<script lang="ts">
import Vue from 'vue';
import { AxiosResponse } from 'axios';
export default Vue.extend({

  data() {
    return {
        info: null,
    };
  },

  mounted() {

    this.$http.get('api')
    .then((response: AxiosResponse) => {
        this.info = response.data;
    });

  },

});
</script>