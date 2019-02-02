<template>
    <div class="students">
        <h2>Studenten</h2>

        <a v-on:click="csv" class="button is-pulled-right" style="text-decoration: none;">CSV-export</a>

        <component :is="modalType" v-bind:element="modalElement" v-on:close="close()"></component>

        <table class="table is-striped is-fullwidth">
            <thead>
                <tr align="left">
                    <th>Actief</th>
                    <th>UvAnetID</th>
                    <th>Naam</th>
                    <th>Opleiding</th>
                    <th>Opmerking</th>
                    <th>Groep</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
            <tr v-if="students.length == 0"><td colspan="6"><div class="element is-loading" style="height: 75px"></div></td></tr>


            <tr v-for="student in students" :key="student.id" :id="student.id" v-bind:class="{'target': $route.hash == '#' + student.id }">
                    <td>
                        <span class="icon">
                            <i class="fa fa-check" v-if="student.active"></i>
                            <i class="fa fa-times" v-else></i>
                        </span>
                    </td>
                    <td>{{ student.user.uvanetid }}</td>
                    <td><a :href="'mailto:' + student.user.email">{{ student.user.name }}</a></td>
                    <td>{{ student.programme }}</td>
                    <td v-html="student.remark.replace(/\n/g, '<br />')"></td>
                    <td v-if="student.group == null">&nbsp;</td>
                    <td v-else><router-link :to="'/staff/groups#' + student.group_id">{{ student.group.name }}</router-link></td>
                    <td><a class="fas fa-pencil-alt" @click="clickButton(student)"></a></td>
                </tr>
            </tbody>
        </table>

    </div>
</template>


<script lang="ts">
import Vue from 'vue';
import { AxiosResponse } from 'axios';
import StudentModal from '@/components/StudentModal.vue';
import downloadjs from 'downloadjs';
import Papa from 'papaparse';

export default Vue.extend({

    components: {
        StudentModal,
    },

    data() {
        return {
            students: [ ],
            modalType: '',
            modalElement: { },
        };
    },

    mounted() {
        this.getStudents().then(() => {
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
            this.modalType = 'StudentModal';
        },

        close() {
            this.getStudents();
            this.modalType = '';
            this.modalElement = { };
        },

        getStudents() {
            return this.$http.get('/api/students')
            .then((response: AxiosResponse) => {
                this.students = response.data;
            });
        },

        csv(event: Event) {
            event.preventDefault();

            const data = [['Actief', 'UvAnetID', 'Naam', 'E-mailadres', 'Opleiding',
                           'Opmerkingen', 'Groep', 'Opmerkingen groep']];

            this.students.forEach((student: any) => {
                    data.push([
                        student.active ? 'Ja' : 'Nee',
                        student.user.uvanetid,
                        student.user.name,
                        student.user.email,
                        student.programme,
                        student.remark,
                        student.group == null ? '' : student.group.name,
                        student.group == null ? '' : student.group.remark]);
            });

            downloadjs(new Blob([Papa.unparse(data)]), 'studenten.csv', 'text/csv');
        },

    },
});
</script>


<style scoped>
    tr.target td, tr.target td a {
        font-weight: bold;
        color: red !important;
    }


    th, td {
        padding-top: 5px;
        padding-bottom: 1px;
    }

</style>