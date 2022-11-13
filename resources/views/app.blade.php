@php
$appConfig = [
  'name' => config('app.name'),
  'locale' => app()->getLocale(),
  'locales' => config('app.locales'),
  'fallbackLocale' => config('app.fallback_locale'),
  'authFeatures' => config('fortify.features'),
];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
  @vite(['resources/js/app.ts'])
</head>
<body>
  <div id="app" class="app"></div>
  <script>
    window.config = @json($appConfig);
  </script>
</body>
</html>
