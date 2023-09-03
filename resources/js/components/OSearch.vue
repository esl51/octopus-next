<template>
  <div class="input-icon">
    <span class="input-icon-addon">
      <o-icon name="search" />
    </span>
    <input
      :placeholder="$t('global.search')"
      :aria-label="$t('global.search')"
      class="form-control"
      @input="emit('input', $event)"
    />
  </div>
</template>

<script setup lang="ts">
const emit = defineEmits(['input'])
</script>
