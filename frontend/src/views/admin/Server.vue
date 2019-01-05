<template>

    <div>

        <div v-if="server == null" class="element is-loading" style="height: 75px"></div>

    <div v-if="server != null">
        <h1>Server {{server.name }}</h1>


        <table class="table info-table">
            <tr><th>Hostname</th><td><a :href="'http://' + server.hostname">{{ server.hostname }}</a></td></tr>
            <tr><th>IP-adres</th><td>{{ server.ip_address }}</td></tr>
            <tr><th>State</th><td>{{ server.state }}</td></tr>
            <tr><th>Groep</th><td>{{ server.group == null ? "" : server.group.name }}</td></tr>
        </table>

        <h2>SSL Certificaat</h2>

        <table class="table info-table">
            <tr><th>Issuer</th><td>{{ server.ssl_issuer }}</td></tr>
            <tr><th>Geldig vanaf</th><td>{{ format_date(server.ssl_valid_from) }}</td></tr>
            <tr><th>Geldig tot</th><td>{{ format_date(server.ssl_valid_to) }}</td></tr>
        </table>

        <h2>Gebruikersaccounts</h2>

        <table class="table is-striped is-fullwidth">
            <thead>
                <tr><th>Naam</th><th>UvAnetID</th><th>Gebruikersnaam</th><th>Wachtwoord</th><th>Status</th></tr>
            </thead>
            <tbody>
                <tr v-for="user in server.users" :key="user.id">
                    <td>{{ user.user.name }}</td>
                    <td>{{ user.user.uvanetid }}</td>
                    <td>{{ user.username }}</td>
                    <td>{{ user.password }}</td>
                    <td>{{ user.state }}</td>
                </tr>
            </tbody>
        </table>

        </div>
    </div>

</template>


<script lang="ts">
import Vue from 'vue';
import { AxiosResponse } from 'axios';
import moment from 'moment';

export default Vue.extend({

    methods: {
        format_date(date: string) {
            if (date == null) {
                return '';
            }
            return moment(date).format('DD MMM YYYY HH:mm');
        },
    },

    data() {
        return {
            server: null,
        };
    },

    mounted() {
        this.$http.get('/api/servers/' + this.$route.params.id)
        .then((response: AxiosResponse) => {
            this.server = response.data;
        });
    },

});
</script>

<style lang="scss" scoped>
.info-table {
     table, tr, th, td {
        border: 0;
    }

    th, td {
        padding-top: 0;
        padding-bottom: 0;
    }
}
</style>