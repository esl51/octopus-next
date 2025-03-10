<template>
  <v-form-control>
    <template #labelDescription="slotData: any">
      <slot
        name="labelDescription"
        v-bind="slotData"
      />
    </template>
    <vue-select
      :id="id"
      v-model="model[name]"
      :get-option-key="getKey"
      :get-option-label="getLabel"
      :reduce="reduce"
      :options="options"
      :multiple="multiple"
      :disabled="disabled"
      :placeholder="placeholder"
      :close-on-select="!multiple"
      :components="{
        Deselect: { render: () => h(OIcon, { type: IconX }) },
        OpenIndicator: { render: () => h(OIcon, { type: IconChevronDown }) },
      }"
      :class="{
        [`v-select--${size}`]: !!size,
        'is-invalid': state === false,
      }"
      @search="search"
      @input="input"
      @option:selected="select"
    >
      <template #no-options="{ searching }">
        <template v-if="searching">
          {{ $t('global.no_items') }}
        </template>
        <template v-else>
          {{ $t('global.start_typing') }}
        </template>
      </template>
      <template
        v-if="$slots.option"
        #option="option"
      >
        <slot
          name="option"
          v-bind="cast<object>(option)"
        />
      </template>
      <template
        v-if="$slots.selected"
        #selected-option="option"
      >
        <slot
          name="selected"
          v-bind="normalizeOption(option)"
        />
      </template>
    </vue-select>
  </v-form-control>
</template>

<script setup lang="ts">
import OIcon from './OIcon.vue'
import { useFormControl } from '@/composables/useFormControl'
import { cast } from '@/helpers'
import { Item } from '@/types'
import { IconChevronDown, IconX } from '@tabler/icons-vue'
import Form from 'vform'
import { computed, h, inject, ref } from 'vue'
import VueSelect from 'vue-select'

// props
const props = withDefaults(
  defineProps<{
    modelValue?: Form
    name: string
    options: Array<Record<string, unknown>>
    multiple?: boolean
    label?: string
    hint?: string
    description?: string
    disabled?: boolean
    size?: 'sm' | 'lg'
    placeholder?: string
    keyAttribute?: string
    labelAttribute?: string
    initialOptions?: Array<Item> | Item | null
  }>(),
  {
    modelValue: undefined,
    multiple: false,
    label: undefined,
    hint: undefined,
    description: undefined,
    disabled: false,
    size: undefined,
    placeholder: undefined,
    keyAttribute: 'id',
    labelAttribute: 'name',
    initialOptions: null,
  },
)

// control
const { id, state } = useFormControl(props)

// form
const form: Form | undefined = inject('form')

// value
const emit = defineEmits(['update:modelValue', 'search', 'input'])
const model = computed({
  get: () => props.modelValue || form?.value,
  set: (value) => {
    emit('update:modelValue', value)
  },
})

// reduce
const reduce = (option: Item | number) => {
  if (typeof option === 'number') {
    return option
  } else {
    return option[props.keyAttribute]
  }
}

// cached options
const cachedOptions = ref<Array<Item>>([])

// select options
const select = (options: Array<Item | number> | Item) => {
  let selected: Array<Item> = []
  if (!Array.isArray(options)) {
    selected = [options]
  } else {
    selected = options.map((o) =>
      typeof o === 'number'
        ? labelOptions.value.find((lo) => lo[props.keyAttribute] === o) ||
          ({ [props.keyAttribute]: o } as Item)
        : o,
    )
  }
  cachedOptions.value = [...cachedOptions.value, ...selected]
}

// label options
const labelOptions = computed(() => {
  let labelOptions = cachedOptions.value
  if (props.initialOptions) {
    if (Array.isArray(props.initialOptions)) {
      labelOptions = [...labelOptions, ...props.initialOptions]
    } else {
      labelOptions.push(props.initialOptions)
    }
  }
  return labelOptions
})

// get key
const getKey = (option: Item | number) => {
  if (typeof option === 'object') {
    return option[props.keyAttribute]
  }
  return option
}

// get label
const getLabel = (option: Item | number) => {
  if (typeof option === 'object') {
    return option[props.labelAttribute]
  }
  const labelOption = labelOptions.value.find(
    (lo: Item) => lo[props.keyAttribute] == option,
  )
  if (labelOption) {
    return labelOption[props.labelAttribute]
  }
  return option
}

// normalize option
const normalizeOption = (option: Item | number | { label: number }) => {
  if (typeof option === 'number') {
    return labelOptions.value.find((lo) => lo[props.keyAttribute] === option)
  } else if (option.label) {
    return labelOptions.value.find(
      (lo) => lo[props.keyAttribute] === option.label,
    )
  } else {
    return option
  }
}

// search
const search = (query: string, busy: (b: boolean) => void) => {
  emit('search', query, busy)
}

// input
const input = (value: string) => {
  emit('input', value)
}
</script>
