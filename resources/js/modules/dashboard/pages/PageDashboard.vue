<template>
  <o-page-header :title="$t('dashboard.title')" />
  <app-verify-alert />
  <o-card>
    <h3 class="card-title">To Do</h3>
    <ul>
      <li>
        Причесать
        <a
          href="https://github.com/alexeymezenin/laravel-best-practices/blob/master/russian.md"
          target="_blank"
        >
          best practices
        </a>
      </li>
      <li>
        Доработать модуль списка файлов для переименования файлов (внутри
        объектов, а не в разделе Файлы)
      </li>
      <li>Сортировка перетаскиванием файлов в списке</li>
      <li>Просмотр изображений/аватарок в модальном окне</li>
      <li>Разработать систему виджетов</li>
    </ul>
  </o-card>
</template>

<script setup lang="ts">
import AppVerifyAlert from '@/components/app/AppVerifyAlert.vue'
import { usePage } from '@/composables/usePage'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

// page
usePage({
  title: t('dashboard.title'),
})
</script>
