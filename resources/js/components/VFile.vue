<template>
  <v-form-control>
    <template #labelDescription="slotData: any">
      <slot
        name="labelDescription"
        v-bind="slotData"
      />
    </template>
    <slot />
    <b-input-group class="input-group-flat">
      <input
        :id="id"
        ref="input"
        type="file"
        :name="name"
        class="form-control"
        :class="{
          autofocus: !!autofocus,
          'is-invalid': state === false,
        }"
        :disabled="disabled"
        :autofocus="autofocus"
        :autocomplete="autocomplete"
        :placeholder="placeholder"
        :size="size"
        :multiple="multiple"
        @change="change"
      />
      <template #append>
        <b-input-group-text>
          <file-upload-icon class="icon" />
        </b-input-group-text>
      </template>
    </b-input-group>
  </v-form-control>
</template>

<script setup lang="ts">
import { useFormControl } from '@/composables/useFormControl'
import { Size } from 'bootstrap-vue-3'
import Form from 'vform'
import { computed, inject, ref, watch } from 'vue'

// props
const props = withDefaults(
  defineProps<{
    modelValue?: Form
    name: string
    label?: string
    hint?: string
    disabled?: boolean
    autofocus?: boolean
    autocomplete?: string
    size?: Size
    placeholder?: string
    multiple?: boolean
  }>(),
  {
    modelValue: undefined,
    label: undefined,
    hint: undefined,
    disabled: false,
    autofocus: false,
    autocomplete: undefined,
    size: undefined,
    placeholder: undefined,
    multiple: false,
  }
)

// control
const { id, state } = useFormControl(props)

// form
const form: Form | undefined = inject('form')

// input
const input = ref()

// value
const emit = defineEmits(['update:modelValue'])
const model = computed({
  get: () => props.modelValue || form?.value,
  set: (value) => {
    emit('update:modelValue', value)
  },
})

// change
const change = (event: Event) => {
  model.value[props.name] = null
  const target = event.target as HTMLInputElement
  if (props.multiple && target.files?.length) {
    model.value[props.name] = target.files
  } else if (target.files?.length) {
    model.value[props.name] = target.files[0]
  }
}

// watch
watch(
  () => model.value[props.name],
  (value) => {
    if (value === undefined || value === null) {
      input.value.value = ''
    }
  }
)
</script>