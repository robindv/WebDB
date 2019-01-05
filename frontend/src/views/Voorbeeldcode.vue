<template>
  <div class="voorbeelcode">
    <h2>Voorbeeldcode</h2>

    <p>Om je opweg te helpen met het webprogrammeren is er voorbeeldcode beschikbaar.</p>
    <p>Deze code kun je raadplegen op <a :href="examples_site_url"> {{ course.examples_site }}</a>.
       Je kunt door de mappen en bestanden bladeren en de broncode bekijken.
       Het wachtwoord voor deze website kun je vinden in de cursushandleiding.</p>
  </div>
</template>

<script lang="ts">
import Vue from 'vue';
import { AxiosResponse } from 'axios';
export default Vue.extend({
  name: 'voorbeelcode',

  computed: {
    course(): object {
      return this.$store.state.course;
    },
    examples_site_url(): string {
      return 'https://' + this.$store.state.course.examples_site;
    },
  },
});
</script>

<style scoped>
p {
  margin-bottom: 10px;
}
</style>