<template>
    <div class="servers">
        <h2>Servers</h2>

        <table class="table is-striped is-fullwidth servers-table">
            <thead>
                <tr>
                    <th>Server</th>
                    <th>Domeinnaam</th>
                    <th>IP adres</th>
                    <th>Aangemaakt</th>
                    <th>Geheugen</th>
                    <th>Status</th>
                    <th>Groep</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
            <tr v-if="servers == null"><td colspan="6"><div class="element is-loading" style="height: 75px"></div></td></tr>


            <tr v-for="server in servers" :key="server.id">
                    <td><router-link :to="'/admin/servers/' + server.id">{{ server.name }}</router-link></td>
                    <td>{{ server.hostname }}</td>
                    <td>{{ server.ip_address }}</td>
                    <td>
                        <span class="icon">
                            <i class="fa fa-check" v-if="server.created"></i>
                            <i class="fa fa-times" v-else></i>
                        </span>

                        <span class="icon">
                            <i class="fa fa-check" v-if="server.configured"></i>
                            <i class="fa fa-times" v-else></i>
                        </span>

                        <span class="icon">
                            <i class="fa fa-lock" v-if="server.ssl_issuer"></i>
                            <i class="fa fa-times" v-else></i>
                        </span>
                    </td>
                    <td>{{ server.memory }} <template v-if="server.memory">MiB</template></td>
                    <td>{{ server.state }}</td>
                    <td>{{ server.group == null ? " ": server.group.name }}</td>
                    <td><a :href="'http://'+ server.hostname " class="fa fa-globe-africa"></a></td>
                </tr>
            </tbody>
        </table>

    </div>
</template>


<script lang="ts">
import Vue from 'vue';
export default Vue.extend({
    name: 'Servers',

    data() {
        return {
            servers: null,
        };
    },

    mounted() {
        this.$http.get('/api/servers')
        .then((response) => {
            this.servers = response.data;
        });
    },

});
</script>


<style lang="scss" scoped>
    .servers-table {
        th, td {
            padding-top: 5px;
            padding-bottom: 1px;
        }
    }
</style>
