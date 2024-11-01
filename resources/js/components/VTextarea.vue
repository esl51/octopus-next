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
    <textarea
      v-for="locale in translatable ? availableLocales : [undefined]"
      v-show="!translatable || locale === activeLocale"
      :id="locale ? id + '-' + locale : id"
      :key="locale ? name + ':' + locale : name"
      v-model="model[locale ? name + ':' + locale : name]"
      :name="locale ? name + ':' + locale : name"
      :readonly="readonly"
      :disabled="disabled"
      :autofocus="autofocus"
      :autocomplete="autocomplete"
      :placeholder="placeholder"
      :class="{
        'form-control': true,
        ['form-control-' + size]: !!size,
        'is-invalid': locale
          ? locale === activeLocale
            ? state === false
            : undefined
          : state === false,
        autofocus: !!autofocus,
      }"
    />
  </v-form-control>
</template>

<script setup lang="ts">
import { useFormControl } from '@/composables/useFormControl'
import Form from 'vform'
import { computed, inject } from 'vue'
import VLocaleSwitch from './VLocaleSwitch.vue'

// props
const props = withDefaults(
  defineProps<{
    modelValue?: Form
    name: string
    label?: string
    hint?: string
    description?: string
    readonly?: boolean
    disabled?: boolean
    autofocus?: boolean
    autocomplete?: string
    size?: 'sm' | 'lg'
    placeholder?: string
    translatable?: boolean
  }>(),
  {
    modelValue: undefined,
    label: undefined,
    hint: undefined,
    description: undefined,
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
