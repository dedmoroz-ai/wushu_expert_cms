<?php

namespace Database\Seeders;

use App\Models\AgeGroup;
use Illuminate\Database\Seeder;

class AgeGroupSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            // --- ДЕТИ (Мальчики) ---
            ['name' => 'Мальчики', 'gender' => 'male', 'min_age' => 4, 'max_age' => 6],
            ['name' => 'Мальчики', 'gender' => 'male', 'min_age' => 7, 'max_age' => 8],

            // --- ДЕТИ (Девочки) ---
            ['name' => 'Девочки', 'gender' => 'female', 'min_age' => 4, 'max_age' => 6],
            ['name' => 'Девочки', 'gender' => 'female', 'min_age' => 7, 'max_age' => 8],

            // --- ЮНОШИ / ДЕВУШКИ ---
            ['name' => 'Юноши', 'gender' => 'male', 'min_age' => 9, 'max_age' => 11],
            ['name' => 'Девушки', 'gender' => 'female', 'min_age' => 9, 'max_age' => 11],

            ['name' => 'Юноши', 'gender' => 'male', 'min_age' => 12, 'max_age' => 14],
            ['name' => 'Девушки', 'gender' => 'female', 'min_age' => 12, 'max_age' => 14],

            // --- ЮНИОРЫ / ЮНИОРКИ ---
            ['name' => 'Юниоры', 'gender' => 'male', 'min_age' => 15, 'max_age' => 17],
            ['name' => 'Юниорки', 'gender' => 'female', 'min_age' => 15, 'max_age' => 17],

            // --- ВЗРОСЛЫЕ ---
            ['name' => 'Мужчины', 'gender' => 'male', 'min_age' => 18, 'max_age' => 40],
            ['name' => 'Женщины', 'gender' => 'female', 'min_age' => 18, 'max_age' => 40],

            // --- ВЕТЕРАНЫ ---
            ['name' => 'Ветераны мужчины', 'gender' => 'male', 'min_age' => 41, 'max_age' => 55],
            ['name' => 'Ветераны женщины', 'gender' => 'female', 'min_age' => 41, 'max_age' => 55],

            ['name' => 'Ветераны мужчины', 'gender' => 'male', 'min_age' => 56, 'max_age' => 99],
            ['name' => 'Ветераны женщины', 'gender' => 'female', 'min_age' => 56, 'max_age' => 99],
        ];

        foreach ($groups as $group) {
            // Мы проверяем совпадение по всем полям, чтобы "Мальчики 4-6" и "Мальчики 7-8" создались как разные записи
            AgeGroup::firstOrCreate($group);
        }
    }
}
