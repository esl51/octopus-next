<template>
  <div class="empty">
    <div class="empty-header">404</div>
    <p class="empty-title">{{ $t('error.not_found_title') }}</p>
    <p class="empty-subtitle text-muted">
      {{ $t('error.not_found_text') }}
    </p>
    <div class="empty-action">
      <router-link
        :to="{ name: 'home' }"
        class="btn btn-primary"
      >
        <arrow-left-icon />
        {{ $t('error.home_button') }}
      </router-link>
    </div>
  </div>
</template>

<script setup lang="ts">
//
</script>
