# Plan: User CRUD + Role/Permission management + Audit trail

## Where we are today
- `users.role` is a plain string: `admin | guruji | vendor | user` (`App\Models\User`, helpers `isAdmin()` etc.).
- Access control is hard-coded: ~10 `Gate::define(...)` in `AppServiceProvider` (all `isAdmin()` checks) and `role === 'admin'` checks in views/controllers (`layouts/app.blade.php`, `AppointmentController`, `AuthController`).
- Public `/register` lets anyone pick the `admin` role (**security hole, remove**).
- No user-management screens, no audit/history anywhere.
- Admin areas: `admin/location/*`, `admin/store/*` (categories, products, inventory, matajis, vendors), gurus, donation-categories, services, appointments, clients.

## Deletion rule (all roles)
**Soft delete only. No role, including Admin, can permanently delete anything.**
- Add `SoftDeletes` to every audited/managed model (users, products already has it, categories, inventory, vendors, matajis, services, gurus, donation categories, appointments, orders, locations) via migrations adding `deleted_at`.
- The `delete` permission means "archive"; UI label "Archive/Delete" with a restore action (`<menu>.restore` permission, default Admin + Manager).
- Remove/never register `forceDelete` routes; policies return false for `forceDelete`; image files are kept on disk when a record is soft-deleted (current `ServiceController::destroy` deletes files, change this).
- Archived records are hidden from lists by default, with an "Archived" filter. Archive/restore are logged in the audit trail.

## Goals
1. Admin can create / list / edit / delete users of any type.
2. Roles are data, with admin-editable permissions per menu: **access (route), list, create, edit, delete**.
3. Admin can also override permissions for a single user.
4. Every create/update/delete (products, categories, users, etc.) records **who, what, when, old -> new values**, viewable by admin.

## Design decisions
- Use `spatie/laravel-permission` for roles/permissions (standard, well tested, plays with Gates/`@can`) and `spatie/laravel-activitylog` for the audit trail. Alternative is hand-rolled tables; more code and more risk, so not recommended.
- Keep `users.role` for now as the "primary type" (keeps the mobile app and existing checks working); Spatie roles become the source of truth for permissions. Migrate off `users.role` checks gradually.
- Permission naming: `<menu>.<action>`, e.g. `products.view`, `products.create`, `products.update`, `products.delete`. `view` covers menu visibility + route access + listing.
- Menus covered: users, roles, products, categories, inventory, vendors, matajis, services, appointments, availability, clients, gurus, donation-categories, donations, locations (country/state/district/city/pincode), notifications, audit-log.
- Safety: `admin` role always has all permissions (Gate `before`); cannot delete or demote the last admin or yourself.

## Roles and default permissions (admin can edit all of this later)
| Role | Default access |
|---|---|
| Admin | Everything, incl. users, roles, audit log. Cannot be restricted. |
| Manager | View/create/edit on store (products, categories, inventory, vendors, matajis), services, appointments, gurus, donations, clients; delete only products/categories; view audit log; no users/roles management. |
| Support | View-only on most menus; edit appointments, clients and notifications; no delete; no store pricing/inventory edits. |
| Guruji | Own profile, donations, appointments/availability, **plus Mataji saree sales and purchases for customers** (`mataji-sales.*`, `mataji-orders.*`, view products/matajis/clients). Only sees their own customers' orders. |
| Vendor | Own products and inventory only (row-level), once the vendor account is active. |
| Enduser | Own profile, own appointments, notifications. |

`users.role` allowed values become `admin|manager|support|guruji|vendor|user` (Enduser is stored as `user` so existing data and the mobile app keep working).

## Steps

### 1. Foundation
- `composer require spatie/laravel-permission spatie/laravel-activitylog`; publish + run migrations (SQLite/MySQL safe).
- Add `HasRoles` to `User`; add `status` (active/suspended) to `users` so access can be revoked without deleting.
- `PermissionSeeder`: create permissions from a single config map (`config/permissions.php`: menu => actions), seed the six roles (`admin`, `manager`, `support`, `guruji`, `vendor`, `user`/Enduser) with the default matrix above; backfill existing users into matching Spatie roles.

### 2. Enforcement
- Replace `Gate::define` admin checks with permission checks: `Gate::authorize('products.update')` / `->middleware('can:products.view')`.
- Add `can:` middleware on every admin route group in `routes/web.php` (list vs write routes split per action).
- Keep legacy gate names (`manage-store`, `manage-appointments`, ...) as thin wrappers during migration so nothing breaks.
- Sidebar in `layouts/app.blade.php`: show each menu item with `@can('<menu>.view')` instead of role comparisons.
- Remove `admin` option from public register (force `user`).

### 3. User CRUD (admin)
- `Admin\UserController` (resource): index (search, filter by role/status, paginate), create, store, edit, update, destroy (soft delete), plus suspend/activate.
- `UserRequest` form request: name, email unique, password (required on create, optional on edit), role, status, optional direct permissions.
- Views `resources/views/admin/users/{index,create,edit}.blade.php` following existing Blade style.
- Creating a `vendor`/`guruji` user also creates/links the `Vendor`/`Guru` profile (reuse logic in `VendorsController`).

### 3b. Mataji saree sales & purchase (new module, Guruji + staff)
Today `matajis` is only a temple/deity directory and `products` the saree catalogue; there is **no order/sale table**. Add:
- `mataji_orders` (guruji_id, customer user_id or customer name/phone, mataji_id, type `sale|purchase`, status `draft|confirmed|paid|delivered|cancelled`, totals, notes, soft deletes) and `mataji_order_items` (product_id, qty, unit price snapshot).
- On confirm: decrement/increment `inventory` via `inventory_transactions` (type sale/purchase) so stock stays consistent.
- `GurujiMatajiOrderController` (list own, create, edit while draft, confirm, cancel/archive), Blade views, permissions `mataji-orders.view|create|update|delete|restore`; Guruji is scoped to `guruji_id = auth id`, Admin/Manager/Support see all (per matrix).
- Audit-logged like everything else; tests for scoping, stock changes and cancel.

### 3c. App Content / Promotions (new CRUD section: banners, ads, announcements)
Admin-managed content shown inside the mobile app (home banners, promotion ads, festival offers, announcements). **Backend/admin panel first**; app screens come after (see Phase 2).

**Data model** (`promotions`, soft deletes, audited):
- `title`, `description` (text), `image` (stored on `public` disk, validated jpg/png/webp, max 2 MB) plus optional `gallery` images
- `type`: `banner | promotion | announcement | offer`
- `placement`: `home_top | home_middle | shop | pooja | popup` (where the app shows it)
- `cta_type` (`none | product | category | mataji | pooja | url`) + `cta_value` (target id/url)
- `starts_at`, `ends_at` (scheduling), `sort_order`, `status` (`draft | active | inactive`)
- `audience`: `all | user | guruji | vendor` (which app roles see it)
- `translations` JSON (title/description per supported language, same pattern as `Service`)
- `created_by`, `updated_by`, `view_count`/`click_count` (optional, phase 2)

**Admin panel** (`Admin\PromotionController`, views `resources/views/admin/promotions/*`):
- Menu item "App Content" in sidebar, shown with `@can('promotions.view')`
- List with search, filter by type/placement/status/active-now, drag/number ordering, thumbnail preview; Archived filter
- Create/edit form with image upload + live preview, multilingual description, schedule pickers, CTA picker
- Actions: activate/deactivate toggle, archive (soft delete), restore. **No permanent delete.**
- Permissions: `promotions.view|create|update|delete|restore` (Admin all; Manager all; Support view only; other roles none). Admin can change per role/user in the matrix.
- Every create/update/archive/restore/status change goes to the audit log with old/new values and image path changes.

**Public API for the app** (same style as existing `api/v1`):
- `GET /api/v1/promotions?placement=home_top&lang=hi` returns only `active` + within schedule + not archived + matching audience, ordered by `sort_order`; JSON `{success, data:[{id,title,description,image,type,placement,cta:{type,value},starts_at,ends_at}]}` with absolute image URLs.
- Cache short (e.g. 5 min), cleared on any promotion change.
- Optional `POST /api/v1/promotions/{id}/click` for analytics (phase 2).

**Tests**: permission 403/200 per role; image validation; scheduling/audience filtering in API; archived never returned; audit entry created.

## Build order (admin panel / backend first)
**Phase 1 - Backend (KRMKNDI-ADMIN), in this order**
1. Foundation: Spatie permission + activitylog, six roles, permission seeder, soft-delete columns
2. Enforcement: `can:` middleware, sidebar by permission, close admin-register hole
3. User CRUD
4. Role & permission matrix
5. App Content / Promotions CRUD + public API (3c)
6. Mataji saree sales/purchase module (3b)
7. Audit log viewer
8. Tests + `/check` after each step, each step its own commit/PR

**Phase 2 - Mobile app (KRMKNDI-APP), after backend is stable**
- Home banner carousel + promotion cards/popup fed by `GET /api/v1/promotions`, with image caching, language param, tap-through via CTA to the right screen, i18n and tests.

### 4. Role & permission management (admin)
- `Admin\RoleController`: list roles, create custom role, edit role -> **permission matrix** (rows = menus, columns = access/list/create/edit/delete checkboxes), delete role (blocked if users assigned).
- Per-user override tab on the user edit screen using the same matrix (direct permissions on top of role).
- Clear Spatie permission cache on every change.

### 5. Audit trail
- `LogsActivity` trait on audited models: User, Product, ProductCategory, Inventory/transactions, Vendor, Mataji, Service, Guru, DonationCategory, Appointment, location models. Log only dirty attributes, exclude password/tokens.
- Log also: login/logout, role and permission changes (who granted what to whom), user create/delete.
- `Admin\AuditLogController`: filterable table (user, action created/updated/deleted, model type, date range), detail view showing **old vs new** values; admin-only (`audit-log.view`). Entries are read-only; no edit/delete UI.

### 6. Tests (PHPUnit)
- Permission matrix: user without `products.delete` gets 403; with it, 200.
- User CRUD happy paths + validation; cannot delete last admin/self; register cannot create admin.
- Audit: updating a product writes an activity row with causer, old and new values; password never logged.
- Run `/check` (Pint + tests).

### 7. Rollout
- Back up DB; run migrations + seeder; verify existing admin retains full access; spot-check each role's sidebar.
- Mobile app (KRMKNDI-APP): no change needed unless its API starts honoring permissions (out of scope for now).

## Suggested order / size
1 -> 2 (small, high risk, do first with tests) -> 3 -> 4 -> 5 -> 6. Roughly 5 PRs: foundation, enforcement, user CRUD, role matrix, audit log.

## Open questions
- Mataji saree "sales and buy for customer": my reading is the Guruji sells sarees to a customer and buys (orders) sarees on a customer's behalf for a Mataji offering. Is that right, and does payment need to be recorded (cash/UPI/online)?
- Confirm the default Manager and Support permissions in the roles table.
- Should vendors keep seeing only their own products (row-level) in addition to the permission matrix? Plan assumes yes.
- OK to add the two Spatie packages?
