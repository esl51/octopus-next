<template>
  <div class="input-icon">
    <span class="input-icon-addon">
      <o-icon name="search" />
    </span>
    <b-form-input
      :placeholder="$t('global.search')"
      :aria-label="$t('global.search')"
      @input="emit('input', $event)"
    />
  </div>
</template>

<script setup lang="ts">
const emit = defineEmits(['input'])
</script>
