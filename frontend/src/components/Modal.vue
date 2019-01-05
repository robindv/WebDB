<template>
<div class="modal is-active">
  <div class="modal-background" @click="close"></div>
  <div class="modal-card">
    <header class="modal-card-head">
      <p class="modal-card-title"><slot name="header"></slot></p>
      <button class="delete" aria-label="close" @click="close"></button>
    </header>
    <section class="modal-card-body">
        <slot name="body"></slot>
    </section>
    <footer class="modal-card-foot">
        <slot name="footer"></slot>
        <button class="button" @click="close">Annuleren</button>
    </footer>
  </div>
</div>

</template>

<script lang="ts">
import Vue from 'vue';

export default Vue.extend({

    methods: {
        close() {
            this.$emit('close');
        },
    },

});
</script>

<style scoped>

.modal {
    justify-content: flex-start;
    padding-top: 150px;
}

.modal-card-title {
    font-size: 20px;
}

.modal-card-head, .modal-card-foot {
    padding: 15px;
}

.modal-card-foot {
    justify-content: flex-end;
}
</style>