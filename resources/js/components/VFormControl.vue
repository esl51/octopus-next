<template>
  <div
    class="mb-3"
    :class="state === false ? 'is-invalid' : undefined"
  >
    <label
      v-if="label || $slots.labelDescription"
      class="form-label"
      :class="'form-label-' + size"
      :for="label ? id : undefined"
    >
      {{ label }}
      <span
        v-if="$slots.labelDescription"
        class="form-label-description"
      >
        <slot name="labelDescription" />
      </span>
    </label>
    <slot />
    <div
      v-if="hint"
      class="form-text"
    >
      {{ hint }}
    </div>
    <div
      v-if="state === false && errors?.length"
      v-dompurify-html="errors?.join('<br />')"
      class="invalid-feedback"
    />
  </div>
</template>

<script setup lang="ts">
import { ComputedRef, inject } from 'vue'

const id: ComputedRef<string> | undefined = inject('id')
const errors: ComputedRef<Array<string>> | undefined = inject('errors')
const state: ComputedRef<boolean> | undefined = inject('state')
const label: ComputedRef<string> | undefined = inject('label')
const hint: ComputedRef<string> | undefined = inject('hint')
const size: ComputedRef<string> | undefined = inject('size')
</script>
