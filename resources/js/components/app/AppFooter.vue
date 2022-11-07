<template>
  <footer class="footer footer-transparent d-print-none">
    <div class="container-xl">{{ year }} &copy; {{ $appConfig.name }}</div>
  </footer>
</template>

<script setup lang="ts">
const year = new Date().getFullYear()
</script>
