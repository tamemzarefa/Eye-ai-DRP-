<!doctype html>
<html lang="en" dir="rtl">
  <head>
    <meta charset="UTF-8" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>EyeAI - Diagnostic Platform</title>
    
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
  </head>
  <body class="bg-gray-50 antialiased font-sans">
    <div id="root"></div>
  </body>
</html>
