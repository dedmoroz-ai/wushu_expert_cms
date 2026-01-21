<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? 'Табло соревнований' }}</title>

        {{-- Подключаем Tailwind (через CDN для быстрого старта на этой странице) --}}
        <script src="https://cdn.tailwindcss.com"></script>
        
        {{-- Стили Livewire (автоматически) --}}
        @livewireStyles
    </head>
    <body class="antialiased bg-slate-900 text-white">
        
        {{-- Сюда Livewire вставит наш компонент scoreboard --}}
        {{ $slot }}

        @livewireScripts
    </body>
</html>
