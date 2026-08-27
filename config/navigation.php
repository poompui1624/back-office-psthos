<?php

/*
|--------------------------------------------------------------------------
| Sidebar navigation
|--------------------------------------------------------------------------
|
| Each item names a route, the pattern that marks it active, the permission
| that reveals it, and its icon. A null permission means "any signed-in user".
| Items whose route does not exist are skipped, so partially deployed modules
| do not break the shell.
|
*/

return [
    [
        'label' => 'ของฉัน',
        'items' => [
            ['label' => 'ภาพรวมของฉัน', 'route' => 'me.index', 'active' => 'me.index', 'permission' => null, 'icon' => 'user'],
            ['label' => 'ใบลาของฉัน', 'route' => 'me.leaves', 'active' => 'me.leaves', 'permission' => 'leave.view.own', 'icon' => 'calendar'],
            ['label' => 'เวรของฉัน', 'route' => 'me.duties', 'active' => 'me.duties', 'permission' => 'duty.view.own', 'icon' => 'clock'],
            ['label' => 'ลงเวลาของฉัน', 'route' => 'me.attendance', 'active' => 'me.attendance', 'permission' => 'attendance.view.own', 'icon' => 'device'],
            ['label' => 'สลิปของฉัน', 'route' => 'me.payslips', 'active' => 'me.payslips', 'permission' => 'payslip.view.own', 'icon' => 'money'],
            ['label' => 'แจ้งซ่อมของฉัน', 'route' => 'me.repairs', 'active' => 'me.repairs', 'permission' => 'repair.view.own', 'icon' => 'wrench'],
            ['label' => 'จองห้องของฉัน', 'route' => 'me.meetings', 'active' => 'me.meetings', 'permission' => 'meeting.view.own', 'icon' => 'building'],
        ],
    ],

    [
        'label' => 'ภาพรวม',
        'items' => [
            ['label' => 'Dashboard', 'route' => 'dashboard', 'active' => 'dashboard', 'permission' => 'dashboard.view', 'icon' => 'dashboard'],
            ['label' => 'รายการอนุมัติ', 'route' => 'approvals.index', 'active' => 'approvals.*', 'permission' => 'approval.view', 'icon' => 'approvals'],
        ],
    ],

    [
        'label' => 'ข้อมูลหลัก',
        'items' => [
            ['label' => 'หน่วยงาน', 'route' => 'departments.index', 'active' => 'departments.*', 'permission' => 'department.view', 'icon' => 'building'],
            ['label' => 'ตำแหน่ง', 'route' => 'positions.index', 'active' => 'positions.*', 'permission' => 'position.view', 'icon' => 'clipboard'],
            ['label' => 'บุคลากร', 'route' => 'employees.index', 'active' => 'employees.*', 'permission' => 'employee.view', 'icon' => 'users'],
            ['label' => 'ผู้ใช้งานระบบ', 'route' => 'users.index', 'active' => 'users.*', 'permission' => 'user.view', 'icon' => 'shield'],
            ['label' => 'ตั้งค่าระบบ', 'route' => 'system-settings.index', 'active' => 'system-settings.*', 'permission' => 'setting.view', 'icon' => 'cog'],
            ['label' => 'Audit Log', 'route' => 'audit-logs.index', 'active' => 'audit-logs.*', 'permission' => 'audit.view', 'icon' => 'document'],
        ],
    ],
    [
        'label' => 'งานบุคคล',
        'items' => [
            ['label' => 'Dashboard การลา', 'route' => 'leave-requests.dashboard', 'active' => 'leave-requests.dashboard', 'permission' => 'leave.view', 'icon' => 'chart'],
            ['label' => 'รายการลา', 'route' => 'leave-requests.index', 'active' => 'leave-requests.index', 'permission' => 'leave.view', 'icon' => 'calendar'],
            ['label' => 'ปฏิทินลา', 'route' => 'leave-requests.calendar', 'active' => 'leave-requests.calendar', 'permission' => 'leave.view', 'icon' => 'calendar'],
            ['label' => 'ประเภทการลา', 'route' => 'leave-types.index', 'active' => 'leave-types.*', 'permission' => 'leave.view', 'icon' => 'clipboard'],
            ['label' => 'Dashboard เวลา', 'route' => 'attendance-summaries.dashboard', 'active' => 'attendance-summaries.dashboard', 'permission' => 'attendance.view', 'icon' => 'chart'],
            ['label' => 'เวลาเข้างาน', 'route' => 'attendance-logs.index', 'active' => 'attendance-logs.*', 'permission' => 'attendance.view', 'icon' => 'clock'],
            ['label' => 'สรุปเวลาทำงาน', 'route' => 'attendance-summaries.index', 'active' => 'attendance-summaries.index', 'permission' => 'attendance.view', 'icon' => 'clipboard'],
            ['label' => 'เครื่องสแกนนิ้ว', 'route' => 'attendance-devices.index', 'active' => 'attendance-devices.*', 'permission' => 'attendance.view', 'icon' => 'device'],
            ['label' => 'Dashboard ตารางเวร', 'route' => 'duty-schedules.index', 'active' => 'duty-schedules.index', 'permission' => 'duty.view', 'icon' => 'chart'],
            ['label' => 'ปฏิทินตารางเวร', 'route' => 'duty-schedules.calendar', 'active' => 'duty-schedules.calendar', 'permission' => 'duty.view', 'icon' => 'calendar'],
            ['label' => 'พิมพ์ตารางเวร', 'route' => 'duty-schedules.print', 'active' => 'duty-schedules.print', 'permission' => 'duty.view', 'icon' => 'document'],
            ['label' => 'สร้างเวรหลายรายการ', 'route' => 'duty-schedules.bulk-create', 'active' => 'duty-schedules.bulk-create', 'permission' => 'duty.create', 'icon' => 'clipboard'],
            ['label' => 'ประเภทเวร', 'route' => 'shift-types.index', 'active' => 'shift-types.*', 'permission' => 'duty.view', 'icon' => 'clock'],
        ],
    ],
    [
        'label' => 'บริการและทรัพย์สิน',
        'items' => [
            ['label' => 'จองห้องประชุม', 'route' => 'meeting-bookings.index', 'active' => 'meeting-bookings.*', 'permission' => 'meeting.view', 'icon' => 'building'],
            ['label' => 'ห้องประชุม', 'route' => 'meeting-rooms.index', 'active' => 'meeting-rooms.*', 'permission' => 'meeting.view', 'icon' => 'building'],
            ['label' => 'แจ้งซ่อม', 'route' => 'repair-requests.index', 'active' => 'repair-requests.index', 'permission' => 'repair.view', 'icon' => 'wrench'],
            ['label' => 'Repair Kanban', 'route' => 'repair-requests.kanban', 'active' => 'repair-requests.kanban', 'permission' => 'repair.view', 'icon' => 'clipboard'],
            ['label' => 'ทะเบียนพัสดุ', 'route' => 'assets.index', 'active' => 'assets.*', 'permission' => 'asset.view', 'icon' => 'box'],
            ['label' => 'หมวดหมู่พัสดุ', 'route' => 'asset-categories.index', 'active' => 'asset-categories.*', 'permission' => 'asset.view', 'icon' => 'clipboard'],
            ['label' => 'โอนย้ายพัสดุ', 'route' => 'asset-movements.index', 'active' => 'asset-movements.*', 'permission' => 'asset.view', 'icon' => 'swap'],
            ['label' => 'ทะเบียนคอมพิวเตอร์', 'route' => 'computers.index', 'active' => 'computers.*', 'permission' => 'computer.view', 'icon' => 'device'],
            ['label' => 'Computer Agents', 'route' => 'computer-agents.index', 'active' => 'computer-agents.*', 'permission' => 'computer.agent_manage', 'icon' => 'shield'],
            ['label' => 'Software Inventory', 'route' => 'software-inventory.index', 'active' => 'software-inventory.*', 'permission' => 'software.view', 'icon' => 'box'],
            ['label' => 'Software Products', 'route' => 'software-products.index', 'active' => 'software-products.*', 'permission' => 'software.view', 'icon' => 'clipboard'],
            ['label' => 'Software Licenses', 'route' => 'software-licenses.index', 'active' => 'software-licenses.*', 'permission' => 'software.view', 'icon' => 'key'],
        ],
    ],

    [
        'label' => 'การเงินและเอกสาร',
        'items' => [
            ['label' => 'เงินเดือน / สลิป', 'route' => 'payroll-periods.index', 'active' => 'payroll-periods.*', 'permission' => 'payroll.view', 'icon' => 'money'],
            ['label' => 'ตั้งค่าเงินเดือน', 'route' => 'salary-profiles.index', 'active' => 'salary-profiles.*', 'permission' => 'payroll.view', 'icon' => 'cog'],
            ['label' => 'เอกสาร ITA', 'route' => 'ita.documents.index', 'active' => 'ita.documents.*', 'permission' => 'ita.view', 'icon' => 'document'],
        ],
    ],
];
