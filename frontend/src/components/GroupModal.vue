<template>
    <Modal v-on:close="$emit('close')">

        <template slot="header">
            Groep bewerken
        </template>

        <template slot="body">
            
            <HorizontalFormElement title="Naam">
                <TextElement v-model="target.name" />
            </HorizontalFormElement>

            <HorizontalFormElement title="Project">
                <SelectElement :options="projects" v-model="target.project_id" />
            </HorizontalFormElement>

            <HorizontalFormElement title="Assistent">
                <SelectElement :options="assistants" v-model="target.assistant_id" />
            </HorizontalFormElement>

            <HorizontalFormElement title="Opmerkingen">
                <TextAreaElement v-model="target.remark" />
            </HorizontalFormElement>

        </template>

        <template slot="footer">
            <button class="button is-success" v-bind:class="{'is-loading': isLoading }" @click="save">Opslaan</button>
        </template>
    </Modal>
</template>


<script lang="ts">
import Vue from 'vue';
import { AxiosResponse, AxiosError } from 'axios';
import Modal from '@/components/Modal.vue';
import TextElement from '@/components/TextElement.vue';
import TextAreaElement from '@/components/TextAreaElement.vue';
import SelectElement from '@/components/SelectElement.vue';
import HorizontalFormElement from '@/components/HorizontalFormElement.vue';



export default Vue.extend({
    components: {
        Modal,
        TextElement,
        TextAreaElement,
        SelectElement,
        HorizontalFormElement,
    },
    props: ['element'],

    data() {
        return {
            target: this.$lodash.clone(this.element),
            projects: [],
            assistants: [],
            groups: [],
            isLoading: false,
            errors: [],
        };
    },

    methods: {
        save() {
            this.isLoading = true;
            this.$http.post('/api/groups', this.target)
                .then((response) => {
                    this.$emit('close');
                }).catch((error: AxiosError) => {
                // if code == 422?
                if (error.response !== undefined) {
                    this.errors = error.response.data.errors;
                }
                this.isLoading = false;
            });
        },
    },

    mounted() {
      delete this.target.students;

      this.$http.get('/api/projects')
      .then((response: AxiosResponse) => {
          this.projects =  response.data.map((e: any) => ({id: e.id, label: e.name}));
          // @ts-ignore
          this.projects.unshift({id: null, label: ''});
      });

      this.$http.get('/api/assistants')
      .then((response: AxiosResponse) => {
          this.assistants = response.data.map((e: any) => ({id: e.id, label: e.name}));
          // @ts-ignore
          this.assistants.unshift({id: null, label: ''});

      });
    },

});
</script>
