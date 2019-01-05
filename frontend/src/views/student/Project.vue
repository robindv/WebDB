<template>
    <div>
        <h1>Project</h1>

        <div v-if="group != null">

            <table class="table project-table">
                <tr><th>Groep</th><td>{{ group.name }}</td></tr>
                <tr v-if="group.assistant != null"><th>Begeleider</th><td>{{ group.assistant.name }}</td></tr>
                <tr v-if="group.students_can_edit_project">
                    <th>Project</th>
                    <td><SelectElement :options="projects" v-model="group.project_id" @input="isSaved=false" /></td>
                </tr>
                <tr v-else><th>Project</th><td>{{ group.project.name }}</td></tr>

                <button v-if="group.students_can_edit_project" class="button" v-bind:class="{'is-loading': isLoading, 'is-success': isSaved }" @click="save">Opslaan</button>

            </table>

            <h2>Groepsleden</h2>
            <div class="content">
                <ul>
                    <li v-for="student in group.students" :key="student.id">{{ student.user.name }}: <a :href="'mailto:' + student.user.email">{{ student.user.email }}</a></li>
                </ul>
            </div>

        </div>

    </div>
</template>



<script lang="ts">
import Vue from 'vue';
import SelectElement from '@/components/SelectElement.vue';
import HorizontalFormElement from '@/components/HorizontalFormElement.vue';
import { AxiosResponse, AxiosError } from 'axios';
export default Vue.extend({

    components: {
        SelectElement,
        HorizontalFormElement,
    },

    data() {
        return {
            group: null,
            projects: [],
            isLoading: false,
            isSaved: false,
        };
    },

    methods: {
        save() {

            this.isLoading = true;
            // @ts-ignore
            this.$http.post('/api/group/project', {project_id: this.group!.project_id})
                .then((response) => {
                    this.getGroup();
                    this.isLoading = false;
                    this.isSaved = true;
                }).catch((error: AxiosError) => {
                this.isLoading = false;
            });
        },

        getGroup() {
            this.$http.get('/api/group')
                .then((response: AxiosResponse) => {
                    this.group = response.data;
                });
        },
    },

    mounted() {
        this.getGroup();
        this.$http.get('/api/projects')
            .then((response: AxiosResponse) => {
                this.projects =  response.data.map((e: any) => ({id: e.id, label: e.name}));
                // @ts-ignore
                this.projects.unshift({id: null, label: ''});
            });
    },
});
</script>

<style lang="scss" scoped>
.project-table {
     table, tr, th, td {
        border: 0;
    }

}
</style>