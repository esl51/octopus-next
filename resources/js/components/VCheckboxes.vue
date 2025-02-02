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
        :key="cast<string>(item[keyAttribute])"
        class="form-check"
        :class="{
          'form-check-inline': !!inline,
        }"
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
import { cast } from '@/helpers'
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
    description?: string
    disabled?: boolean
    autofocus?: boolean
    keyAttribute?: string
    labelAttribute?: string
    inline?: boolean
  }>(),
  {
    modelValue: undefined,
    label: undefined,
    hint: undefined,
    description: undefined,
    disabled: false,
    autofocus: false,
    keyAttribute: 'id',
    labelAttribute: 'name',
    inline: false,
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
