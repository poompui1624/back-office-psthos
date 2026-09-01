<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One installed package on one machine.
 *
 * @property string $name
 * @property string $normalized_name
 * @property string|null $version
 * @property string|null $publisher
 * @property bool $is_component
 */
class ComputerSoftware extends Model
{
    use HasFactory;

    protected $table = 'computer_software';

    protected $fillable = [
        'computer_id',
        'name',
        'normalized_name',
        'version',
        'publisher',
        'is_component',
        'first_seen_at',
        'last_seen_at',
    ];

    protected $casts = [
        'is_component' => 'boolean',
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    public function computer(): BelongsTo
    {
        return $this->belongsTo(Computer::class);
    }

    /**
     * The form used for grouping and searching.
     *
     * Lowercased with runs of whitespace collapsed, so the same product
     * reported with different spacing or casing groups as one.
     */
    public static function normalizeName(string $name): string
    {
        return mb_strtolower(trim(preg_replace('/\s+/u', ' ', $name)));
    }

    /**
     * Whether a package is a runtime, driver, or update rather than software
     * someone chose to install. Patterns live in config/software_inventory.php.
     */
    public static function looksLikeComponent(string $name): bool
    {
        foreach (config('software_inventory.component_patterns', []) as $pattern) {
            if (preg_match('/'.$pattern.'/iu', $name) === 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * A stable fingerprint of one machine's software list.
     *
     * Sorted before hashing so a reordered list — which the agent makes no
     * promise about — is not mistaken for a change.
     *
     * @param  array<int, array<string, mixed>>  $installedSoftware
     */
    public static function fingerprint(array $installedSoftware): string
    {
        $lines = [];

        foreach ($installedSoftware as $item) {
            $name = trim((string) ($item['name'] ?? ''));

            if ($name === '') {
                continue;
            }

            $lines[] = self::normalizeName($name)
                .'|'.mb_strtolower(trim((string) ($item['version'] ?? '')))
                .'|'.mb_strtolower(trim((string) ($item['publisher'] ?? '')));
        }

        sort($lines);

        return hash('sha256', implode("\n", $lines));
    }

    /**
     * Hide runtimes and drivers unless the caller asks for them.
     */
    public function scopeExcludingComponents(Builder $query, bool $includeComponents = false): Builder
    {
        if ($includeComponents) {
            return $query;
        }

        return $query->where('is_component', false);
    }

    /**
     * Limit to machines that have reported recently.
     *
     * Without this a machine that was decommissioned keeps contributing to
     * every count until someone deletes it from the register.
     */
    public function scopeOnActiveComputers(Builder $query, ?int $withinDays = null): Builder
    {
        if ($withinDays === null) {
            return $query;
        }

        return $query->whereHas(
            'computer',
            fn (Builder $computer) => $computer->where('last_seen_at', '>=', now()->subDays($withinDays))
        );
    }
}
