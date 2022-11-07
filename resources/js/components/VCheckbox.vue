<template>
  <b-form-checkbox
    :id="id"
    v-model="model"
    :state="state"
    :invalid-feedback="errors"
    :size="size"
  >
    {{ label }}
  </b-form-checkbox>
</template>

<script setup lang="ts">
import { useFormControl } from '@/composables/useFormControl'
import { Size } from 'bootstrap-vue-3'
import { computed } from 'vue'

// props
const props = withDefaults(
  defineProps<{
    modelValue: boolean | undefined
    name: string
    label?: string
    disabled?: boolean
    autofocus?: boolean
    size?: Size
  }>(),
  {
    label: undefined,
    disabled: false,
    autofocus: false,
    size: undefined,
  }
)

// control
const { id, state, errors } = useFormControl(props)

// value
const emit = defineEmits(['update:modelValue'])
const model = computed({
  get() {
    return props.modelValue
  },
  set(value) {
    return emit('update:modelValue', value)
  },
})
</script>
