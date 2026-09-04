<?php

namespace App\Console\Commands;

use App\Models\Computer;
use App\Models\ComputerSoftware;
use Illuminate\Console\Command;

/**
 * Fills computer_software from the snapshots already on record.
 *
 * The inventory page reads that table, so without this it stays empty until
 * every machine happens to report again — which for a daily agent means up to
 * a day of a blank page after deploying.
 */
class BackfillComputerSoftware extends Command
{
    protected $signature = 'software-inventory:backfill
                            {--fresh : Clear existing rows before filling}';

    protected $description = 'Fill computer_software from each machine\'s most recent snapshot';

    public function handle(): int
    {
        if ($this->option('fresh')) {
            $this->warn('Clearing computer_software.');
            ComputerSoftware::query()->delete();
        }

        $computers = Computer::query()->get();

        if ($computers->isEmpty()) {
            $this->info('No computers on record.');

            return self::SUCCESS;
        }

        $written = 0;
        $skipped = 0;

        $bar = $this->output->createProgressBar($computers->count());
        $bar->start();

        foreach ($computers as $computer) {
            // The most recent snapshot that actually carries a list, rather
            // than simply the most recent: a machine whose last report had no
            // software still has a last known good one worth filling from.
            $snapshot = $computer->snapshots()
                ->whereNotNull('installed_software')
                ->latest('reported_at')
                ->first();

            if (! $snapshot || ComputerSoftware::asEntryList($snapshot->installed_software) === []) {
                $skipped++;
                $bar->advance();

                continue;
            }

            $written += $this->fill($computer, $snapshot->installed_software, $snapshot->reported_at);

            // Snapshots taken before the hash column existed have none, so fill
            // it in now; otherwise the next report would look like a change.
            if (! $snapshot->software_hash) {
                $snapshot->update([
                    'software_hash' => ComputerSoftware::fingerprint($snapshot->installed_software),
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Wrote {$written} rows across ".($computers->count() - $skipped).' computers.');

        if ($skipped > 0) {
            $this->warn("{$skipped} computers had no usable snapshot and were skipped.");
        }

        return self::SUCCESS;
    }

    /**
     * @param  array<int, array<string, mixed>>  $installedSoftware
     */
    private function fill(Computer $computer, array $installedSoftware, $reportedAt): int
    {
        $seenAt = $reportedAt ?? now();
        $rows = [];

        foreach (ComputerSoftware::asEntryList($installedSoftware) as $item) {
            $name = trim((string) ($item['name'] ?? ''));

            if ($name === '') {
                continue;
            }

            $version = trim((string) ($item['version'] ?? ''));
            $normalized = ComputerSoftware::normalizeName($name);

            // Keyed so a machine listing the same package twice does not make
            // the upsert fail on a duplicate inside its own batch.
            $rows[$normalized.'|'.$version] = [
                'computer_id' => $computer->id,
                'name' => $name,
                'normalized_name' => $normalized,
                // Empty rather than null: the unique key includes this column,
                // and NULL never equals NULL, so nulls would not deduplicate.
                'version' => $version,
                'publisher' => trim((string) ($item['publisher'] ?? '')) ?: null,
                'is_component' => ComputerSoftware::looksLikeComponent($name),
                'first_seen_at' => $seenAt,
                'last_seen_at' => $seenAt,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if ($rows === []) {
            return 0;
        }

        ComputerSoftware::upsert(
            array_values($rows),
            ['computer_id', 'normalized_name', 'version'],
            ['name', 'publisher', 'is_component', 'last_seen_at', 'updated_at']
        );

        return count($rows);
    }
}
