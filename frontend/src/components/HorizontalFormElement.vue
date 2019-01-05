<template>
    <div class="field is-horizontal">
        <div class="field-label is-small">
            <label class="label">{{ title }}</label>
        </div>
        <div class="field-body">
            <div class="field">
                <div class="control">
                    <slot></slot>
                </div>
                <p class="help is-danger" v-if="hasError" >{{ error }}</p>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import Vue from 'vue';

export default Vue.extend({
    props: ['title', 'errors'],

    computed: {
        error(): any {
            return this.errors ? this.errors[0] : '';
        },

        hasError(): boolean {
            return this.errors !== undefined;
        },
    },
});


</script>