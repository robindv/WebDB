<template>
    <Modal v-on:close="$emit('close')">

        <template slot="header">
            Student bewerken
        </template>

        <template slot="body">
            
            <HorizontalFormElement title="Naam">
                <div class="input is-static is-small">{{ target.user.name }}</div>
            </HorizontalFormElement>

            <HorizontalFormElement title="E-mailadres" :errors="errors['user.email']">
                <TextElement v-model="target.user.email" :errors="errors['user.email']" v-if="user.is_teacher" />
                <div class="input is-static is-small" v-else>{{ target.user.email }}</div>
            </HorizontalFormElement>

            <HorizontalFormElement title="Opleiding" :errors="errors['programme']">
                <TextElement v-model="target.programme" :errors="errors['programme']"  v-if="user.is_teacher" />
                <div class="input is-static is-small" v-else>{{ target.programme }}</div>
            </HorizontalFormElement>

            <HorizontalFormElement title="Actief">
                <SelectElement :options="active" v-model="target.active"  v-if="user.is_teacher"  />
                <div class="input is-static is-small" v-else>{{ target.active ? "Ja" : "Nee" }}</div>
            </HorizontalFormElement>

            <HorizontalFormElement title="Groep">
                <SelectElement :options="groups" v-model="target.group_id"  v-if="user.is_teacher" />
                <div class="input is-static is-small" v-else>{{ target.group.name }}</div>
            </HorizontalFormElement>

            <HorizontalFormElement title="Opmerkingen" :errors="errors['remark']">
                <TextAreaElement v-model="target.remark" :errors="errors['remark']" />
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
import { mapGetters } from 'vuex';
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

    computed: {
        ...mapGetters(['user']),
    },


    data() {
        return {
           target: this.$lodash.clone(this.element),
           active: [{id: 0, label: 'Nee'}, { id: 1, label: 'Ja'}],
           groups: [],
           isLoading: false,
           errors: [],
        };
    },

    methods: {
        save() {
            this.isLoading = true;
            this.$http.post('/api/students', this.target)
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
      this.$http.get('/api/groups')
      .then((response: AxiosResponse) => {
          this.groups = response.data.map((e: any) => ({id: e.id, label: e.name}));
          // @ts-ignore
          this.groups.unshift({id: null, label: ''});

      });
    },

});
</script>
