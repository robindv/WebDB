<template>
    <div class="profile">
        <h1>Profiel</h1>

        <table class="table profile-table">
            <tr><th>Naam</th><td>{{ user.name }}</td></tr>
            <tr><th>UvAnetID</th><td>{{ user.uvanetid }}</td></tr>
            <tr><th>E-mailadres</th><td>{{ user.email }}</td></tr>
        </table>

        <h2>Mijn wachtwoorden</h2>

        <table class="table is-fullwidth is-striped">
            <thead>
                <tr><th>Server</th><th>Groep</th><th>Gebruikersnaam</th><th>Wachtwoord</th></tr>
            </thead>
            <tbody>
                <tr v-for="user in serverusers" :key="user.id">
                    <td>{{ user.server.hostname }}</td>
                    <td>{{ user.server.group.name || "-" }}</td>
                    <td>{{ user.username }}</td>
                    <td>{{ user.password }}</td>
                </tr>
            </tbody>
        </table>

    </div>
</template>

<script lang="ts">
import Vue from 'vue';
import { AxiosResponse } from 'axios';
import { mapGetters } from 'vuex';
export default Vue.extend({
    name: 'Profile',

    computed: {
        ...mapGetters(['user']),
    },

    data() {
        return {
            serverusers: null,
        };
    },

    mounted() {
      this.$http.get('api/user/serverusers')
      .then((response: AxiosResponse) => {
          this.serverusers = response.data;
      });
    },

});
</script>


<style lang="scss" scoped>
.profile-table {
     table, tr, th, td {
        border: 0;
    }

    th, td {
        padding-top: 0;
        padding-bottom: 0;
    }
}


</style>
