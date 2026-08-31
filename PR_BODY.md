Six commits on top of `main`. The first carries in-progress work that was
already in the working tree; the rest are this session's.

| commit | what |
|---|---|
| `ec68910` | Personnel profiles, DashboardController extraction, duty/leave overviews |
| `46ca190` | Security fixes, testable foundation, department scoping |
| `102b7d7` | Staff self-service portal at `/me` |
| `ff5a46e` | Overtime as a real amount, roster rules, printable roster |
| `9581a16` | Dashboard and shell rebuilt on a shared design system |
| `33d6030` | Remaining dashboards scoped, payslip access fixed |

## Why

The app worked but three things blocked further work: permissions were global
(anyone with `leave.view` saw every ward's leave), there was one model factory
in the whole project so domain behaviour had no test coverage, and business
logic lived entirely in controllers.

## Bugs found and fixed along the way

- **Exports leaked credentials.** `DatabaseTableExport` dumped every column, so
  exporting `users` produced a spreadsheet containing `password` and
  `two_factor_secret`, and `employees` produced `citizen_id`.
- **Submitting leave returned a 500** on any install where `leave.approve` had
  not been seeded — the notify step used Spatie's permission scope, which
  throws rather than returning nobody. Three call sites had this shape.
- **Document numbers could collide.** Leave, repair, and meeting numbers used
  `count() + 1`, so deleting any record made the next request reuse a number a
  live row already held.
- **Approved leave was deducted as absence.** Taking leave correctly cost the
  employee money.
- **`staff` could submit but not read back.** The role held `leave.create` with
  no matching `.view`.
- **Anyone with `payroll.view` could open any payslip** by changing the id in
  the URL; staff could not open their own.

## Department scoping

Visibility is now limited to the viewer's own department unless they hold
`<prefix>.view.all`. `BaseModulePolicy::canSee()` is the single seam every
record-level check funnels through. A new `supervisor` role holds the module
permissions without `.view.all`, which is what scopes it.

An account not linked to an employee sees nothing rather than everything —
failing closed when the link is missing.

## Reviewer notes

- **This changes what existing users see.** Some accounts will see less than
  before, and any account without an `employee_id` will see nothing until that
  link is set.
- The dashboard theme uses no new dependency: donut, line, and bar charts are
  plain SVG built from the data in Blade.
- 43 nav items moved from the layout into `config/navigation.php`.
- The layout deliberately does not render flash messages — 36 pages already
  render their own.

## Deploy

```
php artisan migrate --force
php artisan db:seed --class=RolePermissionSeeder
npm run build
```

Then set `is_ot` and the OT multiplier on the shift types, and the hourly OT
rate on the salary profiles — shifts already named "OT" were flagged by the
migration, but they carry no rate yet.

## Still open

`APP_DEBUG=true` in the checked environment, rate limiting only on login, no
tests for 11 modules (attendance, computers, software licences, ITA,
attachments, system settings, asset movements), and the FormRequest sweep.

## Verification

229 tests, 510 assertions, all passing. Pint clean. Vite build clean. 213
routes resolve. The dashboard was checked in a browser: no console errors,
donut arc lengths sum to the circumference, both trend series plot 12 points.
