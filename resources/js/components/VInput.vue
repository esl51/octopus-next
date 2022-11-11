<template>
  <v-form-control>
    <template #labelDescription="slotData: any">
      <slot
        name="labelDescription"
        v-bind="slotData"
      />
    </template>
    <b-input-group class="input-group-flat">
      <template
        v-if="icon"
        #prepend
      >
        <b-input-group-text :class="state === false ? 'is-invalid' : null">
          <component :is="iconComponent" />
        </b-input-group-text>
      </template>
      <b-form-input
        :id="id"
        v-model="model"
        :name="name"
        :state="state"
        :type="inputType"
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
      <template
        v-if="type === 'password'"
        #append
      >
        <b-input-group-text :class="state === false ? 'is-invalid' : null">
          <b-link
            class="link-secondary"
            data-bs-toggle="tooltip"
            :aria-label="$t('global.show_password')"
            :data-bs-original-title="$t('global.show_password')"
            @click.prevent="togglePasswordVisible"
          >
            <eye-off-icon
              v-if="passwordVisible"
              class="icon"
            />
            <eye-icon
              v-else
              class="icon"
            />
          </b-link>
        </b-input-group-text>
      </template>
    </b-input-group>
  </v-form-control>
</template>

<script setup lang="ts">
import { useFormControl } from '@/composables/useFormControl'
import { InputType, Size } from 'bootstrap-vue-3'
import { ref, computed } from 'vue'

// props
const props = withDefaults(
  defineProps<{
    modelValue: string | number | undefined
    name: string
    label?: string
    hint?: string
    readonly?: boolean
    disabled?: boolean
    autofocus?: boolean
    autocomplete?: string
    size?: Size
    type?: InputType
    placeholder?: string
    icon?: string
  }>(),
  {
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
  }
)

// control
const { id, state } = useFormControl(props)

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

// icon
const iconComponent = computed(() => (props.icon ? props.icon + '-icon' : null))

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
