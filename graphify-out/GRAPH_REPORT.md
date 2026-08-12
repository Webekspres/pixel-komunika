# Graph Report - /home/developer/www/pixel-komunika  (2026-08-11)

## Corpus Check
- 2 files · ~109,036 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1055 nodes · 1425 edges · 129 communities (110 shown, 19 thin omitted)
- Extraction: 96% EXTRACTED · 4% INFERRED · 0% AMBIGUOUS · INFERRED: 51 edges (avg confidence: 0.81)
- Token cost: 0 input · 0 output

## Community Hubs (Navigation)
- Data Dictionary
- Software Requirements Specification (SRS)
- composer.json
- 3. Customer-facing Flows
- 15. Keputusan Terbuka
- Ringkasan Ruang Lingkup dan Persetujuan Pengembangan Website
- Functional Requirements Document (FRD)
- scripts
- Business Requirements Document (BRD)
- docs/README.md
- .import
- package.json
- Repository Agent Instructions
- ProductPrice
- PROGRESS
- User
- 7.3 Biteship API
- icon.blade.php
- AppServiceProvider
- ExampleTest
- ExampleTest
- 0001_01_01_000000_create_users_table.php
- 0001_01_01_000001_create_cache_table.php
- 0001_01_01_000002_create_jobs_table.php
- Domains/README.md
- 12. Non-Functional Requirements
- artisan
- ponytail.md
- 17. Development and Deployment
- config/app.php
- cache.php
- database.php
- filesystems.php
- logging.php
- mail.php
- queue.php
- services.php
- session.php
- index.php
- app.js
- home.blade.php
- Illuminate\Http\Request
- HealthTest.php
- Pest.php
- vite.config.js
- CustomerProfile
- DESIGN.md — Color Tokens & Font
- guest.blade.php
- 2. Prinsip Desain
- 3. Keputusan Arsitektur
- 5. Deployment Profile
- 2026_08_04_170000_create_catalog_pricing_inventory_tables.php
- 4. Technology Stack
- 9. Conceptual Data Model
- dashboard.blade.php
- customers/index.blade.php
- login.blade.php
- register.blade.php
- checkout/index.blade.php
- components/layouts/app.blade.php
- Rekomendasi pemakaian
- Frontend UI Architecture
- 1. UI Layer Architecture
- 2. Flux Adoption Matrix
- 3. Component Strategy
- 6. Animation Guidelines
- admin-page.blade.php
- app-page.blade.php
- auth-shell.blade.php
- filter-bar.blade.php
- page-header.blade.php
- section-card.blade.php
- Illuminate\Database\Eloquent\Model
- ProductIndex
- Checkout.php
- product-show.blade.php

## God Nodes (most connected - your core abstractions)
1. `CartService` - 31 edges
2. `15. Keputusan Terbuka` - 28 edges
3. `User` - 25 edges
4. `Software Requirements Specification (SRS)` - 23 edges
5. `Order` - 23 edges
6. `CustomerProfile` - 20 edges
7. `Frontend UI Architecture` - 19 edges
8. `Functional Requirements Document (FRD)` - 19 edges
9. `Product` - 19 edges
10. `Business Requirements Document (BRD)` - 18 edges

## Surprising Connections (you probably didn't know these)
- `Frontend UI Architecture` --references--> `Pixel Komunika Brand UI`  [EXTRACTED]
  docs/design/FRONTEND_UI_ARCHITECTURE.md → DESIGN.md
- `Pixel Komunika Repository Overview` --references--> `Pixel Komunika Brand UI`  [EXTRACTED]
  README.md → DESIGN.md
- `Always-Active Tooling` --references--> `Ponytail Lazy Senior Mode`  [EXTRACTED]
  AGENTS.md → .cursor/rules/ponytail.md
- `Software Requirements Specification` --conceptually_related_to--> `Frontend UI Architecture`  [INFERRED]
  docs/requirements/SRS.md → docs/design/FRONTEND_UI_ARCHITECTURE.md
- `DatabaseSeeder` --references--> `SampleCatalogImporter`  [EXTRACTED]
  database/seeders/DatabaseSeeder.php → app/Domains/SeedDataSupport/SampleCatalogImporter.php

## Import Cycles
- None detected.

## Hyperedges (group relationships)
- **POS and Website Product Data Boundary** — docs_pos_follow_up_pos_website_ownership, docs_pos_follow_up_website_product_enrichment, docs_pos_follow_up_shipping_dimensions, docs_progress_product_enrichment_ownership [EXTRACTED 1.00]
- **Implementation Reconciliation Gaps** — docs_progress_requirement_reconciliation, docs_progress_price_priority_mismatch, docs_progress_pph22_formula_mismatch, docs_progress_phase_3_requirement_gaps [EXTRACTED 1.00]
- **POS Integration Contract Package** — docs_pos_follow_up_master_read_endpoints, docs_pos_follow_up_identifier_upsert_rules, docs_pos_follow_up_sync_behavior, docs_pos_follow_up_sales_return_contract, docs_pos_follow_up_contract_deliverables [EXTRACTED 1.00]
- **Verified Customer End-to-End Sales Pipeline** — docs_design_user_flows_access_and_registration, docs_design_user_flows_cart_pricing_checkout, docs_design_user_flows_order_invoice_pos_reporting, docs_design_user_flows_payment_fulfillment [EXTRACTED 1.00]
- **Frontend Five-Layer Architecture** — docs_design_frontend_ui_architecture_tailwind, docs_design_frontend_ui_architecture_flux, docs_design_frontend_ui_architecture_shared, docs_design_frontend_ui_architecture_business, docs_design_frontend_ui_architecture_pages [EXTRACTED 1.00]
- **Pixel Komunika Visual Brand System** — public_assets_brand_logo_pixel_komunika_wordmark, public_assets_brand_logo_customer_service_shopping_mascot, public_assets_brand_logo_ecommerce_shopping_cart_symbol, public_assets_brand_logo_black_yellow_visual_identity [EXTRACTED 1.00]
- **Friendly Support and Gaming Brand Expression** — public_assets_mascot_maskot_base_friendly_headset_mascot, public_assets_mascot_maskot_base_customer_support_headset, public_assets_mascot_maskot_base_game_controller, public_assets_mascot_maskot_base_playful_technology_brand_persona [INFERRED 0.75]

## Communities (129 total, 19 thin omitted)

### Community 0 - "Data Dictionary"
Cohesion: 0.05
Nodes (22): AccountController, AddressController, CustomerReviewController, AuthenticatedSessionController, RegisteredUserController, Controller, CustomerOrders, OrderDetail (+14 more)

### Community 1 - "Software Requirements Specification (SRS)"
Cohesion: 0.04
Nodes (47): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+39 more)

### Community 2 - "composer.json"
Cohesion: 0.05
Nodes (44): 10. Constraint dan Index Minimum, 11. Delete dan Retention, 1. Konvensi, 2.1 `roles`, 2.2 `users`, 2.3 `customer_profiles`, 2.4 `addresses`, 2. Identity dan Customer (+36 more)

### Community 3 - "3. Customer-facing Flows"
Cohesion: 0.08
Nodes (31): Color tokens, DESIGN.md — Color Tokens & Font, Font, Neutrals (UI), Open, Product context, Semantic, Tailwind `@theme` (+23 more)

### Community 4 - "15. Keputusan Terbuka"
Cohesion: 0.06
Nodes (38): Modular Monolith Domain Boundaries, Domain Modules, Version One Acceptance Criteria, Website Invoice and Delivery Channels, Client Decisions Still Required, Order Fulfillment Lifecycle, Website and POS Data Exchange, Partai Grosir and PPh 22 Rules (+30 more)

### Community 5 - "Ringkasan Ruang Lingkup dan Persetujuan Pengembangan Website"
Cohesion: 0.05
Nodes (38): 10. Perubahan Setelah Persetujuan, 11. Persetujuan, 1.1 Cara Memberikan Tanggapan melalui Grup WhatsApp, 1. Tujuan Dokumen, 2. Ringkasan Website, 3. Fitur yang Termasuk dalam Versi Pertama, 4. Keputusan yang Sudah Disepakati, 5.1 Pendaftaran dan Persetujuan Pelanggan (+30 more)

### Community 6 - "Functional Requirements Document (FRD)"
Cohesion: 0.07
Nodes (33): 1. Master data read endpoints, 2. Identifier dan aturan upsert, 3. Sync behavior, 4. Sales/return reporting contract, POS Contract Deliverables, Deliverable follow-up, POS Identifier and Upsert Rules, POS Master Data Read Endpoints (+25 more)

### Community 7 - "scripts"
Cohesion: 0.09
Nodes (6): Brand, Category, Order, Product, Illuminate\Database\Eloquent\Relations\HasMany, Illuminate\Database\Eloquent\Relations\HasOne

### Community 8 - "Business Requirements Document (BRD)"
Cohesion: 0.11
Nodes (7): AdminOrders, PaymentProof, User, PaymentService, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable

### Community 9 - "docs/README.md"
Cohesion: 0.07
Nodes (28): 1.1 Notasi Flowchart, 1. Konvensi, 2. Indeks Flow, 3. Customer-facing Flows, 4. Back-office dan Integration Flows, 5. Traceability, 6. Open Decisions yang Membatasi Flow, Pixel Komunika E-Commerce (+20 more)

### Community 10 - ".import"
Cohesion: 0.07
Nodes (28): 15.1 Klarifikasi Klien 28 Juli 2026, 15.2 MVP Baseline dan Stage Gates, 15.3 Rencana Delivery 45 Hari Kerja, 15.4 Checklist Pra-Pengembangan, 15. Keputusan Terbuka, OPN-001, OPN-002, OPN-003 (+20 more)

### Community 11 - "package.json"
Cohesion: 0.13
Nodes (7): CartItem, Invoice, OrderItem, OrderReturn, ProductEnrichment, Illuminate\Database\Eloquent\Model, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 12 - "Repository Agent Instructions"
Cohesion: 0.07
Nodes (27): 10. Modul Pembayaran Manual, 11. Modul Laporan, 12. Audit dan Monitoring, 13. Modul Notifikasi Admin, 14. Modul Reseller, 15. Penanganan Error Fungsional, 16. Traceability BRD ke FRD, 17. Acceptance Gate (+19 more)

### Community 13 - "ProductPrice"
Cohesion: 0.08
Nodes (26): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+18 more)

### Community 14 - "PROGRESS"
Cohesion: 0.08
Nodes (26): 10. Business Rules, 11.1 Registrasi dan Persetujuan, 11.2 Transaksi dan Pembayaran, 11.3 Sinkronisasi POS, 11. Proses Bisnis Utama, 12. Ukuran Keberhasilan, 13. Asumsi dan Dependensi, 14. Risiko Bisnis (+18 more)

### Community 15 - "User"
Cohesion: 0.13
Nodes (10): ImportSampleCatalog, SampleCatalogImporter, Role, UserFactory, DatabaseSeeder, Illuminate\Console\Command, Illuminate\Database\Console\Seeds\WithoutModelEvents, Illuminate\Database\Eloquent\Factories\Factory (+2 more)

### Community 16 - "7.3 Biteship API"
Cohesion: 0.09
Nodes (21): Ponytail Lazy Senior Mode, Minimal Runnable Check, Root-Cause Bug Fix, 1. Rust Token Killer (RTK), 2. Graphify, 3. Ponytail Ultra, Always-Active Tooling, Aturan penulisan (+13 more)

### Community 17 - "icon.blade.php"
Cohesion: 0.10
Nodes (20): concurrently, laravel-vite-plugin, lucide, dependencies, lucide, devDependencies, concurrently, laravel-vite-plugin (+12 more)

### Community 19 - "ExampleTest"
Cohesion: 0.16
Nodes (18): Pixel Komunika Data Dictionary, Core Commerce Entity Relations, MVP Entity Relationship Model, Historical Transaction Snapshots, MVP User Flow Catalog UF-01 to UF-19, Pixel Komunika Business Requirements, E-Commerce Business Rules, Business Scope and Objectives (+10 more)

### Community 20 - "ExampleTest"
Cohesion: 0.12
Nodes (15): 2026-08-07, Blocker dan dependency eksternal, Cara pakai, Fase 1 — Identity & access, Fase 2 — Catalog, pricing, stock, dan data contoh POS, Fase 3 — Cart, checkout, shipping, order, invoice, Fase 4 — Payment, pembatalan/retur, admin workflow, Fase 5 — Reporting, audit, sync resilience, security (+7 more)

### Community 21 - "0001_01_01_000000_create_users_table.php"
Cohesion: 0.12
Nodes (15): 1. Guest registrasi, 2. Pending customer dibatasi, 3. Admin review customer, 4. Active customer mendapat akses, 5. Address book customer, 6. Import sample catalog, Sprint 1 Acceptance Gate, Acceptance Sprint 1 (+7 more)

### Community 22 - "0001_01_01_000001_create_cache_table.php"
Cohesion: 0.13
Nodes (15): 10. Transaction and Concurrency, 11. Queue and Scheduler, 13. File and Media Handling, 14. Caching and Traffic Spike Protection, 15. Logging, Audit, and Observability, 16. Backup and Disaster Recovery, 18. Testing Requirements, 19. Traceability FRD ke SRS (+7 more)

### Community 23 - "0001_01_01_000002_create_jobs_table.php"
Cohesion: 0.20
Nodes (4): PriceCalculator, CategoryTaxRule, ProductPrice, Illuminate\Support\Collection

### Community 25 - "12. Non-Functional Requirements"
Cohesion: 0.20
Nodes (3): Address, InventoryLedger, InventorySnapshot

### Community 26 - "artisan"
Cohesion: 0.17
Nodes (12): 6. Animation Guidelines, Button interaction, Cart drawer, Cocok dianimasikan, Hero reveal, Loading state, Product hover, Rekomendasi pemakaian (+4 more)

### Community 27 - "ponytail.md"
Cohesion: 0.20
Nodes (10): A — Baseline (sudah / native), B — Ambil saat storefront / Identity, Brand Color Tokens and Typography, Pixel Komunika Brand UI, C — Nanti (trigger konkret), D — Skip, Frontend toolkit, Verified-Customer E-Commerce Context (+2 more)

### Community 28 - "17. Development and Deployment"
Cohesion: 0.20
Nodes (10): 7. Performance Considerations, 8. Development Rules, 9. Internal Rollout Baseline, Flux Internal Custom Storefront Hybrid Commerce, Adoption Recommendation, Best practices, Frontend UI Architecture, Five-Layer UI Architecture (+2 more)

### Community 29 - "config/app.php"
Cohesion: 0.20
Nodes (9): Laravel Livewire Application Stack, Branch, Repository Branch Flow, Catatan, Development setup, Dokumentasi, Pixel Komunika, Pixel Komunika Repository Overview (+1 more)

### Community 30 - "cache.php"
Cohesion: 0.39
Nodes (4): EnsureActiveCustomer, EnsureAdmin, Closure, Symfony\Component\HttpFoundation\Response

### Community 32 - "filesystems.php"
Cohesion: 0.22
Nodes (9): 5. Design System, Animation principles, Border radius, Colors, Iconography, Interaction principles, Shadows, Spacing system (+1 more)

### Community 33 - "logging.php"
Cohesion: 0.22
Nodes (9): 7.1.1 Data Contoh Non-Production, 7.1 POS API, 7.2 Data Ownership, 7.3.1 Endpoint Baseline, 7.3.2 Kontrak Request Rates, 7.3.3 Kontrak Response dan Snapshot, 7.3.4 Scope, Error, dan Environment, 7.3 Biteship API (+1 more)

### Community 34 - "mail.php"
Cohesion: 0.33
Nodes (7): Catalog Access Registration and Approval Flows, Admin Configuration Reporting and Notification Flows, Cancellation Stock Return and POS Return Flow, Product Cart Pricing Shipping and Checkout Flows, Order Invoice and POS Sales Reporting Flow, Payment Verification and Fulfillment Flows, POS Master and Effective Stock Flows

### Community 35 - "queue.php"
Cohesion: 0.38
Nodes (4): Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase, ExampleTest, TestCase

### Community 37 - "session.php"
Cohesion: 0.33
Nodes (6): Cart Order Invoice and Payment Data, Notifications and Audit Data, POS Integration and Synchronization Data, Product Pricing and Inventory Data, Data Constraints and Retention, Shipment Snapshot Data

### Community 38 - "index.php"
Cohesion: 0.33
Nodes (6): 1. UI Layer Architecture, Layer 1 — Tailwind tokens and utilities, Layer 2 — Flux primitives, Layer 3 — Shared project components, Layer 4 — Business components, Layer 5 — Page layouts and screens

### Community 39 - "app.js"
Cohesion: 0.33
Nodes (6): 12.1 Performance, 12.2 Availability and Resilience, 12.3 Security, 12.4 Data and Time, 12.5 Browser and Accessibility, 12. Non-Functional Requirements

### Community 41 - "Illuminate\Http\Request"
Cohesion: 0.40
Nodes (5): Business Components Layer, Flux Primitives Layer, Page Screens Layer, Shared Project Components Layer, Tailwind Tokens Layer

### Community 42 - "HealthTest.php"
Cohesion: 0.40
Nodes (5): 17.1 Environment, 17.2 CI Gate, 17.3 Deployment, 17.4 Increment dan Release Gate, 17. Development and Deployment

### Community 43 - "Pest.php"
Cohesion: 0.50
Nodes (5): Black and Yellow Visual Identity, Customer-Service Shopping Mascot, E-Commerce Shopping Cart Symbol, Pixel Komunika Brand Logo, Pixel Komunika Wordmark

### Community 44 - "vite.config.js"
Cohesion: 0.40
Nodes (5): Compact Keyboard Product Placeholder, Function and Media Key Row, Low-Profile Compact Keyboard, Mac-Style Modifier-Key Layout, Minimalist Desktop Product Presentation

### Community 45 - "CustomerProfile"
Cohesion: 0.40
Nodes (5): Apple iPhone Devices, Mixed Smartphone Assortment, Mobile Phone Category Placeholder, Pastel Smartphone Flat-Lay Presentation, Unbranded White Smartphone

### Community 46 - "DESIGN.md — Color Tokens & Font"
Cohesion: 0.50
Nodes (3): addToCart, decrementQuantity, incrementQuantity

### Community 47 - "guest.blade.php"
Cohesion: 0.50
Nodes (3): approvePayment({{ $order->latestPaymentProof->id }}), cancelOrder({{ $order->id }}), updateOrderStatus({{ $order->id }}, 

### Community 48 - "2. Prinsip Desain"
Cohesion: 0.50
Nodes (4): 2. Flux Adoption Matrix, Admin screens, Customer screens, Public screens

### Community 49 - "3. Keputusan Arsitektur"
Cohesion: 0.50
Nodes (4): 3. Component Strategy, Flux components, Pixel components, Rule of thumb

### Community 50 - "5. Deployment Profile"
Cohesion: 0.50
Nodes (4): 4. Folder Structure, `resources/views/components/ui/`, `resources/views/flux/`, `resources/views/livewire/pages/`, `resources/views/livewire/admin/`, `resources/views/livewire/customer/`

### Community 51 - "2026_08_04_170000_create_catalog_pricing_inventory_tables.php"
Cohesion: 0.50
Nodes (4): 2.1 Engineering Governance Hybrid, 2.2 Change Notice dan Klarifikasi 27 Juli-11 Agustus 2026, 2.3 Design Artifacts, 2. Prinsip Desain

### Community 52 - "4. Technology Stack"
Cohesion: 0.50
Nodes (4): 3.1 Pola, 3.2 Modul Aplikasi, 3.3 Component Diagram, 3. Keputusan Arsitektur

### Community 53 - "9. Conceptual Data Model"
Cohesion: 0.50
Nodes (4): 5.1 Profil A - VPS (Opsi Upgrade), 5.2 Profil B - Shared Hosting (Production Baseline), 5.3 Keputusan Hosting, 5. Deployment Profile

### Community 55 - "customers/index.blade.php"
Cohesion: 0.83
Nodes (4): Customer Support Headset, Friendly Headset Mascot, Game Controller, Playful Technology Brand Persona

### Community 56 - "login.blade.php"
Cohesion: 0.67
Nodes (4): Accessories Category Placeholder, Modern Laptop Product Image, Premium Electronics Presentation, Thin-Bezel Laptop

### Community 57 - "register.blade.php"
Cohesion: 0.67
Nodes (4): Atmospheric Teal Product Presentation, Handheld Consumer-Electronics Package, Xiaomi Brand Mark, Xiaomi Packaged Product Placeholder

### Community 58 - "checkout/index.blade.php"
Cohesion: 0.67
Nodes (4): Headphones Product Image, Minimal Electronics Product Photography, Over-Ear Headphones, Yellow-Black Visual Contrast

### Community 59 - "components/layouts/app.blade.php"
Cohesion: 0.67
Nodes (4): Computer Mouse, Computer Mouse Product Image, Curved Ergonomic Form, Neutral-Background Product Photography

### Community 68 - "Rekomendasi pemakaian"
Cohesion: 0.67
Nodes (3): 4.1 Dependency Policy, 4.2 Frontend toolkit (MVP), 4. Technology Stack

### Community 69 - "Frontend UI Architecture"
Cohesion: 0.67
Nodes (3): 9.1 Entity Utama, 9.2 Relasi Konseptual, 9. Conceptual Data Model

### Community 70 - "1. UI Layer Architecture"
Cohesion: 0.67
Nodes (3): Digital Voucher Category, Mobile App Ecosystem, Smartphone and Laptop Voucher Placeholder

## Ambiguous Edges - Review These
- `Modern Laptop Product Image` → `Accessories Category Placeholder`  [AMBIGUOUS]
  public/assets/placeholders/accessories.webp · relation: conceptually_related_to
- `Smartphone and Laptop Voucher Placeholder` → `Digital Voucher Category`  [AMBIGUOUS]
  public/assets/placeholders/voucher.webp · relation: conceptually_related_to

## Knowledge Gaps
- **458 isolated node(s):** `1. Rust Token Killer (RTK)`, `2. Graphify`, `3. Ponytail Ultra`, `Bootstrap Wajib`, `Urutan Kerja` (+453 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **19 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **What is the exact relationship between `Modern Laptop Product Image` and `Accessories Category Placeholder`?**
  _Edge tagged AMBIGUOUS (relation: conceptually_related_to) - confidence is low._
- **What is the exact relationship between `Smartphone and Laptop Voucher Placeholder` and `Digital Voucher Category`?**
  _Edge tagged AMBIGUOUS (relation: conceptually_related_to) - confidence is low._
- **Why does `Frontend UI Architecture` connect `17. Development and Deployment` to `filesystems.php`, `3. Customer-facing Flows`, `15. Keputusan Terbuka`, `index.php`, `2. Prinsip Desain`, `3. Keputusan Arsitektur`, `5. Deployment Profile`, `artisan`, `ponytail.md`?**
  _High betweenness centrality (0.056) - this node is a cross-community bridge._
- **Why does `Data Dictionary` connect `composer.json` to `3. Customer-facing Flows`?**
  _High betweenness centrality (0.030) - this node is a cross-community bridge._
- **Why does `Functional Requirements Document (FRD)` connect `Repository Agent Instructions` to `3. Customer-facing Flows`?**
  _High betweenness centrality (0.029) - this node is a cross-community bridge._
- **Are the 2 inferred relationships involving `User` (e.g. with `.store()` and `.run()`) actually correct?**
  _`User` has 2 INFERRED edges - model-reasoned connections that need verification._
- **What connects `1. Rust Token Killer (RTK)`, `2. Graphify`, `3. Ponytail Ultra` to the rest of the system?**
  _458 weakly-connected nodes found - possible documentation gaps or missing edges._