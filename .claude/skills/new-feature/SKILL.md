---
name: new-feature
description: Scaffold a feature in KRMKNDI-ADMIN (migration, model, controller, routes, Blade views, test). Args: feature name and fields.
---

1. Study an existing feature (Service: controller, model, routes, views) and mirror it.
2. Create migration + model, controller, routes inside the `auth` group, Blade views under `resources/views/<feature>`.
3. Add a PHPUnit feature test.
4. Run `/check` and summarize what was created.
