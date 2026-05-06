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

class DatabaseTableExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithTitle
{
    public function __construct(
        protected string $table,
        protected string $title = 'Export'
    ) {}

    public function collection(): Collection
    {
        if (! Schema::hasTable($this->table)) {
            return collect();
        }

        $columns = Schema::getColumnListing($this->table);

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

    public function headings(): array
    {
        if (! Schema::hasTable($this->table)) {
            return [];
        }

        return Schema::getColumnListing($this->table);
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
}
