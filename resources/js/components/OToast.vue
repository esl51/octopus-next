<template>
  <div
    class="toast fade"
    role="alert"
    :class="classes"
  >
    <div class="d-flex align-items-center">
      <div
        v-if="icon"
        class="toast-body pe-0"
      >
        <o-icon :name="icon" />
      </div>
      <div class="toast-body flex-grow-1">
        <div
          v-if="title || hint"
          class="me-auto d-flex"
        >
          <strong
            v-if="title"
            class="me-auto"
          >
            {{ title }}
          </strong>
          <small
            v-if="hint"
            class="opacity-50"
          >
            {{ hint }}
          </small>
        </div>
        <div v-if="body">
          {{ body }}
        </div>
      </div>
      <button
        type="button"
        class="btn-close"
        :class="closeClasses"
        :aria-label="$t('global.close')"
        @click="hide"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'

// props
const props = withDefaults(
  defineProps<{
    variant?: string
    timeout?: number
    title?: string
    body?: string
    hint?: string
  }>(),
  {
    variant: undefined,
    timeout: 5000,
    title: undefined,
    body: undefined,
    hint: undefined,
  },
)

const localShow = ref(false)
const localShowing = ref(false)

// classes
const classes = computed(() => ({
  [`bg-${props.variant}`]: props.variant !== undefined,
  [`text-bg-${props.variant}`]: props.variant !== undefined,
  'border-0': props.variant !== undefined,
  show: !!localShow.value,
  showing: !!localShowing.value,
}))

const closeClasses = computed(() => ({
  'btn-close-white': props.variant !== undefined,
}))

const icon = computed(() => {
  let icon: string | null = null
  if (props.variant == 'info') {
    icon = 'info-circle'
  } else if (props.variant == 'success') {
    icon = 'circle-check'
  } else if (props.variant == 'danger') {
    icon = 'circle-x'
  } else if (props.variant == 'warning') {
    icon = 'alert-triangle'
  }
  return icon
})

const emit = defineEmits(['show', 'hide'])

// show
const show = () => {
  emit('show')
  localShowing.value = true
  localShow.value = true
  setTimeout(() => {
    localShowing.value = false
  }, 300)
}

// hide
const hide = () => {
  emit('hide')
  localShowing.value = true
  setTimeout(() => {
    localShow.value = false
    localShowing.value = false
  }, 300)
}

onMounted(() => {
  show()
  if (props.timeout > 0) {
    setTimeout(() => {
      hide()
    }, props.timeout)
  }
})
</script>
