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
import { Size } from 'bootstrap-vue-next'
import Form from 'vform'
import { computed, inject } from 'vue'

// props
const props = withDefaults(
  defineProps<{
    modelValue?: boolean | undefined
    name: string
    label?: string
    disabled?: boolean
    autofocus?: boolean
    size?: Size
  }>(),
  {
    modelValue: undefined,
    label: undefined,
    disabled: false,
    autofocus: false,
    size: undefined,
  },
)

// control
const { id, state, errors } = useFormControl(props)

// form
const form: Form | undefined = inject('form')

// value
const emit = defineEmits(['update:modelValue'])
const model = computed({
  get: () => props.modelValue || form?.value,
  set: (value) => {
    emit('update:modelValue', value)
  },
})
</script>
