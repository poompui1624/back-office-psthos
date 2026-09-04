<?php

namespace Database\Seeders;

use App\Models\SitePage;
use Illuminate\Database\Seeder;

/**
 * Creates the three pages the homepage places, so an editor finds them ready
 * to fill in rather than having to know which keys the layout expects.
 */
class SitePageSeeder extends Seeder
{
    public function run(): void
    {
        $starters = [
            'history' => 'ประวัติความเป็นมาของโรงพยาบาล',
            'vision' => 'วิสัยทัศน์ พันธกิจ และค่านิยม',
            'structure' => 'โครงสร้างผู้บริหารและการบริหารงาน',
        ];

        foreach (SitePage::KEYS as $key => $title) {
            SitePage::firstOrCreate(
                ['key' => $key],
                [
                    'title' => $title,
                    'body' => null,
                    'is_active' => true,
                ]
            );
        }

        $this->command?->info('Site pages ready: '.implode(', ', array_keys($starters)).'.');
    }
}
