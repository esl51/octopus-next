<template>
  <ul
    v-if="localMeta.last_page && localMeta.last_page > 1"
    class="pagination m-0 ms-auto"
  >
    <li
      class="page-item"
      :class="{
        disabled: !prev,
      }"
    >
      <b-link
        class="page-link"
        :tabindex="!prev ? -1 : null"
        :aria-disabled="!prev"
        @click.prevent="change(prev)"
      >
        <chevron-left-icon class="icon" />
      </b-link>
    </li>
    <template
      v-for="page in pages"
      :key="page"
    >
      <li
        v-if="typeof page === 'number'"
        class="page-item"
        :class="{
          active: page + 1 === localMeta.current_page,
        }"
      >
        <b-link
          class="page-link"
          @click.prevent="change(page + 1)"
        >
          {{ page + 1 }}
        </b-link>
      </li>
      <li
        v-else
        class="page-item text-muted"
      >
        <span class="page-link pe-none">...</span>
      </li>
    </template>
    <li
      class="page-item"
      :class="{
        disabled: !next,
      }"
    >
      <b-link
        class="page-link"
        :tabindex="!next ? -1 : null"
        :aria-disabled="!next"
        @click.prevent="change(next)"
      >
        <chevron-right-icon class="icon" />
      </b-link>
    </li>
  </ul>
</template>

<script setup lang="ts">
import { Meta } from '@/types'
import { computed } from 'vue'

// props
const props = withDefaults(
  defineProps<{
    meta: Meta
    count?: number
  }>(),
  {
    count: 5,
  }
)

// emits
const emit = defineEmits(['change'])

// change
const change = (page: number | null) => {
  if (page !== null) {
    emit('change', page)
  }
}

// local
const localMeta = computed(() => props.meta)

// prev
const prev = computed(() =>
  props.meta.current_page > 1 ? props.meta.current_page - 1 : null
)

//next
const next = computed(() =>
  props.meta.current_page < props.meta.last_page
    ? props.meta.current_page + 1
    : null
)

// pages
const pages = computed(() => {
  let width = props.count
  const total = props.meta.last_page
  const current = props.meta.current_page
  if (width < 7) {
    width = 7
  }
  if (width % 2 === 0) {
    width++
  }
  if (total < width) {
    return [...new Array(total).keys()]
  }
  const left = Math.max(
    0,
    Math.min(total - width, current - Math.floor(width / 2))
  )
  const items: (string | number)[] = new Array(width)
  for (let i = 0; i < width; i += 1) {
    items[i] = i + left
  }
  if (items[0] > 0) {
    items[0] = 0
    items[1] = 'prev-more'
  }
  if (items[items.length - 1] < total - 1) {
    items[items.length - 1] = total - 1
    items[items.length - 2] = 'next-more'
  }
  return items
})
</script>
