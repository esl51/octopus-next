<template>
  <o-avatar
    :image="file.mime_type.match(/^image\//) ? file.url : null"
    :icon="getIcon(file.mime_type)"
    :size="size"
  />
</template>

<script setup lang="ts">
import { File } from '@/modules/files/types'

// props
withDefaults(
  defineProps<{
    file: File
    size?: string
  }>(),
  {
    size: undefined,
  },
)

// get file icon
const getIcon = (mimeType: string) => {
  let icon = 'file'
  if (mimeType.match(/^image\//)) {
    icon = 'photo'
  }
  return icon
}
</script>
