<template>
  <div class="input-icon">
    <span class="input-icon-addon">
      <o-icon name="search" />
    </span>
    <input
      v-model="model"
      :placeholder="$t('global.search')"
      :aria-label="$t('global.search')"
      class="form-control"
      @input="emit('input', $event)"
    />
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

// props
const props = withDefaults(
  defineProps<{
    modelValue?: string
  }>(),
  {
    modelValue: undefined,
  },
)
// value
const emit = defineEmits(['input', 'update:modelValue'])
const model = computed({
  get: () => props.modelValue,
  set: (value) => {
    emit('update:modelValue', value)
  },
})
</script>
