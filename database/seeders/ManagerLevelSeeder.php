<?php

namespace Database\Seeders;

use App\Models\ManagerLevel;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ManagerLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding predefined manager levels...');
        
        ManagerLevel::seedPredefinedLevels();
        
        $this->command->info('Predefined manager levels seeded successfully!');
    }
}
