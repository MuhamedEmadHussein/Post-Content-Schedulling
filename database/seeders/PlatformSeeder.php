<?php

namespace Database\Seeders;

use App\Models\Platform;
use Illuminate\Database\Seeder;

class PlatformSeeder extends Seeder
{
    public function run(): void
    {
        $platforms = [
            ['name' => 'X', 'type' => 'x'],
            ['name' => 'Instagram', 'type' => 'instagram'],
            ['name' => 'LinkedIn', 'type' => 'linkedin'],
        ];

        foreach ($platforms as $platform) {
            Platform::updateOrCreate(['type' => $platform['type']], $platform);
        }
    }
}