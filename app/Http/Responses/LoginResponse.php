<?php

namespace App\Http\Responses;

use App\Filament\Pages\JudgePad;
use App\Filament\Pages\SuperJudgePad;
use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Support\Facades\Auth;

class LoginResponse implements LoginResponseContract
{
    /**
     * Куда перенаправлять пользователя сразу после входа.
     */
    public function toResponse($request)
    {
        $user = Auth::user();

        // 1. Если это ЛИНЕЙНЫЙ СУДЬЯ -> на пульт судьи
        if ($user->role === 'judge') {
            return redirect()->to(JudgePad::getUrl());
        }

        // 2. Если это СТАРШИЙ СУДЬЯ -> на пульт старшего
        // (Админа не трогаем, пусть идет в админку, даже если он может зайти на пульт)
        if ($user->role === 'head_judge') {
            return redirect()->to(SuperJudgePad::getUrl());
        }

        // 3. Все остальные (Админ, Тренер) -> на стандартную Главную (Dashboard)
        return redirect()->to(filament()->getHomeUrl());
    }
}
