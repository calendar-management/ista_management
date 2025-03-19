<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Teaching;
use App\Models\Progress;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->seedMissingProgress();
    }

    protected function seedMissingProgress(): void
    {
        $teachings = Teaching::with('progress', 'module')->get();

        foreach ($teachings as $teaching) {
            if (!$teaching->progress) {
                Progress::create([
                    'id_teaching' => $teaching->id_teaching,
                    'hours_completed' => 0,
                    'final_exam_date' => Carbon::now()->addWeeks(6),
                    'module_start_date' => Carbon::now(),
                    'hours_affected' => json_encode([]),
                    'remaining_hours' => $teaching->module->nbrHeure ?? 0,
                ]);
            }
        }

        $this->command->info('Progress records have been seeded where missing.');
    }
}
