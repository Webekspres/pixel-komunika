---
name: livewire-blade-ui
description: Guides Blade, Livewire, Flux, and Lucide UI work in this repo. Use when editing storefront/admin views, Livewire components, navigation, dropdowns, forms, or shared UI components.
disable-model-invocation: true
---

# Livewire Blade UI

## Use This When

- Editing `resources/views/**`
- Editing `app/Livewire/**`
- Changing storefront/admin layouts, dropdowns, filters, tables, forms, or empty states

## Repo Pattern

- UI is Blade + Livewire, not SPA.
- Reuse shared components in `resources/views/components/**` before adding inline markup.
- Keep admin and storefront visual language distinct, but reuse primitives where possible.
- Favor `wire:navigate` patterns already used in the repo.

## Lucide Rules

- Use `<x-icon>` for project-owned icons.
- Only use valid Lucide names.
- If adding a new icon name, ensure the JS Lucide registry supports it.
- Avoid leaving icon-sized empty space; icon + label rows should use explicit alignment like `justify-start`, `gap-*`, and `shrink-0` where needed.

## Interaction Guide

- Dropdown/modal polish: verify alignment, hover states, and auth-role branches together.
- Shared nav changes: check desktop and mobile variants in the same edit.
- Form updates: keep label, helper text, validation message, and spacing consistent with existing Flux usage.

## Validate

- Check the affected Blade component plus every screen that reuses it.
- Build assets when JS or icon behavior changes.
- Watch for duplicated menu items caused by auth-role redirects.
