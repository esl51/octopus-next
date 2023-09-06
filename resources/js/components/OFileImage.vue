<template>
  <o-avatar
    :image="file.mime_type.match(/^image\//) ? file.url : null"
    :icon="getIcon(file.mime_type)"
    :size="size"
  />
</template>

<script setup lang="ts">
import { File } from '@/modules/files/types'
import { IconPhoto, IconFile } from '@tabler/icons-vue'

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
  let icon = IconFile
  if (mimeType.match(/^image\//)) {
    icon = IconPhoto
  }
  return icon
}
</script>
