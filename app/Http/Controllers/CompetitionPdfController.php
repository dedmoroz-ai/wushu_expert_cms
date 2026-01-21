<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CompetitionPdfController extends Controller
{
    // Хелпер для получения Base64 картинки
    private function getImageBase64($path)
    {
        if (!$path) return null;
        
        // В Filament путь сохраняется относительно диска public
        $fullPath = storage_path('app/public/' . $path);

        if (file_exists($fullPath)) {
            $type = pathinfo($fullPath, PATHINFO_EXTENSION);
            $data = file_get_contents($fullPath);
            return 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
        return null;
    }

    private function getFormattedDate($date)
    {
        if (!$date) return '';
        Carbon::setLocale('ru'); 
        return Carbon::parse($date)->translatedFormat('j F Y') . ' г.';
    }

    public function startList(Competition $competition)
    {
        // 1. БЕРЕМ ОРГАНИЗАТОРА ИЗ СВЯЗИ (Federation)
        $organizerName = $competition->federation->name ?? 'ОРГАНИЗАТОР НЕ УКАЗАН';

        // 2. ПОДГОТОВКА КАРТИНОК (Используем твои названия полей)
        $logoBase64 = $this->getImageBase64($competition->organization_logo);
        $judgeSignBase64 = $this->getImageBase64($competition->chief_judge_signature);
        $secSignBase64 = $this->getImageBase64($competition->chief_secretary_signature);
        $stampBase64 = $this->getImageBase64($competition->organization_stamp);

        // 3. ФИО СУДЕЙ
        $judgeName = $competition->chief_judge_name;
        $secName = $competition->chief_secretary_name;

        $formattedDate = $this->getFormattedDate($competition->start_date);
        // Собираем Город + Адрес через запятую
        $locParts = [];
        if ($competition->city) $locParts[] = $competition->city;
        if ($competition->address) $locParts[] = $competition->address;
        $address = implode(', ', $locParts);
        // 4. ДАННЫЕ УЧАСТНИКОВ
        $registrations = $competition->registrations()
            ->with(['athlete', 'athlete.club', 'style', 'ageGroup', 'partner'])
            ->join('styles', 'registrations.style_id', '=', 'styles.id')
            ->join('age_groups', 'registrations.age_group_id', '=', 'age_groups.id')
            ->join('athletes', 'registrations.athlete_id', '=', 'athletes.id')
            ->orderBy('styles.sort_order')
            ->orderBy('age_groups.sort_order')
            ->orderBy('athletes.gender') 
            ->orderBy('registrations.sort_order') 
            ->select('registrations.*')
            ->get();

        $grouped = $registrations->groupBy(function ($reg) {
            $ageStr = "";
            if ($reg->ageGroup) {
                if ($reg->ageGroup->min_age && $reg->ageGroup->max_age) {
                    $ageStr = "({$reg->ageGroup->min_age}-{$reg->ageGroup->max_age} лет)";
                } elseif ($reg->ageGroup->min_age) {
                    $ageStr = "({$reg->ageGroup->min_age}+ лет)";
                }
            }
            return sprintf("%s — %s %s", $reg->style->name, $reg->ageGroup->name, $ageStr);
        });

        // Передаем переменные в View
        $pdf = Pdf::loadView('pdf.start-list', compact(
            'competition', 'grouped', 'organizerName', 'formattedDate', 'address',
            'logoBase64', 'judgeSignBase64', 'secSignBase64', 'stampBase64',
            'judgeName', 'secName'
        ));
        
        $pdf->setOption(['defaultFont' => 'DejaVu Sans', 'isRemoteEnabled' => true]);
        return $pdf->stream('start-list.pdf');
    }

    public function finalResults(Competition $competition)
    {
        // 1. ДАННЫЕ
        $organizerName = $competition->federation->name ?? 'ОРГАНИЗАТОР НЕ УКАЗАН';
        
        $logoBase64 = $this->getImageBase64($competition->organization_logo);
        $judgeSignBase64 = $this->getImageBase64($competition->chief_judge_signature);
        $secSignBase64 = $this->getImageBase64($competition->chief_secretary_signature);
        $stampBase64 = $this->getImageBase64($competition->organization_stamp);

        $judgeName = $competition->chief_judge_name;
        $secName = $competition->chief_secretary_name;
        
        $formattedDate = $this->getFormattedDate($competition->start_date);
        // Собираем Город + Адрес через запятую
        $locParts = [];
        if ($competition->city) $locParts[] = $competition->city;
        if ($competition->address) $locParts[] = $competition->address;
        $address = implode(', ', $locParts);
        // 2. УЧАСТНИКИ
        $registrations = $competition->registrations()
            ->with(['athlete', 'athlete.club', 'style', 'ageGroup', 'partner'])
            ->where('is_completed', true)
            ->join('styles', 'registrations.style_id', '=', 'styles.id')
            ->join('age_groups', 'registrations.age_group_id', '=', 'age_groups.id')
            ->join('athletes', 'registrations.athlete_id', '=', 'athletes.id')
            ->orderBy('styles.sort_order')
            ->orderBy('age_groups.sort_order')
            ->orderBy('athletes.gender')
            ->orderByDesc('registrations.final_score') 
            ->select('registrations.*')
            ->get();

        $grouped = $registrations->groupBy(function ($reg) {
            $ageStr = "";
            if ($reg->ageGroup) {
                if ($reg->ageGroup->min_age && $reg->ageGroup->max_age) {
                    $ageStr = "({$reg->ageGroup->min_age}-{$reg->ageGroup->max_age} лет)";
                } elseif ($reg->ageGroup->min_age) {
                    $ageStr = "({$reg->ageGroup->min_age}+ лет)";
                }
            }
            return sprintf("%s — %s %s", $reg->style->name, $reg->ageGroup->name, $ageStr);
        });

        $pdf = Pdf::loadView('pdf.final-results', compact(
            'competition', 'grouped', 'organizerName', 'formattedDate', 'address',
            'logoBase64', 'judgeSignBase64', 'secSignBase64', 'stampBase64',
            'judgeName', 'secName'
        ));
        
        $pdf->setOption(['defaultFont' => 'DejaVu Sans', 'isRemoteEnabled' => true]);
        return $pdf->stream('final-results.pdf');
    }
}
