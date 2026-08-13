---
name: repo-workflow
description: Guides day-to-day agent workflow for this repo. Use when planning, implementing, validating, or handing off work in Pixel Komunika, especially for RTK, Graphify, build/test, and high-risk touchpoints.
disable-model-invocation: true
---

# Repo Workflow

## Use This When

- Starting a new task in this repo
- Deciding what to validate after a change
- Touching routes, config, env-backed behavior, icons, or commerce workflows

## Mandatory Repo Habits

- Use RTK for terminal commands in this repo.
- Use Graphify first for codebase orientation when the graph is available.
- Follow Ponytail: smallest correct diff, reuse-first, no speculative abstraction.

## Validation Defaults

- Backend logic: run focused `php artisan test`
- Frontend or JS/icon changes: run `npm run build`
- Formatting-sensitive PHP changes: consider `pint` if needed
- Significant code changes: update Graphify after edits

## Sensitive Areas

- `routes/web.php`, `routes/api.php`, `routes/console.php`
- `config/*.php` and `.env.example`
- shared Blade components and layouts
- pricing, tax, order, invoice, payment, and stock flows

## Handoff Checklist

- Say what changed in product terms, not just file terms.
- Mention anything not validated.
- Call out env or migration follow-up explicitly.
