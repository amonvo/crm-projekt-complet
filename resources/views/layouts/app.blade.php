<!DOCTYPE html>
<html lang="cs" 
      x-data="{ 
          darkMode: JSON.parse(localStorage.getItem('darkMode') || 'false') 
      }" 
      x-init="$watch('darkMode', val => localStorage.setItem('darkMode', JSON.stringify(val)))" 
      :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'CRM System')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 dark:bg-gray-900 transition-colors duration-200">
    <div class="min-h-screen">
        @yield('content')
    </div>
</body>
</html>
