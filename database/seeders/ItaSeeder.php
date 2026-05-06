<?php

namespace Database\Seeders;

use App\Models\ItaFiscalYear;
use App\Models\ItaMoitSubTopic;
use App\Models\ItaMoitTopic;
use Illuminate\Database\Seeder;

class ItaSeeder extends Seeder
{
    public function run(): void
    {
        $year = ItaFiscalYear::firstOrCreate(
            ['year' => 2569],
            [
                'name' => 'ปีงบประมาณ 2569',
                'is_active' => true,
            ]
        );

        $moit1 = ItaMoitTopic::updateOrCreate(
            [
                'fiscal_year_id' => $year->id,
                'code' => 'MOIT 1',
            ],
            [
                'indicator_no' => 1,
                'indicator_title' => 'การเปิดเผยข้อมูล',
                'title' => 'หน่วยงานมีการวางระบบโดยการกำหนดมาตรการการเผยแพร่ข้อมูลต่อสาธารณะ ผ่านเว็บไซต์ของหน่วยงาน',
                'description' => null,
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        $moit1SubTopics = [
            [
                'code' => '1',
                'title' => 'คำสั่ง / ประกาศ ที่ระบุกรอบแนวทาง (ของปีงบประมาณ พ.ศ. 2569)',
                'sort_order' => 1,
            ],
            [
                'code' => '1.1',
                'title' => 'มีบันทึกข้อความ ที่ผู้บริหารลงนามในคำสั่ง / ประกาศ และมีการขออนุญาต นำไปเผยแพร่บนเว็บไซต์ของหน่วยงาน',
                'sort_order' => 2,
            ],
            [
                'code' => '1.2',
                'title' => 'มีคำสั่ง / ประกาศ โดยผู้บริหารสูงสุดของหน่วยงาน เป็นไปตามข้อ 1. (รายละเอียดข้อมูลประกอบข้อคำถาม)',
                'sort_order' => 3,
            ],
            [
                'code' => '1.3',
                'title' => 'มีกรอบแนวทางการเผยแพร่ข้อมูลต่อสาธารณะผ่านเว็บไซต์ของหน่วยงาน รายละเอียดเนื้อหาในข้อมูลประกอบข้อคำถามข้อ 2. (ข้อ 2.1 มีลักษณะ / ประเภทข้อมูลที่หน่วยงานต้องเผยแพร่ต่อสาธารณะ และข้อ 2.2 มีการระบุวิธีการ ขั้นตอนการดำเนินงาน ระบุเวลาการดำเนินการและผู้มีหน้าที่รับผิดชอบในการเผยแพร่ข้อมูลต่อสาธารณะอย่างชัดเจน)',
                'sort_order' => 4,
            ],
            [
                'code' => '1.4',
                'title' => 'มีแบบฟอร์มการเผยแพร่ข้อมูลต่อสาธารณะ',
                'sort_order' => 5,
            ],
            [
                'code' => '2',
                'title' => 'รายงานผลการกำกับติดตามการเผยแพร่ข้อมูลต่อสาธารณะผ่านเว็บไซต์ของหน่วยงานในปีที่ผ่านมา (ของปีงบประมาณ พ.ศ. 2568)',
                'sort_order' => 6,
            ],
            [
                'code' => '2.1',
                'title' => 'มีบันทึกข้อความ ที่ผู้บริหารลงนามรับทราบรายงานฯ และมีการขออนุญาต นำไปเผยแพร่บนเว็บไซต์ของหน่วยงานในปีที่ผ่านมา',
                'sort_order' => 7,
            ],
            [
                'code' => '2.2',
                'title' => 'มีรายงานผลการกำกับติดตามการเผยแพร่ข้อมูลต่อสาธารณะผ่านเว็บไซต์ ของหน่วยงานในปีที่ผ่านมา',
                'sort_order' => 8,
            ],
            [
                'code' => '2.3',
                'title' => 'มีแบบฟอร์มการเผยแพร่ข้อมูลต่อสาธารณะผ่านเว็บไซต์ของหน่วยงานในปีที่ผ่านมา',
                'sort_order' => 9,
            ],
        ];

        foreach ($moit1SubTopics as $item) {
            ItaMoitSubTopic::updateOrCreate(
                [
                    'main_topic_id' => $moit1->id,
                    'code' => $item['code'],
                ],
                [
                    'fiscal_year_id' => $year->id,
                    'title' => $item['title'],
                    'description' => null,
                    'sort_order' => $item['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
