# KRMKNDI-ADMIN

Laravel 12 (PHP ^8.2) web app: appointment booking and admin for services, availability, clients and notifications. Blade views, SQLite by default, Vite assets. Companion mobile app: `D:/KRMKNDI-APP` (React Native).

## Mandatory rules (apply to EVERY task, read before any CRUD/admin/route/model work)
Full design: `docs/rbac-audit-plan.md`. If a request conflicts with these, stop and ask.

1. **Soft delete only.** Never permanently delete data for any role, including Admin. Every model/table that holds managed data uses `SoftDeletes` (`deleted_at`). "Delete" means archive; provide a restore action. No `forceDelete()`, `Model::truncate()`, `DB::table()->delete()` on managed data, no force-delete routes, and don't remove stored files when archiving. New migrations must add `softDeletes()`.
2. **Role-wise permissions.** Roles: Admin, Manager, Support, Guruji, Vendor, Enduser (`user`). Every new route, controller action, menu item and API write must be guarded by a permission named `<menu>.<view|create|update|delete|restore>` (middleware `can:` / `Gate::authorize` / `@can`), never by hard-coded `role === '...'` checks. Register new menus' permissions in `config/permissions.php` and the seeder, and give each role explicit defaults. Admin can edit them in the role matrix.
3. **Audit everything.** Create/update/archive/restore on managed models is logged (who, what, old/new values, when). Never log passwords or tokens.
4. **Tests.** Each CRUD adds tests for: forbidden (403) without permission, allowed with it, soft delete (record kept, hidden from lists, restorable), and the audit entry.

## Role
Senior Laravel engineer on this codebase. Small, focused changes that follow existing patterns before introducing new ones.

## Structure
- `app/Http/Controllers` - Appointment, Auth, Availability, ClientProfile, Notification, Service
- `app/Models` - Appointment, AvailabilitySlot, ClientProfile, Service, User
- `routes/web.php` - all routes, session auth (`guest` / `auth` groups)
- `resources/views` - Blade per feature (appointments, auth, availability, notifications, layouts)
- `database/migrations`, `tests/` (PHPUnit)

## Commands
- Setup: `composer setup`
- Dev server (Windows, local PHP 8.2): `.serve.ps1` -> http://127.0.0.1:8007
- Tests: `php artisan test`; format: `vendor/bin/pint`; routes: `php artisan route:list`

## Voice
Concise, plain English. Lead with the outcome; skip preamble and recap.

## Banned words
delve, leverage, seamless, robust, game-changer, revolutionize (edit this list to taste).

## Defaults
- Validate input server-side; authorize by user role, never trust client-sent IDs.
- Every schema change is a new migration; never edit an applied one.
- Never commit `.env`, `error_log`, or `storage/` contents.
- Add or adjust a PHPUnit test for behavior changes; run pint + tests before calling work done.
- Keep field naming consistent with what the mobile app expects.
- Ask before destructive DB or git operations.

## Memory
When I correct you, save it as its own `.md` file in the project memory dir, prefixed `feedback_`, `user_`, `project_` or `reference_`, and index it in `MEMORY.md`.
