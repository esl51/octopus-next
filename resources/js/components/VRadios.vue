<template>
  <v-form-control>
    <template #labelDescription="slotData: any">
      <slot
        name="labelDescription"
        v-bind="slotData"
      />
    </template>
    <b-form-radio-group
      :id="id"
      v-model="model[name]"
      :options="options"
      :value-field="keyAttribute"
      :text-field="labelAttribute"
      :state="state"
      :size="size"
      :disabled="disabled"
      :autofocus="autofocus"
      stacked
    />
  </v-form-control>
</template>

<script setup lang="ts">
import { useFormControl } from '@/composables/useFormControl'
import { Size } from 'bootstrap-vue-next'
import Form from 'vform'
import { computed, inject } from 'vue'

// props
const props = withDefaults(
  defineProps<{
    modelValue?: Form
    name: string
    options: Array<Record<string, unknown>>
    label?: string
    hint?: string
    disabled?: boolean
    autofocus?: boolean
    size?: Size
    keyAttribute?: string
    labelAttribute?: string
  }>(),
  {
    modelValue: undefined,
    label: undefined,
    hint: undefined,
    disabled: false,
    autofocus: false,
    size: undefined,
    keyAttribute: 'id',
    labelAttribute: 'name',
  },
)

// control
const { id, state } = useFormControl(props)

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
