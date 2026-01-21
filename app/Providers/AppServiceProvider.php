<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// Импортируем интерфейс Filament и наш новый класс ответа
use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;
use App\Http\Responses\LoginResponse;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Подменяем стандартный ответ при входе на наш (с редиректами по ролям)
        $this->app->bind(LoginResponseContract::class, LoginResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
