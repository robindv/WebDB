<template>
    <div class="Groups">
        <h1>Groepen</h1>

        <component :is="modalType" v-bind:element="modalElement" v-on:close="close()"></component>

        <a v-on:click="csv" class="button is-pulled-right" style="text-decoration: none;">CSV-export</a>

        <table class="table is-fullwidth groups-table">
            <thead>
                <tr align="left">
                    <th>Groep</th>
                    <th>Assistent</th>
                    <th>Project</th>
                    <th>Student</th>
                    <th>Opmerkingen</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="groups.length == 0"><td colspan="6"><div class="element is-loading" style="height: 75px"></div></td></tr>

                <template v-for="(group, index) in groups">
                    <tr :id="group.id" v-bind:class="{'striped' : index % 2 == 0, 'target': $route.hash == '#' + group.id }">
                        <td><a :href="'mailto:' + concat_emails(group)">{{ group.name }}</a></td>
                        <td>{{ group.assistant == null ? "Onbekend" : group.assistant.name }}</td>
                        <td>{{ group.project == null ? "Onbekend" : group.project.name }}</td>
                        <td>&nbsp;</td>
                        <td>{{ group.remark }}</td>
                        <td><a class="fas fa-pencil-alt" @click="clickButton(group)"></a></td>
                    </tr>

                    <tr v-for="student in group.students" v-bind:class="{'striped' : index % 2 == 0 }">
                        <td colspan="3"></td>
                        <td><router-link :to="'/staff/students#' + student.id">{{ student.user.name }}</router-link></td>
                        <td>{{ student.remark }}</td>
                        <td>&nbsp;</td>
                    </tr>

                </template>
            </tbody>
        </table>
    </div>


</template>

<script lang="ts">
import Vue from 'vue';
import { AxiosResponse } from 'axios';
import GroupModal from '@/components/GroupModal.vue';
import downloadjs from 'downloadjs';
import Papa from 'papaparse';

export default Vue.extend({
    components: {
        GroupModal,
    },

    data() {
        return {
            groups: [ ],
            projects: [ ],
            modalType: '',
            modalElement: { },
        };
    },

    mounted() {

        this.$http.get('/api/projects')
          .then((response: AxiosResponse) => {
              this.projects = response.data;
          });

        this.getGroups().then(() => {
            if (this.$route.hash) {
                Vue.nextTick(() => {
                    document.getElementById(this.$route.hash.substr(1))!.scrollIntoView();
                });
            }
        });
    },

    methods: {
        clickButton(item: object) {
            this.modalElement = item;
            this.modalType = 'GroupModal';
        },

        close() {
            this.getGroups();
            this.modalType = '';
            this.modalElement = { };
        },

        getGroups() {
            return this.$http.get('/api/groups')
                .then((response: AxiosResponse) => {
                    this.groups = response.data;
                });
        },

        concat_emails(group: any) {
            return group.students.map((s: any) => s.user.email).join(',');
        },


        csv(event: Event) {
            event.preventDefault();

            const data = [['Groep', 'Assistent', 'Project', 'Student', 'Opmerkingen']];

            this.groups.forEach((group: any) => {
                data.push([group.name,
                           group.assistant == null ? 'Onbekend' : group.assistant.name,
                           group.project == null ? 'Onbekend' : group.project.name,
                           '',
                           group.remark]);

                group.students.forEach((student: any) => {
                   data.push(['', '', '', student.user.name, student.remark]);
                });
            });

            downloadjs(new Blob([Papa.unparse(data)]), 'groepen.csv', 'text/csv');
        },
    },

});
</script>


<style lang="scss" scoped>
    .groups-table {
        th, td {
            padding-top: 5px;
            padding-bottom: 1px;
        }
    }

    .striped {
        background: #f1f1f1;
    }

    tr.target td, tr.target td a {
        font-weight: bold;
        color: red !important;
    }

</style>
