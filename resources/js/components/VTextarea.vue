<template>
  <v-form-control>
    <template #labelDescription="slotData: any">
      <slot
        name="labelDescription"
        v-bind="slotData"
      />
      <v-locale-switch
        v-if="translatable"
        v-model="activeLocale"
        :states="localeStates"
      />
    </template>
    <b-form-textarea
      v-for="locale in translatable ? availableLocales : [undefined]"
      v-show="!translatable || locale === activeLocale"
      :id="locale ? id + '-' + locale : id"
      :key="locale ? name + ':' + locale : name"
      v-model="model[locale ? name + ':' + locale : name]"
      :name="locale ? name + ':' + locale : name"
      :state="locale ? (locale === activeLocale ? state : undefined) : state"
      :readonly="readonly"
      :disabled="disabled"
      :autofocus="autofocus"
      :autocomplete="autocomplete"
      :placeholder="placeholder"
      :size="size"
      :class="{
        autofocus: !!autofocus,
      }"
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
    label?: string
    hint?: string
    readonly?: boolean
    disabled?: boolean
    autofocus?: boolean
    autocomplete?: string
    size?: Size
    placeholder?: string
    translatable?: boolean
  }>(),
  {
    modelValue: undefined,
    label: undefined,
    hint: undefined,
    readonly: false,
    disabled: false,
    autofocus: false,
    autocomplete: undefined,
    size: undefined,
    placeholder: undefined,
    translatable: false,
  },
)

// control
const { id, state, activeLocale, availableLocales, localeStates } =
  useFormControl(props)

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
