<?php

namespace Tests\Feature;

use App\Models\Column;
use App\Models\User;
use App\Services\ColumnService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ColumnServiceParallelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * This test ensures that concurrent calls to ColumnService::firstOrCreate
     * do not create duplicate rows when executed in parallel processes.
     */
    public function test_first_or_create_is_process_safe(): void
    {
        if (!function_exists('pcntl_fork')) {
            $this->markTestSkipped('pcntl extension is not available.');
        }
        // Use a file based SQLite database so that both processes share it
        $dbPath = database_path('parallel_test.sqlite');
        touch($dbPath);
        Config::set('database.connections.sqlite.database', $dbPath);
        DB::purge('sqlite');
        $this->artisan('migrate');

        try {
            $service = new ColumnService();
            $user = User::factory()->create();

            $pids = [];
            for ($i = 0; $i < 2; $i++) {
                $pid = pcntl_fork();
                if ($pid === 0) {
                    // Child process: reconnect and run the service call
                    DB::reconnect('sqlite');
                    $service->firstOrCreate(2025, 7, $user->id);
                    exit(0);
                }
                $pids[] = $pid;
            }

            // Wait for children
            foreach ($pids as $pid) {
                pcntl_waitpid($pid, $status);
            }

            $this->assertEquals(1, Column::count());
        } finally {
            Config::set('database.connections.sqlite.database', ':memory:');
            DB::purge('sqlite');
            @unlink($dbPath);
        }
    }
}