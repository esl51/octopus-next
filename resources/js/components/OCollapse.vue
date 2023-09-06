<template>
  <div
    ref="collapse"
    class="collapse"
  >
    <slot />
  </div>
</template>

<script setup lang="ts">
import Collapse from 'bootstrap/js/dist/collapse'
import { ref } from 'vue'

const collapse = ref(null)
if (collapse.value) {
  new Collapse(collapse.value)
}
</script>
