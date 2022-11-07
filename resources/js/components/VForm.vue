<template>
  <b-form
    @submit.prevent="submit"
    @keydown="form.onKeydown($event)"
  >
    <slot />
    <div
      v-if="$slots.footer"
      class="form-footer"
    >
      <slot name="footer" />
    </div>
  </b-form>
</template>

<script setup lang="ts">
import Form from 'vform'
import { provide, computed } from 'vue'

// props
const props = defineProps<{
  form: Form
}>()

// emits
const emit = defineEmits(['submit'])

// submit
const submit = (e: Event) => {
  e.preventDefault()
  emit('submit')
}

// form
provide(
  'form',
  computed(() => props.form)
)
</script>
