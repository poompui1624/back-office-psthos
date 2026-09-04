<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DatabaseTableExport implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles, WithTitle
{
    /**
     * Columns that must never leave the system through an export,
     * whatever table they appear in.
     *
     * @var array<int, string>
     */
    public const DENIED_COLUMNS = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'api_token',
        'token',
        'agent_token',
        'citizen_id',
        'taxpayer_no',
        'social_security_no',
    ];

    /**
     * @param  array<int, string>|null  $columns  Explicit column allowlist. When null,
     *                                            every column except {@see self::DENIED_COLUMNS}.
     */
    public function __construct(
        protected string $table,
        protected string $title = 'Export',
        protected ?array $columns = null
    ) {}

    public function collection(): Collection
    {
        $columns = $this->resolveColumns();

        if ($columns === []) {
            return collect();
        }

        $query = DB::table($this->table)->select($columns);

        if (Schema::hasColumn($this->table, 'id')) {
            $query->orderBy('id');
        } elseif (Schema::hasColumn($this->table, 'created_at')) {
            $query->orderByDesc('created_at');
        }

        return $query->get()->map(function ($row) use ($columns) {
            return collect($columns)->map(function ($column) use ($row) {
                return $row->{$column} ?? null;
            })->toArray();
        });
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return $this->resolveColumns();
    }

    public function title(): string
    {
        return mb_substr($this->title, 0, 31);
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true],
            ],
        ];
    }

    /**
     * Resolve the columns to export: the caller's allowlist when given, otherwise
     * every real column. Denied columns are stripped in both cases so an explicit
     * allowlist can never re-open a sensitive column by mistake.
     *
     * @return array<int, string>
     */
    private function resolveColumns(): array
    {
        if (! Schema::hasTable($this->table)) {
            return [];
        }

        $existing = Schema::getColumnListing($this->table);

        $selected = $this->columns === null
            ? $existing
            : array_values(array_intersect($this->columns, $existing));

        return array_values(array_diff($selected, self::DENIED_COLUMNS));
    }
}
