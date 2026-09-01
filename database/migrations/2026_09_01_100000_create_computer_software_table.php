<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The current software on each machine, one row per package.
     *
     * computer_snapshots keeps the history as a JSON blob, which cannot be
     * queried: the inventory page had to load every machine's blob and group
     * it in PHP on each request. This table is what that page reads instead.
     */
    public function up(): void
    {
        Schema::create('computer_software', function (Blueprint $table) {
            $table->id();

            $table->foreignId('computer_id')
                ->constrained('computers')
                ->cascadeOnDelete();

            $table->string('name');

            // Lowercased and whitespace-collapsed. Grouping and searching both
            // run on this so "Google Chrome" and "google  chrome" are one thing.
            $table->string('normalized_name');

            $table->string('version')->nullable();
            $table->string('publisher')->nullable();

            // Redistributables, runtimes, and drivers: about a third of a
            // typical machine's list, and not what anyone opens this page for.
            $table->boolean('is_component')->default(false);

            $table->timestamp('first_seen_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();

            $table->timestamps();

            // A machine reports the same package once, so this both prevents
            // duplicates and gives upsert something to match on.
            $table->unique(['computer_id', 'normalized_name', 'version'], 'computer_software_unique');

            $table->index('normalized_name');
            $table->index(['normalized_name', 'version']);
            $table->index('is_component');
            $table->index('publisher');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('computer_software');
    }
};
