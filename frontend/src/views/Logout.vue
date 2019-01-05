<template>
<p>hi</p>
</template>

<script lang="ts">
import Vue from 'vue';

export default Vue.extend({
    mounted() {
        this.$store.commit('token', null);
        this.$store.commit('user', {});
        this.$router.push('/');
    },
});


</script>