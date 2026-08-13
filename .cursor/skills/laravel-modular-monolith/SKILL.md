---
name: laravel-modular-monolith
description: Guides Laravel modular-monolith work in this repo. Use when changing controllers, services, domains, models, middleware, config, migrations, or order/catalog/customer flows.
disable-model-invocation: true
---

# Laravel Modular Monolith

## Use This When

- Editing backend Laravel code in `app/`
- Touching `app/Services`, `app/Domains`, `app/Models`, controllers, middleware, migrations, or config
- Changing customer, catalog, cart, checkout, order, invoice, payment, or admin flows

## Repo Pattern

- Keep controllers thin. Put business rules in services or domain helpers.
- Reuse existing models, service methods, and constants before adding new code.
- Prefer extending current flows over adding parallel abstractions.
- Keep route names and middleware explicit.

## Decision Guide

1. Route or request entrypoint changes:
   - Check controller, middleware, request validation, and redirects together.
2. Business rule changes:
   - Prefer `app/Services` first.
   - Use `app/Domains` for focused calculation/import logic already modeled there.
3. Data shape changes:
   - Add/update migrations, casts, fillable fields, and tests in the same pass.
4. Access changes:
   - Reuse role/customer-status helpers and existing middleware aliases.

## Expectations

- Use transactions for multi-write flows like order, invoice, stock, and payment transitions.
- Preserve repo naming and constants rather than introducing new enums or layers unless necessary.
- Update config-backed behavior through `config/*.php` plus `.env.example` when appropriate.

## Validate

- Run focused tests first, then broader relevant tests.
- Re-check route behavior for guest, customer, active customer, and admin where applicable.
- After material code changes, update Graphify.
