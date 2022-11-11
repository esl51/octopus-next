<template>
  <div
    v-if="meta.from"
    class="row align-items-center"
  >
    <p class="col m-sm-0 text-muted">
      {{ $t('global.items_table_footer', { ...meta }) }}
    </p>
    <o-pagination
      class="col-12 col-sm-auto m-0 ms-auto"
      :meta="meta"
      @change="emit('paginate', $event)"
    />
  </div>
</template>

<script setup lang="ts">
import { Meta } from '@/api/items'

// props
defineProps<{
  meta: Meta
}>()

// emits
const emit = defineEmits(['paginate'])
</script>
