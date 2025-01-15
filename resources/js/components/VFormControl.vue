<template>
  <div
    class="form-field"
    :class="state === false ? 'is-invalid' : undefined"
  >
    <div
      v-if="label || $slots.labelDescription"
      class="form-label"
      :class="size ? 'form-label-' + size : ''"
    >
      <div class="d-flex align-items-center">
        <label :for="label ? id : undefined">{{ label }}</label>
        <span
          v-if="description"
          v-tooltip
          :title="description"
          :aria-label="description"
          class="text-primary ms-1"
        >
          <icon-info-square-rounded class="icon" />
        </span>
        <small
          v-if="$slots.labelDescription"
          class="fw-normal ms-auto"
        >
          <slot name="labelDescription" />
        </small>
      </div>
    </div>
    <slot />
    <div
      v-if="hint"
      class="form-text text-balance"
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
import { IconInfoSquareRounded } from '@tabler/icons-vue'
import { ComputedRef, inject } from 'vue'

const id: ComputedRef<string> | undefined = inject('id')
const errors: ComputedRef<Array<string>> | undefined = inject('errors')
const state: ComputedRef<boolean> | undefined = inject('state')
const label: ComputedRef<string> | undefined = inject('label')
const hint: ComputedRef<string> | undefined = inject('hint')
const description: ComputedRef<string> | undefined = inject('description')
const size: ComputedRef<string> | undefined = inject('size')
</script>
