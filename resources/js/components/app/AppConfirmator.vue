<template>
  <o-confirm
    v-if="confirmation"
    :key="confirmation.id"
    v-model="confirmation.state"
    :title="confirmation.title"
    :body="confirmation.body"
    @yes="confirmation?.yes"
    @no="confirmation?.no"
  />
</template>

<script setup lang="ts">
import { useConfirmatorStore } from '@/stores/confirmator'
import { computed } from 'vue'

const confirmatorStore = useConfirmatorStore()
const confirmation = computed(() => confirmatorStore.confirmation)
</script>
