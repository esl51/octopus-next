<template>
  <div>
    <div class="form-check">
      <input
        :id="id"
        v-model="model[name]"
        class="form-check-input"
        type="checkbox"
        :name="name"
      />
      <label
        v-if="label"
        class="form-check-label"
        :for="id"
      >
        {{ label }}
      </label>
    </div>
    <div
      v-if="state === false && errors?.length"
      class="invalid-feedback"
      v-html="errors?.join('<br />')"
    />
  </div>
</template>

<script setup lang="ts">
import { useFormControl } from '@/composables/useFormControl'
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
  }>(),
  {
    modelValue: undefined,
    label: undefined,
    disabled: false,
    autofocus: false,
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
