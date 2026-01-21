<?php

namespace App\Filament\Pages;

use App\Models\Competition;
use App\Models\Score;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class JudgePad extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationLabel = 'Пульт ввода оценок';
    protected static ?string $title = 'Пульт судьи';
    
    // 1. НАСТРОЙКА ВИДИМОСТИ В МЕНЮ
    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check() && Auth::user()->role === 'judge';
    }

    protected static string $view = 'filament.pages.judge-pad';

    public function getHeading(): string { return ''; }

    // ПЕРЕМЕННЫЕ
    public $score = '';
    public $registrationId = null;
    
    public $athleteName = '';
    public $athleteStyle = '';
    public $athleteGroup = ''; 
    public $athleteNumber = '';
    
    public $statusMessage = '';
    public $canVote = false;

    public function mount()
    {
        if (!Auth::check() || Auth::user()->role !== 'judge') {
            abort(403, 'Доступ запрещен. Только для линейных судей.');
        }

        $this->loadState();
    }

    public function loadState()
    {
        $competition = Competition::where('status_code', 1)->first();

        if (!$competition) {
            $this->resetPad('Турнир не запущен');
            return;
        }

        $currentReg = $competition->currentRegistration;

        if (!$currentReg) {
            $this->resetPad('Ожидание выхода...');
            return;
        }

        if ($this->registrationId !== $currentReg->id) {
            $this->score = '';
            $this->registrationId = $currentReg->id;
        }

        // --- ЛОГИКА ИМЕН (ОДИНАКОВЫЙ РАЗМЕР) ---
        $fullName = $currentReg->athlete->surname . ' ' . $currentReg->athlete->name;

        if ($currentReg->partner) {
            $partnerName = $currentReg->partner->surname . ' ' . $currentReg->partner->name;
            // Просто перенос строки, без уменьшения шрифта
            $fullName .= '<br>' . $partnerName;
        }

        $this->athleteName = $fullName;
        // ---------------------------------------

        $this->athleteStyle = $currentReg->style->name ?? '';
        $this->athleteNumber = $currentReg->sort_order;

        // ЛОГИКА ГРУППЫ
        $group = $currentReg->ageGroup;
        if ($group) {
            $groupStr = $group->name;
            $min = $group->min_age;
            $max = $group->max_age;

            if (!is_null($min) && !is_null($max)) {
                $groupStr .= " ({$min}-{$max} лет)";
            }
            $this->athleteGroup = $groupStr;
        } else {
            $this->athleteGroup = '';
        }

        $alreadyVoted = Score::where('registration_id', $currentReg->id)
            ->where('judge_id', Auth::id())
            ->exists();

        if ($alreadyVoted) {
            $this->canVote = false;
            $this->statusMessage = "Оценка принята";
        } else {
            $this->canVote = true;
            $this->statusMessage = "ОЦЕНИВАНИЕ";
        }
    }

    public function addNumber($num)
    {
        if (!$this->canVote) return;
        if (strlen($this->score) >= 4) return;

        if ($this->score === '') {
            if ($num === '.') return; 
            if (is_numeric($num)) {
                if ($num < 5) {
                    Notification::make()->title('Оценка 5..9')->warning()->duration(1000)->send();
                    return; 
                }
            }
        }

        if ($num === '.' && str_contains($this->score, '.')) return;
        $this->score .= $num;
    }

    public function backspace()
    {
        if (!$this->canVote) return;
        $this->score = substr($this->score, 0, -1);
    }

    public function clear() { /* ... */ }

    public function submitScore()
    {
        if (strlen($this->score) < 3) return;
        if (!$this->canVote) return;

        $val = floatval($this->score);

        Score::create([
            'registration_id' => $this->registrationId,
            'judge_id' => Auth::id(),
            'score' => $val,
        ]);

        Notification::make()->title('Принято!')->success()->send();
        $this->loadState();
    }

    protected function resetPad($message)
    {
        $this->canVote = false;
        $this->statusMessage = $message;
        $this->athleteName = '';
        $this->athleteStyle = '';
        $this->athleteGroup = '';
        $this->athleteNumber = '';
        $this->score = '';
        $this->registrationId = null;
    }

    public function logout()
    {
        filament()->auth()->logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->to(filament()->getLoginUrl());
    }
}
