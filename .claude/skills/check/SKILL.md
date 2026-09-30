---
name: check
description: Run the quality gate for KRMKNDI-ADMIN (Pint + PHPUnit) and report pass/fail.
---

1. Run `vendor/bin/pint --test`; if it fails, run `vendor/bin/pint` and note changed files.
2. Run `php artisan test`.
3. Report formatting status, tests passed/failed, and the first failure's file/line. Do not commit.
