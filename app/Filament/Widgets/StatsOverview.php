<?php

namespace App\Filament\Widgets;

use App\Models\Athlete;
use App\Models\Club;
use App\Models\Registration;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsOverview extends BaseWidget
{
    // Обновлять данные раз в 30 секунд (по желанию)
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $user = Auth::user();

        // --- ЛОГИКА ДЛЯ ТРЕНЕРА (если есть club_id) ---
        if ($user && $user->club_id) {
            return [
                Stat::make('Мои спортсмены', Athlete::where('club_id', $user->club_id)->count())
                    ->description('Активные спортсмены')
                    ->descriptionIcon('heroicon-m-user-group')
                    ->color('info'),

                Stat::make('Мои заявки', Registration::whereHas('athlete', function ($q) use ($user) {
                        $q->where('club_id', $user->club_id);
                    })->count())
                    ->description('Поданные заявки')
                    ->descriptionIcon('heroicon-m-clipboard-document-check')
                    ->color('success'),
            ];
        }

        // --- ЛОГИКА ДЛЯ АДМИНА (если нет club_id) ---
        return [
            Stat::make('Всего клубов', Club::count())
                ->description('Зарегистрированные организации')
                ->chart([7, 2, 10, 3, 15, 4, 17]) // Просто график для красоты
                ->color('primary'),

            Stat::make('Всего спортсменов', Athlete::count())
                ->description('Общая база')
                ->color('success'),

            Stat::make('Всего заявок', Registration::count())
                ->description('Все соревнования')
                ->color('warning'),
        ];
    }
}
