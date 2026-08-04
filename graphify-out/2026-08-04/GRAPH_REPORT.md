# Graph Report - pixel-komunika  (2026-08-04)

## Corpus Check
- 88 files · ~87,548 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 625 nodes · 758 edges · 62 communities (57 shown, 5 thin omitted)
- Extraction: 98% EXTRACTED · 2% INFERRED · 0% AMBIGUOUS · INFERRED: 14 edges (avg confidence: 0.78)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `55b96dcd`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

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
- CustomerProfile
- package.json
- Repository Agent Instructions
- Illuminate\Database\Eloquent\Model
- PROGRESS
- Minimum Viable Product (MVP) Baseline
- 7.3 Biteship API
- AppServiceProvider
- TestCase
- ExampleTest
- Domains/README.md
- 12. Non-Functional Requirements
- ponytail.md
- 17. Development and Deployment
- app.js
- Illuminate\Http\Request
- DESIGN.md — Color Tokens & Font
- 2. Prinsip Desain
- 3. Keputusan Arsitektur
- 5. Deployment Profile
- 4. Technology Stack
- 9. Conceptual Data Model

## God Nodes (most connected - your core abstractions)
1. `15. Keputusan Terbuka` - 28 edges
2. `Software Requirements Specification (SRS)` - 23 edges
3. `Functional Requirements Document (FRD)` - 19 edges
4. `Business Requirements Document (BRD)` - 18 edges
5. `CustomerProfile` - 16 edges
6. `Product` - 14 edges
7. `User` - 14 edges
8. `Ringkasan Ruang Lingkup dan Persetujuan Pengembangan Website` - 14 edges
9. `3. Customer-facing Flows` - 14 edges
10. `Data Dictionary` - 13 edges

## Surprising Connections (you probably didn't know these)
- `DatabaseSeeder` --references--> `SampleCatalogImporter`  [EXTRACTED]
  database/seeders/DatabaseSeeder.php → app/Domains/SeedDataSupport/SampleCatalogImporter.php
- `CustomerReviewController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/Admin/CustomerReviewController.php → app/Http/Controllers/Controller.php
- `AuthenticatedSessionController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/Auth/AuthenticatedSessionController.php → app/Http/Controllers/Controller.php
- `RegisteredUserController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/Auth/RegisteredUserController.php → app/Http/Controllers/Controller.php
- `ExampleTest` --inherits--> `TestCase`  [EXTRACTED]
  tests/Feature/ExampleTest.php → tests/TestCase.php

## Import Cycles
- None detected.

## Communities (62 total, 5 thin omitted)

### Community 0 - "Data Dictionary"
Cohesion: 0.05
Nodes (44): 10. Constraint dan Index Minimum, 11. Delete dan Retention, 1. Konvensi, 2.1 `roles`, 2.2 `users`, 2.3 `customer_profiles`, 2.4 `addresses`, 2. Identity dan Customer (+36 more)

### Community 1 - "Software Requirements Specification (SRS)"
Cohesion: 0.13
Nodes (15): 10. Transaction and Concurrency, 11. Queue and Scheduler, 13. File and Media Handling, 14. Caching and Traffic Spike Protection, 15. Logging, Audit, and Observability, 16. Backup and Disaster Recovery, 18. Testing Requirements, 19. Traceability FRD ke SRS (+7 more)

### Community 2 - "composer.json"
Cohesion: 0.04
Nodes (46): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+38 more)

### Community 3 - "3. Customer-facing Flows"
Cohesion: 0.07
Nodes (28): 1.1 Notasi Flowchart, 1. Konvensi, 2. Indeks Flow, 3. Customer-facing Flows, 4. Back-office dan Integration Flows, 5. Traceability, 6. Open Decisions yang Membatasi Flow, Pixel Komunika E-Commerce (+20 more)

### Community 4 - "15. Keputusan Terbuka"
Cohesion: 0.07
Nodes (28): 15.1 Klarifikasi Klien 28 Juli 2026, 15.2 MVP Baseline dan Stage Gates, 15.3 Rencana Delivery 45 Hari Kerja, 15.4 Checklist Pra-Pengembangan, 15. Keputusan Terbuka, OPN-001, OPN-002, OPN-003 (+20 more)

### Community 5 - "Ringkasan Ruang Lingkup dan Persetujuan Pengembangan Website"
Cohesion: 0.05
Nodes (38): 10. Perubahan Setelah Persetujuan, 11. Persetujuan, 1.1 Cara Memberikan Tanggapan melalui Grup WhatsApp, 1. Tujuan Dokumen, 2. Ringkasan Website, 3. Fitur yang Termasuk dalam Versi Pertama, 4. Keputusan yang Sudah Disepakati, 5.1 Pendaftaran dan Persetujuan Pelanggan (+30 more)

### Community 6 - "Functional Requirements Document (FRD)"
Cohesion: 0.07
Nodes (27): 10. Modul Pembayaran Manual, 11. Modul Laporan, 12. Audit dan Monitoring, 13. Modul Notifikasi Admin, 14. Candidate: Reseller, 15. Penanganan Error Fungsional, 16. Traceability BRD ke FRD, 17. Acceptance Gate (+19 more)

### Community 7 - "scripts"
Cohesion: 0.08
Nodes (26): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+18 more)

### Community 8 - "Business Requirements Document (BRD)"
Cohesion: 0.08
Nodes (26): 10. Business Rules, 11.1 Registrasi dan Persetujuan, 11.2 Transaksi dan Pembayaran, 11.3 Sinkronisasi POS, 11. Proses Bisnis Utama, 12. Ukuran Keberhasilan, 13. Asumsi dan Dependensi, 14. Risiko Bisnis (+18 more)

### Community 9 - "docs/README.md"
Cohesion: 0.12
Nodes (19): 1. Batasan Model, 2. ERD Working Baseline, 3. Aturan Relasi Utama, 4. Keputusan Minimal untuk Implementasi, Entity Relationship Diagram (ERD), Pixel Komunika E-Commerce, Client Deliverable, Current Change Notice (+11 more)

### Community 10 - "CustomerProfile"
Cohesion: 0.07
Nodes (12): CustomerProfile, Role, User, UserFactory, DatabaseSeeder, Illuminate\Database\Console\Seeds\WithoutModelEvents, Illuminate\Database\Eloquent\Factories\Factory, Illuminate\Database\Eloquent\Factories\HasFactory (+4 more)

### Community 11 - "package.json"
Cohesion: 0.10
Nodes (20): concurrently, laravel-vite-plugin, lucide, dependencies, lucide, devDependencies, concurrently, laravel-vite-plugin (+12 more)

### Community 12 - "Repository Agent Instructions"
Cohesion: 0.12
Nodes (16): 1. Rust Token Killer (RTK), 2. Graphify, 3. Ponytail Ultra, Always-Active Tooling, Aturan penulisan, Bootstrap Wajib, Contoh singkat, `dev` — manual (+8 more)

### Community 13 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.07
Nodes (17): ImportSampleCatalog, PriceCalculator, SampleCatalogImporter, Brand, Category, CategoryTaxRule, InventoryLedger, InventorySnapshot (+9 more)

### Community 14 - "PROGRESS"
Cohesion: 0.12
Nodes (15): Blocker dan dependency eksternal, Cara pakai, Fase 1 — Identity & access, Fase 2 — Catalog, pricing, stock, dan data contoh POS, Fase 3 — Cart, checkout, shipping, order, invoice, Fase 4 — Payment, pembatalan/retur, admin workflow, Fase 5 — Reporting, audit, sync resilience, security, Fase 6 — Release readiness (+7 more)

### Community 15 - "Minimum Viable Product (MVP) Baseline"
Cohesion: 0.22
Nodes (9): 1. Tujuan, 2. Aturan Prioritas, 3. Daftar P0 / Must Have, 4. Data Contoh Saat Koneksi POS Belum Tersedia, 5. Tidak Termasuk P0 Saat Ini, 6. MVP Acceptance Gate, 7. Persetujuan MVP, Minimum Viable Product (MVP) Baseline (+1 more)

### Community 16 - "7.3 Biteship API"
Cohesion: 0.22
Nodes (9): 7.1.1 Data Contoh Non-Production, 7.1 POS API, 7.2 Data Ownership, 7.3.1 Endpoint Baseline, 7.3.2 Kontrak Request Rates, 7.3.3 Kontrak Response dan Snapshot, 7.3.4 Scope, Error, dan Environment, 7.3 Biteship API (+1 more)

### Community 19 - "TestCase"
Cohesion: 0.40
Nodes (3): Illuminate\Foundation\Testing\TestCase, ExampleTest, TestCase

### Community 25 - "12. Non-Functional Requirements"
Cohesion: 0.33
Nodes (6): 12.1 Performance, 12.2 Availability and Resilience, 12.3 Security, 12.4 Data and Time, 12.5 Browser and Accessibility, 12. Non-Functional Requirements

### Community 28 - "17. Development and Deployment"
Cohesion: 0.40
Nodes (5): 17.1 Environment, 17.2 CI Gate, 17.3 Deployment, 17.4 Increment dan Release Gate, 17. Development and Deployment

### Community 41 - "Illuminate\Http\Request"
Cohesion: 0.13
Nodes (13): CustomerReviewController, AuthenticatedSessionController, RegisteredUserController, Controller, EnsureActiveCustomer, EnsureAdmin, Home, Closure (+5 more)

### Community 46 - "DESIGN.md — Color Tokens & Font"
Cohesion: 0.14
Nodes (14): A — Baseline (sudah / native), B — Ambil saat storefront / Identity, C — Nanti (trigger konkret), Color tokens, D — Skip, DESIGN.md — Color Tokens & Font, Font, Frontend toolkit (+6 more)

### Community 48 - "2. Prinsip Desain"
Cohesion: 0.50
Nodes (4): 2.1 Engineering Governance Hybrid, 2.2 Change Notice dan Klarifikasi 27-28 Juli 2026, 2.3 Design Artifacts, 2. Prinsip Desain

### Community 49 - "3. Keputusan Arsitektur"
Cohesion: 0.50
Nodes (4): 3.1 Pola, 3.2 Modul Aplikasi, 3.3 Component Diagram, 3. Keputusan Arsitektur

### Community 50 - "5. Deployment Profile"
Cohesion: 0.50
Nodes (4): 5.1 Profil A - VPS (Opsi Upgrade), 5.2 Profil B - Shared Hosting (Production Baseline), 5.3 Keputusan Hosting, 5. Deployment Profile

### Community 52 - "4. Technology Stack"
Cohesion: 0.67
Nodes (3): 4.1 Dependency Policy, 4.2 Frontend toolkit (MVP), 4. Technology Stack

### Community 53 - "9. Conceptual Data Model"
Cohesion: 0.67
Nodes (3): 9.1 Entity Utama, 9.2 Relasi Konseptual, 9. Conceptual Data Model

## Knowledge Gaps
- **327 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+322 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **5 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Software Requirements Specification (SRS)` connect `Software Requirements Specification (SRS)` to `docs/README.md`, `2. Prinsip Desain`, `3. Keputusan Arsitektur`, `5. Deployment Profile`, `7.3 Biteship API`, `4. Technology Stack`, `9. Conceptual Data Model`, `12. Non-Functional Requirements`, `17. Development and Deployment`?**
  _High betweenness centrality (0.072) - this node is a cross-community bridge._
- **Why does `Business Requirements Document (BRD)` connect `Business Requirements Document (BRD)` to `docs/README.md`, `15. Keputusan Terbuka`?**
  _High betweenness centrality (0.071) - this node is a cross-community bridge._
- **Why does `Data Dictionary` connect `Data Dictionary` to `docs/README.md`?**
  _High betweenness centrality (0.060) - this node is a cross-community bridge._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _327 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Data Dictionary` be split into smaller, more focused modules?**
  _Cohesion score 0.045454545454545456 - nodes in this community are weakly interconnected._
- **Should `Software Requirements Specification (SRS)` be split into smaller, more focused modules?**
  _Cohesion score 0.13333333333333333 - nodes in this community are weakly interconnected._
- **Should `composer.json` be split into smaller, more focused modules?**
  _Cohesion score 0.0425531914893617 - nodes in this community are weakly interconnected._