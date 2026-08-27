# รายงานการปรับปรุงหลัง Review ระบบ

วันที่ดำเนินการ: 10 มิถุนายน 2569

## สรุปงานที่ดำเนินการ

ปรับปรุงตาม review โดยเน้นงานที่ควรทำก่อนและความเสี่ยงต่ำ ได้แก่ UX/UI layout หลัก, navigation, permission middleware, permission seeder, และ test เพิ่มเติม

## สิ่งที่แก้ไขแล้ว

### 1. ปรับ UX/UI Layout หลัก

- ปรับ `resources/views/components/layouts/app.blade.php` ใหม่ให้รองรับ mobile/tablet/desktop
- เพิ่ม mobile sidebar drawer โดยไม่เพิ่ม dependency ใหม่
- ปรับ sidebar ให้แบ่งหมวดชัดเจน:
  - ภาพรวม
  - ข้อมูลหลัก
  - งานบุคคล
  - บริการและทรัพย์สิน
  - การเงินและเอกสาร
- เพิ่มลิงก์ตรงไปหน้า Dashboard ตารางเวร
- ลดปัญหา active menu ทับกันของระบบการลา
- ปรับ topbar ให้กระชับขึ้นและใช้งานบนมือถือได้ดีขึ้น

### 2. ลด Query ใน Layout

- ลบ query `SystemSetting::where(...)` ออกจาก Blade layout
- ใช้ helper เดิม `hospital_name()` และ `hospital_logo_url()` แทน
- helper เดิมมี cache ผ่าน `Cache::rememberForever('system_settings_all')` อยู่แล้ว

### 3. เพิ่ม Permission Middleware ใน Route สำคัญ

เพิ่ม middleware ให้ route ที่เป็นหน้ารวม/รายงาน/เครื่องมือสำคัญ เช่น

- system settings
- audit logs
- approvals
- leave dashboard / leave calendar
- attendance import / dashboard / print / generate
- duty schedule calendar / bulk create

ยังคงให้ controller ตรวจสิทธิ์ราย action ต่อไป เพื่อไม่กระทบ create/update/delete ที่มี permission แยกกัน

### 4. ปรับ RolePermissionSeeder ให้ตรงกับระบบจริง

- ปรับ `database/seeders/RolePermissionSeeder.php` ให้สร้าง permission ชุดที่ controller และ view ใช้จริง เช่น
  - `leave.view`
  - `duty.create`
  - `setting.update`
  - `approval.approve`
  - `attachment.download`
- แก้ role หลักจาก `super-admin` เป็น `super_admin` ให้ตรงกับ logic ปัจจุบัน
- กำหนด permission ให้ role หลักใหม่ ได้แก่ `super_admin`, `admin`, `it`, `hr`, `staff`

### 5. ป้องกันการลบ Admin หลักผิดพลาด

- ปรับ `app/Http/Controllers/UserController.php`
- รองรับทั้ง role ชื่อ `super_admin` และ `super-admin`
- ป้องกันไม่ให้ลบบัญชี admin หลักคนสุดท้าย

### 6. เพิ่ม Automated Tests

เพิ่ม `tests/Feature/RolePermissionSeederTest.php`

ครอบคลุม:

- seeder สร้าง permission ที่ระบบใช้จริง
- seeder สร้าง role `super_admin`
- ไม่สามารถลบบัญชี protected admin คนสุดท้ายได้

## ไฟล์ที่แก้ไข/เพิ่ม

- `resources/views/components/layouts/app.blade.php`
- `routes/web.php`
- `database/seeders/RolePermissionSeeder.php`
- `app/Http/Controllers/UserController.php`
- `tests/Feature/RolePermissionSeederTest.php`
- `REVIEW_REPORT.md`

## ผลการทดสอบ

ผ่านทั้งหมด

```bash
vendor\bin\pint --dirty --format agent
php artisan test --compact tests\Feature\RolePermissionSeederTest.php
php artisan test --compact
npm run build
php artisan route:list --except-vendor
```

ผลล่าสุด:

- 37 tests ผ่านทั้งหมด
- 90 assertions ผ่านทั้งหมด
- Vite build ผ่าน
- Route list ทำงานได้ตามปกติ

## หมายเหตุ

- พยายามเปิดตรวจ UI ผ่าน Browser plugin แล้ว แต่เครื่องมือ browser ใน Codex ยังล้มเหลวจาก sandbox ฝั่ง Windows ด้วยข้อความ `windows sandbox failed: spawn setup refresh`
- จึงยืนยันด้วย Pest test, Vite build และ route list แทน

## สิ่งที่ยังควรทำต่อ

1. ปรับหน้ารายการเก่าให้ใช้ design system เดียวกัน เช่น table, filter, button, badge, empty state
2. แยก controller ใหญ่เป็น `FormRequest`, `Service/Action`, และ `Policy`
3. เพิ่ม test workflow สำคัญ:
   - approve/reject/cancel leave
   - upload/download/delete attachment
   - attendance import/generate
   - payroll generate/close
   - meeting approve/reject/cancel
4. เพิ่ม department-level access control สำหรับ HR/หัวหน้าแผนก
5. เพิ่ม audit log ให้ครบทุก write action สำคัญ
6. เพิ่ม rate limit ให้ action สำคัญ เช่น login, import, token regenerate, export
