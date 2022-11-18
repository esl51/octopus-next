<template>
  <div class="jodit-editor">
    <textarea
      :id="id"
      ref="textarea"
      v-model="model"
      :name="name"
      @change="change"
    />
  </div>
</template>

<script setup lang="ts">
import { Jodit } from 'jodit'
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'

const props = withDefaults(
  defineProps<{
    modelValue?: string
    config?: Record<string, unknown>
    id?: string
    name?: string
  }>(),
  {
    modelValue: '',
    config: () => ({}),
    id: undefined,
    name: undefined,
  }
)

const textarea = ref()
const jodit = ref()

const editorConfig = computed(() => ({ ...props.config }))

onMounted(() => {
  jodit.value = Jodit.make(textarea.value, editorConfig.value)
})

watch(
  () => editorConfig.value,
  (config: Record<string, unknown>) => {
    if (config.disabled !== undefined) {
      jodit.value.setDisabled(config.disabled)
    }
    if (config.readonly !== undefined) {
      jodit.value.setReadOnly(config.readonly)
    }
  },
  { deep: true }
)

onBeforeUnmount(() => {
  jodit.value.destruct()
})

// value
const emit = defineEmits(['update:modelValue'])
const model = computed({
  get: () => props.modelValue,
  set: (value) => {
    emit('update:modelValue', value)
  },
})

const change = (event: Event) => {
  const target = event.target as HTMLTextAreaElement
  emit('update:modelValue', target.value)
}
</script>
