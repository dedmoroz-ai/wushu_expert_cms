<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Auth;
use App\Filament\Pages\JudgePad;
use App\Filament\Pages\SuperJudgePad;

class Dashboard extends BaseDashboard
{
    // Иконка и название (для админов)
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Инфопанель';

    // 1. Скрываем ссылку в меню для судей
    public static function shouldRegisterNavigation(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Если это судья (любой), меню "Инфопанель" ему не нужно
        if ($user->isJudge()) {
            return false;
        }

        return true;
    }

    // 2. ЛОВУШКА: Если судья попал сюда (кнопка Назад), кидаем его на пульт
    public function mount()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->role === 'judge') {
            return redirect()->to(JudgePad::getUrl());
        }

        if ($user->role === 'head_judge') {
            return redirect()->to(SuperJudgePad::getUrl());
        }
    }
}
