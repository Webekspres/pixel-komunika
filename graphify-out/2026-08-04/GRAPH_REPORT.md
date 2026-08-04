# Graph Report - pixel-komunika  (2026-08-04)

## Corpus Check
- 51 files · ~82,105 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 473 nodes · 470 edges · 50 communities (44 shown, 6 thin omitted)
- Extraction: 100% EXTRACTED · 0% INFERRED · 0% AMBIGUOUS · INFERRED: 1 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `2ee67d4c`
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
- User.php
- devDependencies
- Repository Agent Instructions
- 5. Alur Utama
- config
- Minimum Viable Product (MVP) Baseline
- 7.3 Biteship API
- Dokumentasi Proyek
- AppServiceProvider
- TestCase
- ExampleTest
- Domains/README.md
- Controller.php
- ponytail.md
- require-dev
- Landing.php
- 3. Product, Pricing, dan Inventory
- DESIGN.md — Color Tokens & Font

## God Nodes (most connected - your core abstractions)
1. `15. Keputusan Terbuka` - 28 edges
2. `Software Requirements Specification (SRS)` - 23 edges
3. `Functional Requirements Document (FRD)` - 19 edges
4. `Business Requirements Document (BRD)` - 18 edges
5. `Ringkasan Ruang Lingkup dan Persetujuan Pengembangan Website` - 14 edges
6. `3. Customer-facing Flows` - 14 edges
7. `Data Dictionary` - 13 edges
8. `8. Keputusan yang Masih Diperlukan dari Klien` - 12 edges
9. `require-dev` - 10 edges
10. `3. Product, Pricing, dan Inventory` - 10 edges

## Surprising Connections (you probably didn't know these)
- `ExampleTest` --inherits--> `TestCase`  [EXTRACTED]
  tests/Feature/ExampleTest.php → tests/TestCase.php

## Import Cycles
- None detected.

## Communities (50 total, 6 thin omitted)

### Community 0 - "Data Dictionary"
Cohesion: 0.06
Nodes (34): 10. Constraint dan Index Minimum, 11. Delete dan Retention, 1. Konvensi, 2.1 `roles`, 2.2 `users`, 2.3 `customer_profiles`, 2.4 `addresses`, 2. Identity dan Customer (+26 more)

### Community 1 - "Software Requirements Specification (SRS)"
Cohesion: 0.05
Nodes (43): 10. Transaction and Concurrency, 11. Queue and Scheduler, 12.1 Performance, 12.2 Availability and Resilience, 12.3 Security, 12.4 Data and Time, 12.5 Browser and Accessibility, 12. Non-Functional Requirements (+35 more)

### Community 2 - "composer.json"
Cohesion: 0.07
Nodes (27): autoload, autoload-dev, psr-4, psr-4, description, extra, laravel, keywords (+19 more)

### Community 3 - "3. Customer-facing Flows"
Cohesion: 0.07
Nodes (28): 1.1 Notasi Flowchart, 1. Konvensi, 2. Indeks Flow, 3. Customer-facing Flows, 4. Back-office dan Integration Flows, 5. Traceability, 6. Open Decisions yang Membatasi Flow, Pixel Komunika E-Commerce (+20 more)

### Community 4 - "15. Keputusan Terbuka"
Cohesion: 0.07
Nodes (28): 15.1 Klarifikasi Klien 28 Juli 2026, 15.2 MVP Baseline dan Stage Gates, 15.3 Rencana Delivery 45 Hari Kerja, 15.4 Checklist Pra-Pengembangan, 15. Keputusan Terbuka, OPN-001, OPN-002, OPN-003 (+20 more)

### Community 5 - "Ringkasan Ruang Lingkup dan Persetujuan Pengembangan Website"
Cohesion: 0.07
Nodes (26): 10. Perubahan Setelah Persetujuan, 11. Persetujuan, 1.1 Cara Memberikan Tanggapan melalui Grup WhatsApp, 1. Tujuan Dokumen, 2. Ringkasan Website, 3. Fitur yang Termasuk dalam Versi Pertama, 4. Keputusan yang Sudah Disepakati, 6. Isi Minimum Invoice (+18 more)

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
Cohesion: 0.36
Nodes (6): 1. Batasan Model, 2. ERD Working Baseline, 3. Aturan Relasi Utama, 4. Keputusan Minimal untuk Implementasi, Entity Relationship Diagram (ERD), Pixel Komunika E-Commerce

### Community 10 - "User.php"
Cohesion: 0.16
Nodes (10): User, UserFactory, DatabaseSeeder, Illuminate\Database\Console\Seeds\WithoutModelEvents, Illuminate\Database\Eloquent\Factories\Factory, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Database\Seeder, Illuminate\Foundation\Auth\User (+2 more)

### Community 11 - "devDependencies"
Cohesion: 0.11
Nodes (17): concurrently, laravel-vite-plugin, devDependencies, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite (+9 more)

### Community 12 - "Repository Agent Instructions"
Cohesion: 0.12
Nodes (16): 1. Rust Token Killer (RTK), 2. Graphify, 3. Ponytail Ultra, Always-Active Tooling, Aturan penulisan, Bootstrap Wajib, Contoh singkat, `dev` — manual (+8 more)

### Community 13 - "5. Alur Utama"
Cohesion: 0.17
Nodes (12): 5.1 Pendaftaran dan Persetujuan Pelanggan, 5.2.1 Memilih Produk dan Pengiriman, 5.2.2 Membuat Pesanan dan Mengirim Bukti Pembayaran, 5.2.3 Memverifikasi Pembayaran, 5.2 Pemesanan dan Pembayaran, 5.3.1 Menyiapkan dan Mengemas Pesanan, 5.3.2 Menyerahkan Pesanan kepada Kurir, 5.3.3 Mengonfirmasi Penerimaan dan Menyelesaikan Pesanan (+4 more)

### Community 14 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 15 - "Minimum Viable Product (MVP) Baseline"
Cohesion: 0.22
Nodes (9): 1. Tujuan, 2. Aturan Prioritas, 3. Daftar P0 / Must Have, 4. Data Contoh Saat Koneksi POS Belum Tersedia, 5. Tidak Termasuk P0 Saat Ini, 6. MVP Acceptance Gate, 7. Persetujuan MVP, Minimum Viable Product (MVP) Baseline (+1 more)

### Community 16 - "7.3 Biteship API"
Cohesion: 0.22
Nodes (9): 7.1.1 Data Contoh Non-Production, 7.1 POS API, 7.2 Data Ownership, 7.3.1 Endpoint Baseline, 7.3.2 Kontrak Request Rates, 7.3.3 Kontrak Response dan Snapshot, 7.3.4 Scope, Error, dan Environment, 7.3 Biteship API (+1 more)

### Community 17 - "Dokumentasi Proyek"
Cohesion: 0.29
Nodes (7): Client Deliverable, Current Change Notice, Delivery Method, Design Documents, Dokumentasi Proyek, Requirement Documents, Source Documents

### Community 19 - "TestCase"
Cohesion: 0.40
Nodes (3): Illuminate\Foundation\Testing\TestCase, ExampleTest, TestCase

### Community 40 - "require-dev"
Cohesion: 0.20
Nodes (10): require-dev, fakerphp/faker, laravel/pail, laravel/pao, laravel/pint, mockery/mockery, nunomaduro/collision, pestphp/pest (+2 more)

### Community 45 - "3. Product, Pricing, dan Inventory"
Cohesion: 0.20
Nodes (10): 3.1 `categories`, 3.2 `brands`, 3.3 `products`, 3.4 `product_enrichments`, 3.5 `product_media`, 3.6 `product_prices`, 3.7 `category_tax_rules`, 3.8 `inventory_snapshots` (+2 more)

### Community 46 - "DESIGN.md — Color Tokens & Font"
Cohesion: 0.12
Nodes (15): Color tokens, DESIGN.md — Color Tokens & Font, Font, Neutrals (UI), Open, Product context, Semantic, Tailwind `@theme` (+7 more)

## Knowledge Gaps
- **309 isolated node(s):** `Controller`, `$schema`, `name`, `type`, `description` (+304 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **6 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Business Requirements Document (BRD)` connect `Business Requirements Document (BRD)` to `docs/README.md`, `15. Keputusan Terbuka`?**
  _High betweenness centrality (0.114) - this node is a cross-community bridge._
- **Why does `Software Requirements Specification (SRS)` connect `Software Requirements Specification (SRS)` to `7.3 Biteship API`, `docs/README.md`?**
  _High betweenness centrality (0.113) - this node is a cross-community bridge._
- **Why does `Data Dictionary` connect `Data Dictionary` to `docs/README.md`, `3. Product, Pricing, dan Inventory`?**
  _High betweenness centrality (0.096) - this node is a cross-community bridge._
- **What connects `Controller`, `$schema`, `name` to the rest of the system?**
  _309 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Data Dictionary` be split into smaller, more focused modules?**
  _Cohesion score 0.058823529411764705 - nodes in this community are weakly interconnected._
- **Should `Software Requirements Specification (SRS)` be split into smaller, more focused modules?**
  _Cohesion score 0.046511627906976744 - nodes in this community are weakly interconnected._
- **Should `composer.json` be split into smaller, more focused modules?**
  _Cohesion score 0.07142857142857142 - nodes in this community are weakly interconnected._