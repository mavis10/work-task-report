<?php

namespace Database\Seeders;

use App\Models\Call;
use App\Models\ResolutionType;
use App\Models\WorkTask;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkTaskReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    
    public function run(): void
    {
        // 1. Create fixed resolution types
        $resolutionTypes = collect([
            ['name' => 'Fix Complete – Parts Collection Required', 'description' => 'Parts collection needed'],
            ['name' => 'Further Diagnosis – Internal – 3rd Party Repair', 'description' => 'Third party repair'],
            ['name' => 'Awaiting Purchase Order from Customer', 'description' => 'Waiting for PO'],
            ['name' => 'Call on Hold at Customer Request', 'description' => 'On hold'],
            ['name' => 'Fix Complete – Collection Arranged', 'description' => 'Courier arranged'],
        ])->map(fn ($data) => ResolutionType::create($data));

        // 2. Create ONLY valid calls (important)
        $validCalls = Call::factory()->count(10)->create([
            'stage' => Call::STAGE_OPEN,
        ]);

        // 3. Create some invalid calls (for testing filter)
        Call::factory()->count(3)->create([
            'stage' => Call::STAGE_DRAFT,
        ]);

        Call::factory()->count(2)->create([
            'stage' => Call::STAGE_ARCHIVED,
        ]);

        // 4. Create work tasks for VALID calls (guaranteed results)
        foreach ($validCalls as $call) {
            WorkTask::create([
                'call_id' => $call->id,
                'resolution_type_id' => $resolutionTypes->random()->id,
                'work_started_at' => '2026-04-10 09:00:00',
                'work_completed_at' => '2026-04-10 12:00:00',
                'created_at' => '2026-04-10 09:00:00', 
            ]);
        }

        // 5. Create some edge cases (to test exclusions)
        WorkTask::factory()->count(3)->create([
            'resolution_type_id' => null, // should be excluded
        ]);
    }
}
