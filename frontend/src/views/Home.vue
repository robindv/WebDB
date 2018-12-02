<template>
  <div class="home">
    <h2>{{ course.name }}</h2>
    <p>Welkom op de website van het vak {{ course.name }}.</p>
  </div>
</template>

<script lang="ts">
import Vue from 'vue';
import HelloWorld from '@/components/HelloWorld.vue'; // @ is an alias to /src

export default Vue.extend({
  name: 'home',

  computed: {
    course(): string {
      return this.$store.state.course;
    },
  },

});
</script>
