<template>
  <app-verify-alert />
  <div class="card">
    <div class="card-body">
      <div
        v-if="$appConfig.authFeatures.includes('update-profile-information')"
      >
        <!-- avatar -->
        <h3 class="card-title">{{ $t('auth.profile.avatar_title') }}</h3>
        <div class="row align-items-center">
          <div class="col-auto">
            <o-avatar
              :image="user?.photo_url"
              :placeholder="user?.name_placeholder"
              size="xl"
            />
          </div>
          <div class="col-auto">
            <label
              class="btn"
              :class="{ disabled: updatingAvatar }"
            >
              <input
                type="file"
                accept="image/jpeg,image/png"
                hidden
                @change="updateAvatar"
              />
              <b-spinner
                v-if="updatingAvatar"
                class="me-2"
                small
              />
              {{ $t('auth.profile.change_avatar_button') }}
            </label>
          </div>
          <div
            v-if="user?.photo_url"
            class="col-auto"
          >
            <o-button
              variant="ghost-danger"
              :disabled="deletingAvatar"
              @click="deleteAvatar"
            >
              <b-spinner
                v-if="deletingAvatar"
                class="me-2"
                small
              />
              {{ $t('auth.profile.delete_avatar_button') }}
            </o-button>
          </div>
        </div>
        <!-- information -->
        <h3 class="card-title mt-4">{{ $t('auth.profile.details_title') }}</h3>
        <v-form
          class="row g-3"
          :form="profileForm"
          @submit="updateProfile"
        >
          <div class="col-auto">
            <v-input
              :label="$t('auth.profile.name_label')"
              :placeholder="$t('auth.profile.name_placeholder')"
              name="name"
            />
          </div>
          <div class="col-auto">
            <v-input
              :label="$t('auth.profile.email_label')"
              :placeholder="$t('auth.profile.email_placeholder')"
              name="email"
              type="email"
            />
          </div>
          <div class="col-auto">
            <div class="mb-2">&nbsp;</div>
            <v-submit
              class="mb-3"
              variant="primary"
            >
              {{ $t('global.update') }}
            </v-submit>
          </div>
        </v-form>
      </div>
      <!-- password -->
      <template v-if="$appConfig.authFeatures.includes('update-passwords')">
        <h3 class="card-title mt-4">
          {{ $t('auth.profile.password_title') }}
        </h3>
        <v-form
          class="row g-3"
          :form="passwordForm"
          @submit="updatePassword"
        >
          <div class="col-auto">
            <v-input
              :label="$t('auth.profile.current_password_label')"
              :placeholder="$t('auth.profile.current_password_placeholder')"
              name="current_password"
              type="password"
            />
          </div>
          <div class="col-auto">
            <v-input
              :label="$t('auth.profile.password_label')"
              :placeholder="$t('auth.profile.password_placeholder')"
              name="password"
              type="password"
              autocomplete="new-password"
            />
          </div>
          <div class="col-auto">
            <div class="mb-2">&nbsp;</div>
            <v-submit
              class="mb-3"
              variant="default"
            >
              {{ $t('global.update') }}
            </v-submit>
          </div>
        </v-form>
      </template>
    </div>
  </div>
</template>

<script setup lang="ts">
import authApi, { authUrls } from '@/api/auth'
import AppVerifyAlert from '@/components/app/AppVerifyAlert.vue'
import { useConfirm } from '@/composables/useConfirm'
import { usePage } from '@/composables/usePage'
import { useToast } from '@/composables/useToast'
import { useAuthStore } from '@/stores/auth'
import Form from 'vform'
import { computed, onMounted, reactive, ref } from 'vue'
import { useI18n } from 'vue-i18n'

const authStore = useAuthStore()
const { success } = useToast()
const { confirm } = useConfirm()
const { t } = useI18n()

// page
usePage({
  title: t('auth.profile.title'),
})

// avatar
const updatingAvatar = ref(false)
const updateAvatar = async (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target && target.files && target.files.length) {
    updatingAvatar.value = true
    try {
      await authApi.updateAvatar({
        avatar: target.files[0],
      })
      authStore.fetchUser()
    } finally {
      updatingAvatar.value = false
    }
  }
}
const deletingAvatar = ref(false)
const deleteAvatar = async () => {
  if (
    await confirm({
      title: t('global.confirm_title'),
      body: t('auth.profile.delete_avatar_confirmation'),
      yesTitle: t('global.yes_delete'),
      yesVariant: 'danger',
    })
  ) {
    deletingAvatar.value = true
    try {
      await authApi.deleteAvatar()
      authStore.fetchUser()
    } finally {
      deletingAvatar.value = false
    }
  }
}
// profile form
const profileForm = reactive(
  new Form({
    name: '',
    email: '',
  })
)
const updateProfile = async () => {
  await profileForm.put(authUrls.profileInformation)
  success({
    body: t('auth.profile.details_updated_text'),
  })
  await authStore.fetchUser()
}
onMounted(async () => {
  await authStore.fetchUser()
  profileForm.name = user.value?.name
  profileForm.email = user.value?.email
})

// password form
const passwordForm = reactive(
  new Form({
    current_password: '',
    password: '',
  })
)
const updatePassword = async () => {
  await passwordForm.put(authUrls.password)
  success({
    body: t('auth.profile.password_updated_text'),
  })
}

// user
const user = computed(() => authStore.user)
</script>
