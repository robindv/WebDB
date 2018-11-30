<template>
  <div class="hello">
    <h1>{{ msg }}</h1>
 Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce ipsum tellus, congue ac accumsan ut, fermentum in lorem. Donec vitae est a augue commodo lacinia a id nisi. Cras ultricies pharetra eros, vel iaculis orci venenatis eu. Praesent sodales purus vitae ex sagittis luctus. Nullam facilisis consequat sapien id vestibulum. Mauris vehicula, libero nec egestas pellentesque, massa dolor euismod mauris, vitae venenatis metus velit a neque. Phasellus a condimentum odio, in faucibus elit. In a orci non dui efficitur blandit non id ex. Nulla mauris odio, tempus sed massa vitae, tristique vehicula mauris. Morbi accumsan fermentum tempus. Morbi eu posuere mi, vel lacinia velit.

Etiam in augue nec tellus facilisis tempor. In vitae mi nulla. Phasellus consequat risus quis lorem pretium tempor. Nullam rhoncus magna non purus ultricies vehicula. Vestibulum sit amet condimentum dui. Curabitur ac sapien ex. Maecenas elementum aliquam felis, sed pulvinar nisi porttitor vel. Donec lobortis porttitor velit in porta. Fusce accumsan nisi gravida lorem auctor sagittis eu ac justo. Aliquam tempus facilisis nulla non pulvinar. Proin semper scelerisque libero, ac sollicitudin sem commodo ut. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Suspendisse ut volutpat eros. Fusce ultricies magna vel sapien luctus porttitor. Phasellus neque dolor, aliquet sed sem quis, volutpat sollicitudin lorem. Donec rutrum nec nibh quis interdum. 
  </div>
</template>

<script lang="ts">
import Vue from 'vue';

export default Vue.extend({
  name: 'HelloWorld',
  props: {
    msg: String,
  },
});
</script>
<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="scss">
h3 {
  margin: 40px 0 0;
}
ul {
  list-style-type: none;
  padding: 0;
}
li {
  display: inline-block;
  margin: 0 10px;
}
a {
  color: #42b983;
}
</style>
