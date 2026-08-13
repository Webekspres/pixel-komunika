---
name: tailwind-design-system
description: Guides Tailwind 4 styling decisions for this repo. Use when changing spacing, layout, cards, typography, storefront/admin styling, or shared visual components.
disable-model-invocation: true
---

# Tailwind Design System

## Use This When

- Editing Tailwind classes in Blade views
- Changing layout, spacing, typography, card styling, or responsive behavior
- Refactoring repeated UI patterns into shared components

## Repo Pattern

- Prefer existing utility combinations and component wrappers over inventing new style systems.
- Reuse the repo's storefront/admin split instead of blending both looks.
- Keep layouts readable on shared hosting-era app pages: boring and maintainable beats clever class soup.

## Styling Rules

- Change the smallest surface that fixes the issue.
- When spacing looks wrong, inspect parent flex/grid alignment before adding more padding or margins.
- Preserve semantic groupings:
  - headings and page headers
  - stat cards and section cards
  - filter bars and tables
  - product cards and commerce summaries

## Reuse First

- Check shared components in `resources/views/components/ui/`
- Check storefront wrappers in `resources/views/components/storefront/`
- Check app shell/layout components before patching individual pages

## Validate

- Compare desktop and mobile variants for nav, forms, cards, and tables.
- Watch for icon gaps, text centering drift, and duplicated spacing from nested wrappers.
