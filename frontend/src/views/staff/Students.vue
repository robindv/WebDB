<template>
    <div class="students">
        <h2>Studenten</h2>

        <table class="table is-striped is-fullwidth">
            <thead>
                <tr align="left">
                    <th>Actief</th>
                    <th>UvAnetID</th>
                    <th>Naam</th>
                    <th>Opleiding</th>
                    <th>Tutor</th>
                    <th>Opmerking</th>
                    <th>Groep</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="student in students" :key="student.id">
                    <td>{{ student.active }}</td>
                    <td>{{ student.user.uvanetid }}</td>
                    <td>{{ student.user.email }}{{ student.user.name }}</td>
                    <td>{{ student.programme }}</td>
                    <td>??</td>
                    <td>{{ student.remark }}</td>
                    <td>{{ student.group.name || "?" }}</td>
                    <td>?</td>
                </tr>
            </tbody>
        </table>

    </div>
</template>


<script lang="ts">
import Vue from 'vue';
import { AxiosResponse } from 'axios';
export default Vue.extend({
    data() {
        return {
            students: null,
        };
    },

    mounted() {
      this.$http.get('/api/students')
      .then((response: AxiosResponse) => {
          this.students = response.data;
      });
    },

});
</script>
