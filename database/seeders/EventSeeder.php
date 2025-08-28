<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Event::create([
            'name' => 'JAI 22nd Anniversary',
            'date' => '2025-09-01',
            'place' => 'Jatim Park 1 - Batu',
            'passcode' => 'JAI@22'
        ]);
    }
}
