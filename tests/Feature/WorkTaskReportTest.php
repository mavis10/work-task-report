<?php

namespace Tests\Feature;

use App\Models\Call;
use App\Models\ResolutionType;
use App\Models\WorkTask;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Cache;

/**
 * Class WorkTaskReportTest
 *
 * Feature tests for the Work Task Resolution Report API.
 *
 * This test suite validates:
 * - API response structure
 * - Date range filtering
 * - Exclusion rules (Draft / Archived calls)
 * - Grouping logic by resolution type
 * - Handling of null resolution types
 * - Empty dataset scenarios
 *
 * Key Notes:
 * - Uses RefreshDatabase for isolation between tests
 * - Cache is flushed before each test to avoid stale data issues
 * - Test data is deterministic (no randomness for assertions)
 */
class WorkTaskReportTest extends TestCase
{
    use RefreshDatabase;

     /**
     * Predefined resolution types for deterministic testing
     */
     private const RESOLUTION_TYPE_A = [
        'name' => 'Fix Complete – Parts Collection Required',
        'description' => 'Parts collection needed',
    ];

    private const RESOLUTION_TYPE_B = [
        'name' => 'Awaiting Purchase Order from Customer',
        'description' => 'Waiting for PO',
    ];

    /**
     * Setup before each test
     *
     * Ensures:
     * - Fresh database state
     * - Cache cleared to prevent stale data interference
     */
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

     /**
     * Helper method to call the API endpoint
     *
     * @param string $from Start date (Y-m-d)
     * @param string $to   End date (Y-m-d)
     *
     * @return \Illuminate\Testing\TestResponse
     */
    private function getResponse($from = '2000-01-01', $to = '2100-01-01')
    {
        return $this->getJson("/api/reports/work-tasks/resolutions?from={$from}&to={$to}");
    }

    /**
     * Test API returns correct JSON structure
     */
    public function test_returns_json_structure()
    {
        $resolution = ResolutionType::factory()->create();
        $call = Call::factory()->create(['stage' => Call::STAGE_OPEN]);

        WorkTask::factory()->count(3)->create([
            'call_id' => $call->id,
            'resolution_type_id' => $resolution->id,
        ]);

        $this->getResponse()
            ->assertStatus(200)
            ->assertJsonStructure([
               'resolution_types' => [
                    '*' => ['id', 'name', 'description', 'count']
                ]
            ]);
    }

     /**
     * Test filtering by date range
     *
     * Ensures only records within the given date range are returned
     */
    public function test_filters_by_date_range()
    {
        $resolution = ResolutionType::factory()->create();
        $call = Call::factory()->create(['stage' => Call::STAGE_OPEN]);

        WorkTask::factory()->create([
            'call_id' => $call->id,
            'resolution_type_id' => $resolution->id,
            'created_at' => Carbon::parse('2024-01-10'),
        ]);

        WorkTask::factory()->create([
            'call_id' => $call->id,
            'resolution_type_id' => $resolution->id,
            'created_at' => Carbon::parse('1990-01-10'),
        ]);

        $this->getResponse('2024-01-01', '2024-12-31')
            ->assertJsonCount(1, 'resolution_types')
            ->assertJsonFragment(['count' => 1]);
    }

    /**
     * Test draft calls are excluded from report
    */
    public function test_excludes_draft_calls()
    {
        $resolution = ResolutionType::factory()->create();

        $validCall = Call::factory()->create(['stage' => Call::STAGE_OPEN]);
        $draftCall = Call::factory()->create(['stage' => Call::STAGE_DRAFT]);

        WorkTask::factory()->for($validCall)->create([
            'resolution_type_id' => $resolution->id,
        ]);

        WorkTask::factory()->create([
            'call_id' => $draftCall->id,
            'resolution_type_id' => $resolution->id,
        ]);

        $this->getResponse()
            ->assertJsonCount(1)
            ->assertJsonFragment(['count' => 1]);
    }

    /**
     * Test archived calls are excluded from report
    */
    public function test_excludes_archived_calls()
    {
        $resolution = ResolutionType::factory()->create();

        $validCall = Call::factory()->create(['stage' => Call::STAGE_OPEN]);
        $archivedCall = Call::factory()->create(['stage' => Call::STAGE_ARCHIVED]);

        WorkTask::factory()->create([
            'call_id' => $validCall->id,
            'resolution_type_id' => $resolution->id,
        ]);

        WorkTask::factory()->create([
            'call_id' => $archivedCall->id,
            'resolution_type_id' => $resolution->id,
        ]);

        $this->getResponse()
            ->assertJsonCount(1)
            ->assertJsonFragment(['count' => 1]);
    }

    /**
     * Test grouping by resolution type
     *
     * Ensures:
     * - Results are grouped correctly
     * - Counts are accurate per resolution type
    */
    public function test_groups_by_resolution_type()
    {
        $call = Call::factory()->create(['stage' => Call::STAGE_OPEN]);

        $resolutionA = ResolutionType::factory()->create(self::RESOLUTION_TYPE_A);
        $resolutionB = ResolutionType::factory()->create(self::RESOLUTION_TYPE_B);

        WorkTask::factory()->count(2)
            ->for($call)
            ->for($resolutionA, 'resolutionType')
            ->create();

        WorkTask::factory()->count(3)
            ->for($call)
            ->for($resolutionB, 'resolutionType')
            ->create();

        $response = $this->getResponse();

        $response->assertOk()
            ->assertJsonCount(2, 'resolution_types');

        $counts = collect($response->json('resolution_types'))
            ->pluck('count')
            ->sort()
            ->values()
            ->all();

        $this->assertEquals([2, 3], $counts);
    }

    /**
     * Test null resolution types are excluded
    */
    public function test_excludes_null_resolution_types()
    {
        $call = Call::factory()->create([
            'stage' => Call::STAGE_OPEN 
        ]);

        $resolution = ResolutionType::factory()->create();

        // valid
        WorkTask::factory() 
            ->for($call)
            ->create([
                'resolution_type_id' => $resolution->id,
            ]);

        // null (should be excluded)
       WorkTask::factory()
            ->for($call)
            ->withoutResolution()
            ->create();

        $this->getResponse()
            ->assertJsonCount(1)
            ->assertJsonFragment(['count' => 1]);

    }

    /**
     * Test empty result when no data falls within range
    */
    public function test_returns_empty_when_out_of_range()
    {
        $resolution = ResolutionType::factory()->create();
        $call = Call::factory()->create(['stage' => Call::STAGE_OPEN]);

        WorkTask::factory()->count(3)->create([
            'call_id' => $call->id,
            'resolution_type_id' => $resolution->id,
            'created_at' => Carbon::parse('1990-01-01'),
        ]);

        $this->getResponse('2024-01-01', '2024-12-31')
            ->assertExactJson([ 'resolution_types' => []]);
    }
}