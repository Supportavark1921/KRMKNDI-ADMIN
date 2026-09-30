# KRMKNDI-ADMIN

Laravel 12 (PHP ^8.2) web app: appointment booking and admin for services, availability, clients and notifications. Blade views, SQLite by default, Vite assets. Companion mobile app: `D:KRMKNDI-APP` (React Native).

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
