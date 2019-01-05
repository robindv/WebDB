<template>
    <div>
        <h2>Inloggen</h2>

        <p v-if="error != null">Helaas is het inloggen mislukt, dit komt waarschijnlijk omdat je UvAnetID niet is opgenomen in onze database.
        <br />
        Indien je studentassistent bent kan dit komen omdat je nog met het verkeerde UvAnetID bent ingelogd, klik in dat geval TODO om uit te loggen.
        <br />
        Is dit niet het geval en moet je wel kunnen inloggen? Neem dan contact op met Robin de Vries (Robin punt deVries apenstaartje uva punt nl).
        </p>
    </div>
</template>

<script lang="ts">
import Vue from 'vue';
import { AxiosResponse } from 'axios';
export default Vue.extend({

    data() {
        return {
            error: null,
        };
    },

    mounted() {
        const ticket = this.$route.query.ticket;

        this.$http.get('api/auth?ticket=' + ticket)
         .then((response) => {

            if (response.data.error) {
                this.$store.commit('token', null);
                this.$store.commit('user', {});
                this.error = response.data.error;
                return;
            }

            this.$store.commit('token', response.data);
            this.$http.defaults.headers.common.Authorization = 'Bearer ' + response.data.access_token;

            const userpromise = this.$http.get('/api/user');
            const coursepromise = this.$http.get('/api/course');

            Promise.all([userpromise, coursepromise]).then((results) => {
                const userresponse = results[0];
                const courseresponse = results[1];

                this.$store.commit('user', userresponse.data);
                this.$store.commit('course', courseresponse.data);

                this.$router.push('/');
            });
        });

    },
});
</script>
