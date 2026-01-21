<?php

namespace App\Filament\Resources\RegistrationResource\Pages;

use App\Filament\Resources\RegistrationResource;
use App\Models\AgeGroup;
use App\Models\Athlete;
use App\Models\Competition;
use App\Models\Style;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;

class CreateRegistration extends CreateRecord
{
    protected static string $resource = RegistrationResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // 1. Сборка массива стилей
        $taolu = $data['events_taolu_virtual'] ?? [];
        $trad = $data['events_trad_virtual'] ?? [];
        
        // Объединяем выбранные галочки в один массив
        $selectedStyleIds = array_unique(array_merge($taolu, $trad));

        // --- ЛОГИКА ПАРТНЕРА ---
        $partnerId = $data['partner_id'] ?? null;
        
        // Удаляем лишние поля из данных
        unset($data['events_taolu_virtual']);
        unset($data['events_trad_virtual']);
        unset($data['events']); 
        unset($data['partner_id']); // Убираем из общего шаблона

        // 2. ОПРЕДЕЛЕНИЕ ВОЗРАСТНОЙ ГРУППЫ
        $athlete = Athlete::find($data['athlete_id']);
        $competition = Competition::find($data['competition_id']);
        
        // Значения по умолчанию
        $data['age_group_id'] = null;
        $data['age_group_label'] = 'Не определено';

        if ($athlete && $competition && $athlete->birth_date) {
            $age = $competition->start_date->year - $athlete->birth_date->year;
            
            $dbGroup = $this->findAgeGroupInDb($athlete, $age);

            if ($dbGroup) {
                $data['age_group_id'] = $dbGroup->id;
                $data['age_group_label'] = "{$dbGroup->name} ({$dbGroup->min_age}-{$dbGroup->max_age} лет)";
            } else {
                $data['age_group_id'] = null;
                $data['age_group_label'] = $this->calculateManualLabel($athlete, $age);
            }
        }

        $record = null;
        $countNew = 0;
        $countExists = 0;

        // 3. СОХРАНЕНИЕ (Цикл по стилям)
        foreach ($selectedStyleIds as $styleId) {
            $singleRowData = $data;
            $singleRowData['style_id'] = $styleId; 
            
            // Если записи нет - ставим статус 0. Если есть - статус не трогаем (см. ниже)
            $singleRowData['status'] = 0;

            // --- ПРОВЕРКА НА ДУЙЛЯНЬ ---
            $style = Style::find($styleId);
            $targetPartnerId = null; // По умолчанию партнер NULL

            // Если в названии стиля есть "дуйлянь" — используем выбранного партнера
            if ($style && str_contains(mb_strtolower($style->name), 'дуйлянь')) {
                $targetPartnerId = $partnerId;
            }
            
            $singleRowData['partner_id'] = $targetPartnerId;
            // ---------------------------

            // firstOrCreate: Находит существующую запись ИЛИ создает новую
            $record = static::getModel()::firstOrCreate(
                [
                    'competition_id' => $singleRowData['competition_id'],
                    'athlete_id'     => $singleRowData['athlete_id'],
                    'style_id'       => $styleId,
                ],
                $singleRowData
            );

            // ! ВАЖНОЕ ИСПРАВЛЕНИЕ !
            // Если запись уже существовала (например, создана с ошибкой ранее), 
            // firstOrCreate её не изменил. 
            // Мы принудительно обновляем partner_id, чтобы исправить ошибку на скриншоте.
            if ($record->partner_id != $targetPartnerId) {
                $record->update(['partner_id' => $targetPartnerId]);
            }

            if ($record->wasRecentlyCreated) {
                $countNew++;
            } else {
                $countExists++;
            }
        }

        // Страховка на случай пустых стилей
        if (!$record) {
             $data['partner_id'] = $partnerId;
             $record = static::getModel()::create($data); 
        }

        Notification::make()
            ->title('Обработка завершена')
            ->body("Добавлено/Обновлено: {$countNew}. Найдено существующих: {$countExists}.")
            ->success()
            ->send();

        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function findAgeGroupInDb($athlete, $age)
    {
        $genderRaw = mb_strtolower($athlete->gender ?? '');
        $gender = in_array($genderRaw, ['male', 'm', 'man', 'мужской', 'муж', 'м']) ? 'male' : 'female';

        return AgeGroup::query()
            ->where('gender', $gender)
            ->where('min_age', '<=', $age)
            ->where('max_age', '>=', $age)
            ->first();
    }

    protected function calculateManualLabel($athlete, $age)
    {
        $genderRaw = mb_strtolower($athlete->gender ?? ''); 
        $isMale = in_array($genderRaw, ['male', 'm', 'man', 'мужской', 'муж', 'м']);

        if ($age < 9) return $isMale ? "Мальчики (до 9 лет)" : "Девочки (до 9 лет)";
        if ($age >= 9 && $age <= 11) return $isMale ? "Мальчики (9-11 лет)" : "Девочки (9-11 лет)";
        if ($age >= 12 && $age <= 14) return $isMale ? "Юноши (12-14 лет)" : "Девушки (12-14 лет)";
        if ($age >= 15 && $age <= 17) return $isMale ? "Юниоры (15-17 лет)" : "Юниорки (15-17 лет)";
        if ($age >= 18 && $age <= 35) return $isMale ? "Мужчины (18-35 лет)" : "Женщины (18-35 лет)";
        if ($age >= 36) return $isMale ? "Ветераны (36+ лет)" : "Ветераны-женщины (36+ лет)";

        return "Категория ($age лет)";
    }
}
