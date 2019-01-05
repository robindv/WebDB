<template>
    <div class="select is-small">
        <select v-model="content" @input="handleInput">
            <option v-for="option in options" :value="option.id" :selected="content == option.id">{{ option.label }}</option>
        </select>
    </div>
</template>

<script lang="ts">
import Vue from 'vue';

export default Vue.extend({
    props: ['value', 'options'],

    data() {
        return {
            content: this.value,
        };
    },

    methods: {
        handleInput(e: string) {
            const context = this;
            Vue.nextTick(function nextTick() {
                context.$emit('input', context.content);
            });
        },
    },

});
</script>