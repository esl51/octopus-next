<template>
  <v-form-control>
    <template #labelDescription="slotData: any">
      <slot
        name="labelDescription"
        v-bind="slotData"
      />
    </template>
    <div class="btn-group-md">
      <div
        v-for="(item, index) in options"
        :key="item[keyAttribute] as string"
        class="form-check"
      >
        <input
          :id="id + '_' + index"
          v-model="model[name]"
          class="form-check-input"
          type="checkbox"
          :name="name"
          :value="item[keyAttribute]"
        />
        <label
          :for="id + '_' + index"
          class="form-check-label"
        >
          {{ item[labelAttribute] }}
        </label>
      </div>
    </div>
  </v-form-control>
</template>

<script setup lang="ts">
import { useFormControl } from '@/composables/useFormControl'
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
    keyAttribute?: string
    labelAttribute?: string
  }>(),
  {
    modelValue: undefined,
    label: undefined,
    hint: undefined,
    disabled: false,
    autofocus: false,
    keyAttribute: 'id',
    labelAttribute: 'name',
  },
)

// control
const { id } = useFormControl(props)

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
