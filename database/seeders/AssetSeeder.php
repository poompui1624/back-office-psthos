<?php

namespace Database\Seeders;

use App\Models\AssetCategory;
use Illuminate\Database\Seeder;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['code' => 'COMPUTER', 'name' => 'คอมพิวเตอร์และอุปกรณ์'],
            ['code' => 'MEDICAL', 'name' => 'เครื่องมือแพทย์'],
            ['code' => 'OFFICE', 'name' => 'ครุภัณฑ์สำนักงาน'],
            ['code' => 'VEHICLE', 'name' => 'ยานพาหนะ'],
            ['code' => 'NETWORK', 'name' => 'อุปกรณ์เครือข่าย'],
            ['code' => 'FURNITURE', 'name' => 'เฟอร์นิเจอร์'],
        ];

        foreach ($categories as $category) {
            AssetCategory::firstOrCreate(
                ['code' => $category['code']],
                [
                    'name' => $category['name'],
                    'is_active' => true,
                ]
            );
        }
    }
}
