<template>
    <input class="input is-small" v-model="content" @input="handleInput" v-bind:class="{'is-danger': hasError }"></input>
</template>

<script lang="ts">
import Vue from 'vue';

export default Vue.extend({
    props: ['value', 'errors'],

    data() {
        return {
            content: this.value,
        };
    },

    computed: {
        hasError(): boolean {
            return this.errors !== undefined;
        },
    },

    methods: {
        handleInput(e: string) {
            this.$emit('input', this.content);
        },
    },

});
</script>