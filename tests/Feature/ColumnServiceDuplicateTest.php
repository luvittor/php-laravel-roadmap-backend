<?php

namespace Tests\Feature;

use App\Services\ColumnService;
use App\Models\User;
use App\Models\Column;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ColumnServiceDuplicateTest extends TestCase
{
    use RefreshDatabase;

    public function test_first_or_create_handles_duplicate_insertion(): void
    {
        $service = new ColumnService();
        $user = User::factory()->create();

        $first = $service->firstOrCreate(2025, 7, $user->id);
        $this->assertNotNull($first);

        // Simulate a concurrent attempt that would violate the unique constraint
        $second = $service->firstOrCreate(2025, 7, $user->id);

        // The service should return the existing column
        $this->assertEquals($first->id, $second->id);
        $this->assertEquals(1, Column::count());
    }
}
