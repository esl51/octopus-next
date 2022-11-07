<template>
  <b-form-group
    :label-for="id"
    :state="state"
    :description="hint"
    :label-size="size"
  >
    <template
      v-if="label || $slots.labelDescription"
      #label
    >
      {{ label }}
      <span
        v-if="$slots.labelDescription"
        class="form-label-description"
      >
        <slot name="labelDescription" />
      </span>
    </template>
    <slot />
    <b-form-invalid-feedback :state="state">
      <div v-html="errors" />
    </b-form-invalid-feedback>
  </b-form-group>
</template>

<script setup lang="ts">
import { inject } from 'vue'

const id: string | undefined = inject('id')
const state: boolean | undefined = inject('state')
const errors: string | undefined = inject('errors')
const label: string | undefined = inject('label')
const hint: string | undefined = inject('hint')
const size: string | undefined = inject('size')
</script>
