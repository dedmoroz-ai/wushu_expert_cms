<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Support\Facades\Blade;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('Wushu Expert CMS')
            ->colors([
                'primary' => Color::Sky,
                'danger' => '#dd0000'
            ])
            ->navigationGroups([
                'Управление',
                'Справочники',
                'Турнир',
            ])
            // Логотип в меню
            ->brandLogo(asset('images/logo.png'))
            ->brandLogoHeight('6rem')
            ->font('Exo 2')
            // 1. ОСНОВНОЙ ФАВИКОН
            ->favicon(asset('favicon/favicon.ico'))

            // 2. ПОДКЛЮЧЕНИЕ МАНИФЕСТА И ИКОНОК
            ->renderHook(
                'panels::head.start',
                fn (): string => Blade::render(<<<HTML
                    <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png">
                    <link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
                    <link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
                    <link rel="manifest" href="/favicon/site.webmanifest">
                    <meta name="msapplication-TileColor" content="#da532c">
                    <meta name="theme-color" content="#ffffff">
                HTML)
            )

            // 3. ИСПРАВЛЕННЫЙ CSS
            ->renderHook(
                'panels::head.end',
                fn (): string => '<style>
                    /* 1. ЛЕВАЯ КОЛОНКА (Логотип) */
                    .fi-sidebar-header {
                        height: 8rem !important; 
                        min-height: 8rem !important;
                        padding-top: 0 !important;
                        padding-bottom: 0 !important;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    }
                    
                    .fi-sidebar-header .fi-logo {
                        height: auto;
                        max-height: 6rem;
                    }

                    /* 2. ПРАВАЯ КОЛОНКА (Топбар) */
                    .fi-topbar {
                        height: 8rem !important;
                        min-height: 8rem !important;
                        padding-top: 0 !important;
                        padding-bottom: 0 !important;
                    }

                    /* 3. Центрируем содержимое внутри Топбара */
                    .fi-topbar > nav {
                        height: 100% !important;
                        min-height: 100% !important;
                        padding-top: 0 !important;
                        padding-bottom: 0 !important;
                        display: flex;
                        align-items: center;
                    }
                </style>'
            )

            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                // Pages\Dashboard::class, // <--- УБРАЛИ, ЧТОБЫ РАБОТАЛ НАШ КАСТОМНЫЙ Dashboard.php
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
