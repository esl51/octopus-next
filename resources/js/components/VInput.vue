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
    <div
      v-for="locale in translatable ? availableLocales : [undefined]"
      v-show="!translatable || locale === activeLocale"
      :key="locale ? name + ':' + locale : name"
      class="input-icon"
    >
      <span
        v-if="icon"
        class="input-icon-addon"
      >
        <o-icon :type="icon" />
      </span>
      <input
        :id="locale ? id + '-' + locale : id"
        v-model="model[locale ? name + ':' + locale : name]"
        :name="locale ? name + ':' + locale : name"
        :type="inputType"
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
      <span
        v-if="type === 'password'"
        class="input-icon-addon pe-auto"
      >
        <a
          v-tooltip
          :title="$t('global.show_password')"
          class="link-secondary d-flex"
          :aria-label="$t('global.show_password')"
          href="#"
          @click.prevent="togglePasswordVisible"
        >
          <o-icon :type="passwordVisible ? IconEyeOff : IconEye" />
        </a>
      </span>
    </div>
  </v-form-control>
</template>

<script setup lang="ts">
import VLocaleSwitch from './VLocaleSwitch.vue'
import { useFormControl } from '@/composables/useFormControl'
import { SVGProps } from '@/types'
import { IconEye, IconEyeOff } from '@tabler/icons-vue'
import Form from 'vform'
import { ref, computed, inject, FunctionalComponent } from 'vue'

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
    size?: 'sm' | 'lg'
    type?: 'text' | 'password' | 'email' | 'tel'
    placeholder?: string
    icon?: FunctionalComponent<SVGProps>
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
    icon: undefined,
    type: 'text',
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

// password input type
const passwordVisible = ref(false)
const inputType = computed(() => {
  if (props.type === 'password') {
    return passwordVisible.value ? 'text' : 'password'
  }
  return props.type
})
const togglePasswordVisible = () => {
  passwordVisible.value = !passwordVisible.value
}
</script>
