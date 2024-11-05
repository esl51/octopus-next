<template>
  <div>
    <div
      v-if="title"
      class="form-label"
    >
      {{ title }}
    </div>
    <form
      v-if="uploadUrl"
      ref="dataForm"
      class="dropzone flex-grow-1 d-flex flex-column"
      action="."
      autocomplete="off"
      novalidate
    >
      <div
        ref="previewContainer"
        class="dropzone-previews list-group"
      />
      <input
        type="hidden"
        name="filable_id"
        :value="filableId.toString()"
      />
      <input
        type="hidden"
        name="filable_type"
        :value="filableType"
      />
      <input
        type="hidden"
        name="type"
        :value="type"
      />
      <div class="fallback">
        <input
          name="file"
          type="file"
        />
      </div>
      <div
        class="dz-message border rounded-2 text-center text-muted p-5 flex-grow-1 d-flex flex-column align-items-center justify-content-center"
      >
        <o-button>{{ $t('global.dropzone_button') }}</o-button>
        <div class="d-none d-md-block mt-2">
          {{ hint || $t('global.dropzone_hint') }}
        </div>
      </div>
    </form>
    <div
      v-show="false"
      ref="previewTemplate"
    >
      <div class="dz-preview list-group-item p-2 bg-gray-400">
        <div class="d-flex align-items-center min-w-0">
          <o-avatar
            :icon="IconFileUpload"
            class="flex-shrink-0 me-2"
          />
          <div class="flex-fill min-w-0">
            <div
              class="fw-normal text-truncate"
              data-dz-name
            ></div>
            <small
              class="text-muted"
              data-dz-size
            ></small>
          </div>
        </div>
        <div class="progress mt-2">
          <div
            class="progress-bar"
            data-dz-uploadprogress
          />
        </div>
        <o-alert
          variant="danger"
          data-dz-errormessage
          class="dz-error-message mt-3 mb-0"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import Dropzone from 'dropzone'
import { IconFileUpload } from '@tabler/icons-vue'
import { ref, watchEffect } from 'vue'
import { filesApi } from '@/modules/files/api'

// props
const props = withDefaults(
  defineProps<{
    title?: string
    filableId: number
    filableType: string
    type: string
    hint?: string | null
    success?: string | null
    acceptedFiles?: string
    maxFilesize?: number
    maxFiles?: number | undefined
  }>(),
  {
    title: undefined,
    hint: undefined,
    success: undefined,
    acceptedFiles: undefined,
    maxFilesize: undefined,
    maxFiles: undefined,
  },
)

// preview template
const previewTemplate = ref<HTMLElement | null>(null)

// preview container
const previewContainer = ref<HTMLElement | null>(null)

// create dropzone
const createDropzone = (
  form: HTMLElement,
  options: Dropzone.DropzoneOptions,
) => {
  const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    ?.getAttribute('content')
  const dropzone = new Dropzone(form, {
    ...({
      method: 'post',
      parallelUploads: 3,
      uploadMultiple: false,
      maxFiles: props.maxFiles,
      createImageThumbnails: false,
      previewTemplate: previewTemplate.value?.innerHTML,
      previewsContainer: previewContainer.value,
      headers: {
        'X-CSRF-TOKEN': csrfToken,
      },
      maxfilesexceeded(file) {
        dropzone.removeAllFiles()
        dropzone.addFile(file)
      },
      sending(file, xhr) {
        const send = xhr.send
        xhr.send = (body: FormData) => {
          send.call(xhr, body)
        }
      },
    } as Dropzone.DropzoneOptions),
    ...options,
  })
}

const uploadUrl = filesApi.url

// data form
const dataForm = ref<HTMLElement | null>(null)
watchEffect(async () => {
  if (dataForm.value && uploadUrl) {
    createDropzone(dataForm.value, {
      url: uploadUrl,
      acceptedFiles: props.acceptedFiles,
      maxFilesize: props.maxFilesize,
      success: async () => {
        emit('success')
      },
    })
  }
})

// emits
const emit = defineEmits(['success'])
</script>
