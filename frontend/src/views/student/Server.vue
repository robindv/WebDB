<template>
    <div>
        <h1>Server</h1>

        <div v-if="serverusers.length == 1">

            <table class="table">
                <tr><th>Naam</th><td><a :href="'http://' + serveruser.server.hostname">{{ serveruser.server.hostname }}</a></td></tr>
                <tr><th>IP-adres</th><td>{{ serveruser.server.ip_address }}</td></tr>
            </table>

            <h2>Inloggegevens</h2>
            <p>Met onderstaande gegevens kun je inloggen op SSH en MySQL.</p>
            <table class="table">
                <tr><th>Gebruikersnaam</th><td>{{ serveruser.username }}</td></tr>
                <tr><th>Wachtwoord</th><td>{{ serveruser.password }}</td></tr>
                <tr><th>SSH-commando</th><td>ssh {{ serveruser.username }}@{{ serveruser.server.hostname }}</td></tr>
                <tr><th>phpMyAdmin</th><td><a :href="'https://' + serveruser.server.hostname + '/phpmyadmin'">https://{{ serveruser.server.hostname }}/phpmyadmin</a></td></tr>
            </table>

            <h2>Contact</h2>
            Problemen met je server? Neem contact op met <a :href="'mailto:'+ course.admin_email">{{ course.admin_email }}</a>

        </div>
        <div v-else>
            Je server is nog niet geconfigureerd. Zou dit wel al moeten?  Neem contact op met <a :href="'mailto:'+ course.admin_email">{{ course.admin_email }}</a>.
        </div>

    </div>
</template>

<script lang="ts">
import Vue from 'vue';
import { AxiosResponse } from 'axios';
import { mapGetters } from 'vuex';
export default Vue.extend({
    data() {
        return {
            serverusers: [],
        };
    },

    computed: {
        ...mapGetters(['course']),
        serveruser(): object {
            return this.serverusers[0];
        },
    },

    mounted() {
      this.$http.get('/api/user/serverusers')
      .then((response: AxiosResponse) => {
          this.serverusers = response.data;
      });
    },
});
</script>

<style lang="scss" scoped>
table {
     table, tr, th, td {
        border: 0;
    }

    th, td {
        padding-top: 0;
        padding-bottom: 0;
    }
}
</style>