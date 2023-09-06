<template>
  <v-form-control>
    <template #labelDescription="slotData: any">
      <slot
        name="labelDescription"
        v-bind="slotData"
      />
    </template>
    <date-picker
      :id="id"
      v-model:value="model[name]"
      format="DD.MM.YYYY"
      title-format="DD.MM.YYYY"
      prefix-class="xmx"
      :input-class="
        'form-control' +
        (size ? ' form-control-' + size : '') +
        (state === false ? ' is-invalid' : '')
      "
      :disabled="disabled"
      :autofocus="autofocus"
      :placeholder="placeholder ? placeholder : $t('global.choose')"
      :append-to-body="false"
      :disabled-date="dateDisabled"
      :default-panel="defaultPanel"
      :default-value="defaultValue"
      :get-classes="dateClass"
    >
      <template #icon-calendar>
        <o-icon :type="IconCalendar" />
      </template>
      <template #icon-clear>
        <o-icon :type="IconX" />
      </template>
    </date-picker>
  </v-form-control>
</template>

<script setup lang="ts">
import { useFormControl } from '@/composables/useFormControl'
import { IconX, IconCalendar } from '@tabler/icons-vue'
import Form from 'vform'
import { computed, inject } from 'vue'
import DatePicker from 'vue-datepicker-next'
import 'vue-datepicker-next/locale/ru.es'

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
    size?: 'sm' | 'lg'
    placeholder?: string
    icon?: string
    min?: Date
    max?: Date
    disabledDates?: Array<Date>
    accentDates?: Array<Date>
    defaultPanel?: string
    defaultValue?: Date
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
    icon: undefined,
    min: undefined,
    max: undefined,
    disabledDates: undefined,
    accentDates: undefined,
    defaultPanel: undefined,
    defaultValue: undefined,
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

// disabled dates
const dateDisabled = (date: Date) => {
  if (props.min) {
    let minDate = props.min
    if (typeof minDate === 'string') {
      minDate = new Date(minDate)
    }
    if (date < minDate) {
      return true
    }
  }
  if (props.max) {
    let maxDate = props.max
    if (typeof maxDate === 'string') {
      maxDate = new Date(maxDate)
    }
    if (date > maxDate) {
      return true
    }
  }
  if (!props.disabledDates) {
    return false
  }
  let result = false
  props.disabledDates.forEach((disabledDate) => {
    if (disabledDate.getTime() === date.getTime()) {
      result = true
    }
  })
  return result
}

// dates class
const dateClass = (date: Date) => {
  if (!props.accentDates) {
    return null
  }
  let result = null
  props.accentDates.forEach((accentDate) => {
    if (accentDate.getTime() === date.getTime()) {
      result = 'accent'
    }
  })
  return result
}
</script>
