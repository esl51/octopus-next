<template>
  <div class="d-flex">
    <o-button
      v-for="locale in availableLocales"
      :key="locale"
      size="sm"
      :variant="
        locale === model
          ? states && states[locale] === false
            ? 'danger'
            : 'primary'
          : 'link'
      "
      :class="{
        'text-danger': locale !== model && states && states[locale] === false,
      }"
      @click.prevent="model = locale"
    >
      {{ locale }}
    </o-button>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

const { availableLocales } = useI18n()

// props
const props = defineProps<{
  modelValue: string
  states?: Record<string, boolean | undefined>
}>()

// value
const emit = defineEmits(['update:modelValue'])
const model = computed({
  get: () => props.modelValue,
  set: (value) => {
    emit('update:modelValue', value)
  },
})
</script>
