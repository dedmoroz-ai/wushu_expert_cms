<?php

namespace App\Filament\Resources\CompetitionResource\Pages;

use App\Filament\Resources\CompetitionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCompetition extends EditRecord
{
    protected static string $resource = CompetitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // 1. Кнопка "Стартовый протокол"
            Actions\Action::make('start_protocol')
                ->label('Стартовый протокол (PDF)')
                ->icon('heroicon-o-printer')
                ->color('success') // Серый цвет
                ->url(fn ($record) => route('competition.start-list', $record))
                ->openUrlInNewTab(),

            // 2. Кнопка "Итоговый протокол"
            Actions\Action::make('final_protocol')
                ->label('Итоговый протокол (PDF)')
                ->icon('heroicon-o-trophy')
                ->color('success') // Зеленый цвет
                ->url(fn ($record) => route('competition.final-results', $record))
                ->openUrlInNewTab(),

            // 3. Стандартная кнопка удаления
            Actions\DeleteAction::make(),
        ];
    }
}
