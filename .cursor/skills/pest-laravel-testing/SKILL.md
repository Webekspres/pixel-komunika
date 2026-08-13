---
name: pest-laravel-testing
description: Guides Pest-based testing for this Laravel repo. Use when adding or updating unit tests, feature tests, seeded scenarios, auth/access checks, or commerce workflow validation.
disable-model-invocation: true
---

# Pest Laravel Testing

## Use This When

- Adding or editing tests in `tests/`
- Verifying pricing, PPh 22, auth, approval, cart, checkout, order, payment, or admin flows
- Fixing regressions that need the smallest useful automated check

## Repo Pattern

- Prefer Pest syntax and existing test style in this repo.
- Choose the narrowest test that proves the rule:
  - unit for calculators and isolated helpers
  - feature for routes, middleware, redirects, DB writes, and workflow transitions
- Reuse existing factories and `SampleCatalogImporter` where commerce data is needed.

## Testing Rules

- Fix the root behavior, then add the smallest test that would have failed before.
- Do not add noisy tests that only restate implementation details.
- When behavior depends on auth state, cover the relevant role/status branch explicitly.
- When behavior depends on stock, invoice, or order transitions, assert database-facing outcomes.

## Common Checks

- Route access: guest vs customer vs active customer vs admin
- Pricing: retail/partai/grosir selection and PPh 22 totals
- Workflow: order creation, invoice snapshot, payment status, cancellation, stock restoration

## Validate

- Run focused tests during iteration.
- Run broader related suites when touching shared flows.
