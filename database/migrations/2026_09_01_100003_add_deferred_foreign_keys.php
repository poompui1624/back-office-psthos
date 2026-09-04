<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Constraints that two create migrations used to declare inline.
     *
     * Both tables are created before the tables they point at, so MySQL
     * rejected the inline version and a fresh database could not be built —
     * `migrate:fresh` failed and so would a first deployment. The test suite
     * runs on SQLite, which does not enforce the ordering, so it stayed green
     * and the problem only appeared against a real MySQL server.
     *
     * @var array<int, array{table: string, column: string, references: string}>
     */
    private array $foreignKeys = [
        ['table' => 'leave_requests', 'column' => 'leave_type_id', 'references' => 'leave_types'],
        ['table' => 'duty_schedule_actions', 'column' => 'duty_schedule_id', 'references' => 'duty_schedules'],
    ];

    public function up(): void
    {
        foreach ($this->foreignKeys as $key) {
            if ($this->exists($key['table'], $key['column'])) {
                continue;
            }

            Schema::table($key['table'], function (Blueprint $table) use ($key) {
                $table->foreign($key['column'])
                    ->references('id')
                    ->on($key['references'])
                    ->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        foreach ($this->foreignKeys as $key) {
            if (! $this->exists($key['table'], $key['column'])) {
                continue;
            }

            Schema::table($key['table'], function (Blueprint $table) use ($key) {
                $table->dropForeign([$key['column']]);
            });
        }
    }

    /**
     * A database migrated before this file existed already has the constraint
     * from the original inline declaration, and adding it again would fail.
     */
    private function exists(string $table, string $column): bool
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return false;
        }

        return DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', DB::connection()->getDatabaseName())
            ->where('TABLE_NAME', $table)
            ->where('COLUMN_NAME', $column)
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->exists();
    }
};
