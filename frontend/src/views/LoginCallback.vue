<template>
    <div>
        <h2>Inloggen</h2>
    </div>
</template>

<script lang="ts">
import Vue from 'vue';
export default Vue.extend({
    mounted() {
        const ticket = this.$route.query.ticket;

        this.$http.get('api/auth/get_token_from_ticket?ticket=' + ticket)
         .then((response) => {
            this.$store.commit('token', response.data);

            this.$http.defaults.headers.common.Authorization = 'Bearer ' + response.data.access_token;
        });

    },
});
</script>
