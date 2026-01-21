<?php

namespace App\Filament\Resources\CompetitionResource\Pages;

use App\Filament\Resources\CompetitionResource;
use App\Models\Competition;
use App\Models\Registration;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Notifications\Notification;
use Filament\Actions\Action;

class ManageCompetition extends Page
{
    use InteractsWithRecord; // Позволяет работать с конкретным соревнованием

    protected static string $resource = CompetitionResource::class;

    protected static string $view = 'filament.resources.competition-resource.pages.manage-competition';

    protected static ?string $title = 'Пульт управления';
    protected static ?string $navigationIcon = 'heroicon-o-computer-desktop';

    // Подгружаем данные при открытии страницы
    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    // --- ДЕЙСТВИЯ (КНОПКИ) ---

    // 1. Начать / Возобновить
    public function start()
    {
        $this->record->update(['status_code' => 1]); // 1 = Идет
        
        // Если спортсмен еще не выбран, выбираем первого
        if (!$this->record->current_registration_id) {
            $this->setFirstAthlete();
        }
        
        Notification::make()->title('Соревнование запущено!')->success()->send();
    }

    // 2. Пауза
    public function pause()
    {
        $this->record->update(['status_code' => 2]); // 2 = Пауза
        Notification::make()->title('Соревнование на паузе')->warning()->send();
    }

    // 3. Завершить
    public function stop()
    {
        $this->record->update(['status_code' => 3]); // 3 = Завершено
        $this->record->update(['current_registration_id' => null]); // Убираем спортсмена с ковра
        Notification::make()->title('Соревнование завершено')->info()->send();
    }

    // 4. Следующий участник
    public function nextAthlete()
    {
        $current = $this->record->currentRegistration;
        
        // Ищем следующего по sort_order
        $next = Registration::where('competition_id', $this->record->id)
            ->where('sort_order', '>', $current ? $current->sort_order : 0)
            ->orderBy('sort_order', 'asc')
            ->first();

        if ($next) {
            $this->changeAthlete($next);
        } else {
            Notification::make()->title('Это последний участник в списке')->warning()->send();
        }
    }

    // 5. Предыдущий участник
    public function prevAthlete()
    {
        $current = $this->record->currentRegistration;

        if (!$current) return;

        $prev = Registration::where('competition_id', $this->record->id)
            ->where('sort_order', '<', $current->sort_order)
            ->orderBy('sort_order', 'desc')
            ->first();

        if ($prev) {
            $this->changeAthlete($prev);
        }
    }

    // Вспомогательная функция смены спортсмена
    protected function changeAthlete(Registration $reg)
    {
        // 1. Ставим статус "На ковре" (1) этому спортсмену
        // Сбрасываем статус предыдущему (если был)
        if ($this->record->currentRegistration) {
            $this->record->currentRegistration->update(['status' => 2]); // 2 = Выступил (Оценен или просто ушел)
        }

        $reg->update(['status' => 1]); // 1 = На ковре

        // 2. Обновляем запись соревнования
        $this->record->update(['current_registration_id' => $reg->id]);

        Notification::make()->title("На ковре: {$reg->athlete->name}")->success()->send();
    }

    protected function setFirstAthlete()
    {
        $first = Registration::where('competition_id', $this->record->id)
            ->orderBy('sort_order', 'asc')
            ->first();
            
        if ($first) {
            $this->changeAthlete($first);
        }
    }
}
