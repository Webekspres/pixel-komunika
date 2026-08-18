<?php

namespace App\Domains\SeedDataSupport;

use App\Models\Brand;
use App\Models\Category;
use App\Models\CategoryTaxRule;
use App\Models\InventoryLedger;
use App\Models\InventorySnapshot;
use App\Models\Product;
use App\Models\ProductEnrichment;
use App\Models\ProductPrice;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SampleCatalogImporter
{
    public function import(): void
    {
        DB::transaction(function (): void {
            foreach ($this->dataset() as $categoryData) {
                $category = Category::query()->updateOrCreate(
                    ['pos_category_id' => $categoryData['pos_category_id']],
                    Arr::except($categoryData, ['tax_rule', 'products']),
                );

                CategoryTaxRule::query()->updateOrCreate(
                    ['category_id' => $category->id],
                    $categoryData['tax_rule'],
                );

                foreach ($categoryData['products'] as $productData) {
                    $brand = Brand::query()->updateOrCreate(
                        ['pos_brand_id' => $productData['brand']['pos_brand_id']],
                        $productData['brand'],
                    );

                    $product = Product::query()->updateOrCreate(
                        ['pos_product_id' => $productData['pos_product_id']],
                        array_merge(
                            Arr::except($productData, ['brand', 'prices', 'inventory', 'enrichment']),
                            [
                                'category_id' => $category->id,
                                'brand_id' => $brand->id,
                            ],
                        ),
                    );

                    ProductEnrichment::query()->updateOrCreate(
                        ['product_id' => $product->id],
                        $productData['enrichment'],
                    );

                    foreach ($productData['prices'] as $price) {
                        ProductPrice::query()->updateOrCreate(
                            [
                                'product_id' => $product->id,
                                'price_type' => $price['price_type'],
                            ],
                            $price,
                        );
                    }

                    $snapshot = InventorySnapshot::query()->firstOrNew([
                        'product_id' => $product->id,
                    ]);

                    $before = (int) ($snapshot->quantity_available ?? 0);
                    $after = (int) $productData['inventory']['quantity_available'];

                    $snapshot->fill($productData['inventory'])->save();

                    InventoryLedger::query()->create([
                        'product_id' => $product->id,
                        'source' => 'FULL_SYNC',
                        'quantity_before' => $before,
                        'quantity_after' => $after,
                        'quantity_delta' => $after - $before,
                        'external_reference' => 'seed:'.$product->sku,
                        'occurred_at' => now(),
                        'meta' => ['dataset' => 'sample_catalog'],
                    ]);
                }
            }
        });
    }

    public function dataset(): array
    {
        return [
            [
                'pos_category_id' => 'CAT-ACCESSORIES',
                'name' => 'Aksesoris Elektronik',
                'is_active' => true,
                'source_updated_at' => now(),
                'synced_at' => now(),
                'tax_rule' => [
                    'threshold_amount' => 5000000,
                    'rate_percent' => 0.5000,
                    'is_active' => true,
                ],
                'products' => [
                    $this->product(
                        posProductId: 'PROD-PB10000',
                        sku: 'PB-10000',
                        name: 'Power Bank 10.000 mAh',
                        brand: ['pos_brand_id' => 'BRAND-PIXEL', 'name' => 'Pixel Gear', 'is_active' => true, 'source_updated_at' => now(), 'synced_at' => now()],
                        prices: [175000, 165000, 155000],
                        wholesaleMinimum: 12,
                        stock: 48,
                        lowStockThreshold: 8,
                        weightGrams: 260,
                        lengthCm: 15,
                        widthCm: 7,
                        heightCm: 2.5,
                        enrichment: [
                            'display_name' => 'Power Bank 10.000 mAh Fast Charging Dual USB',
                            'short_description' => 'Power bank 10.000 mAh dengan dual output USB dan fast charging untuk kebutuhan mobile harian.',
                            'description' => <<<'TXT'
                                Power bank 10.000 mAh dari Pixel Gear untuk menemani aktivitas mobile harian, baik untuk kebutuhan pribadi maupun operasional usaha. Baterai lithium-polymer berkapasitas 10.000 mAh mampu mengisi ulang smartphone standar hingga dua kali penuh.

                                Dilengkapi dual output USB (5V/2.1A) sehingga dua perangkat dapat diisi bersamaan, indikator LED untuk memantau sisa daya, serta perlindungan berlapis terhadap over-charge, over-discharge, over-current, dan short circuit.

                                Produk original dengan garansi distributor. Tersedia harga partai dan grosir untuk pembelian jumlah besar — hubungi tim Pixel Komunika untuk penawaran terbaik.
                                TXT,
                            'seo_title' => 'Power Bank 10.000 mAh Fast Charging | Pixel Komunika',
                            'seo_description' => 'Power bank 10.000 mAh dual USB fast charging original. Tersedia harga partai dan grosir untuk reseller di Bandung.',
                            'label' => 'Best Seller',
                        ],
                    ),
                    $this->product(
                        posProductId: 'PROD-HP-WL',
                        sku: 'HP-WL',
                        name: 'Headphone Wireless',
                        brand: ['pos_brand_id' => 'BRAND-AUDIO', 'name' => 'Audio Plus', 'is_active' => true, 'source_updated_at' => now(), 'synced_at' => now()],
                        prices: [220000, 205000, 195000],
                        wholesaleMinimum: 10,
                        stock: 9,
                        lowStockThreshold: 10,
                        weightGrams: 220,
                        lengthCm: 18,
                        widthCm: 16,
                        heightCm: 6,
                        enrichment: [
                            'display_name' => 'Headphone Wireless Bluetooth 5.3',
                            'short_description' => 'Headphone wireless Bluetooth 5.3 dengan driver 40 mm, baterai hingga 20 jam, dan mikrofon terintegrasi.',
                            'description' => <<<'TXT'
                                Headphone wireless dari Audio Plus dengan koneksi Bluetooth 5.3 yang stabil dan latency rendah. Driver 40 mm menghadirkan suara jernih dengan bass yang cukup untuk musik, podcast, hingga meeting online.

                                Baterai internal bertahan hingga 20 jam pemakaian dan terisi penuh dalam sekitar 2 jam melalui kabel USB. Mikrofon terintegrasi mendukung panggilan telepon dan video conference. Desain lipat dengan earcup empuk membuatnya nyaman dibawa saat perjalanan dinas.

                                Cocok untuk kebutuhan kantor, reseller, maupun penggunaan harian. Tersedia harga khusus partai dan grosir untuk pemesanan dalam jumlah banyak.
                                TXT,
                            'seo_title' => 'Headphone Wireless Bluetooth 5.3 | Pixel Komunika',
                            'seo_description' => 'Headphone wireless Bluetooth 5.3 driver 40 mm, baterai 20 jam, mikrofon terintegrasi. Harga grosir untuk reseller di Bandung.',
                            'label' => 'Terlaris',
                        ],
                    ),
                ],
            ],
            [
                'pos_category_id' => 'CAT-VOUCHER',
                'name' => 'Kartu Data & Voucher',
                'is_active' => true,
                'source_updated_at' => now(),
                'synced_at' => now(),
                'tax_rule' => [
                    'threshold_amount' => 2500000,
                    'rate_percent' => 0.3000,
                    'is_active' => true,
                ],
                'products' => [
                    $this->product(
                        posProductId: 'PROD-VCHR-TSEL',
                        sku: 'VCHR-TSEL-25',
                        name: 'Voucher Internet 25GB',
                        brand: ['pos_brand_id' => 'BRAND-TSEL', 'name' => 'Telkomsel', 'is_active' => true, 'source_updated_at' => now(), 'synced_at' => now()],
                        prices: [82000, 79000, 76000],
                        wholesaleMinimum: 20,
                        stock: 120,
                        lowStockThreshold: 25,
                        weightGrams: 10,
                        lengthCm: 8.5,
                        widthCm: 5.5,
                        heightCm: 0.1,
                        enrichment: [
                            'display_name' => 'Voucher Internet Telkomsel 25GB (30 Hari)',
                            'short_description' => 'Voucher internet Telkomsel 25GB dengan masa aktif 30 hari dan aktivasi mudah melalui USSD.',
                            'description' => <<<'TXT'
                                Voucher internet Telkomsel 25GB untuk kebutuhan data reseller dan tim yang bekerja dari mana saja. Kuota utama 25GB dengan masa aktif 30 hari sejak aktivasi, berlaku di jaringan 4G LTE Telkomsel seluruh Indonesia.

                                Cara aktivasi mudah: masukkan kartu, tekan *363# lalu ikuti menu, atau aktivasi melalui aplikasi MyTelkomsel. Kode aktivasi dikirim setelah pembayaran dikonfirmasi oleh admin.

                                Cocok untuk pengisian ulang stok konter. Tersedia harga partai dan grosir dengan minimal pembelian tertentu — hubungi admin Pixel Komunika untuk pemesanan.
                                TXT,
                            'seo_title' => 'Voucher Internet Telkomsel 25GB 30 Hari | Pixel Komunika',
                            'seo_description' => 'Voucher data Telkomsel 25GB masa aktif 30 hari, aktivasi mudah. Harga partai dan grosir untuk konter dan reseller.',
                            'label' => 'Populer',
                        ],
                    ),
                ],
            ],
            [
                'pos_category_id' => 'CAT-PULSA',
                'name' => 'Pulsa',
                'is_active' => true,
                'source_updated_at' => now(),
                'synced_at' => now(),
                'tax_rule' => [
                    'threshold_amount' => 0,
                    'rate_percent' => 0,
                    'is_active' => true,
                ],
                'products' => [
                    $this->product(
                        posProductId: 'PROD-PLS-100',
                        sku: 'PLS-100',
                        name: 'Pulsa Reguler 100K',
                        brand: ['pos_brand_id' => 'BRAND-XL', 'name' => 'XL', 'is_active' => true, 'source_updated_at' => now(), 'synced_at' => now()],
                        prices: [101500, 100500, 99500],
                        wholesaleMinimum: 25,
                        stock: 300,
                        lowStockThreshold: 50,
                        weightGrams: 10,
                        lengthCm: 8.5,
                        widthCm: 5.5,
                        heightCm: 0.1,
                        enrichment: [
                            'display_name' => 'Pulsa XL Reguler 100.000',
                            'short_description' => 'Pulsa reguler XL senilai Rp100.000 dengan pengisian instan setelah pembayaran dikonfirmasi.',
                            'description' => <<<'TXT'
                                Pulsa reguler XL senilai Rp100.000 untuk kebutuhan komunikasi harian, kuota data, dan isi ulang saldo. Pengisian berjalan instan dan otomatis setelah pembayaran dikonfirmasi oleh admin.

                                Pulsa reguler berlaku untuk semua nomor XL di seluruh Indonesia dengan tarif transparan. Cocok untuk konter pulsa dan reseller yang membutuhkan stok isi ulang harian.

                                Tersedia harga partai dan grosir untuk pemesanan dalam jumlah besar. Pemesanan dapat dilakukan melalui website atau langsung menghubungi admin Pixel Komunika.
                                TXT,
                            'seo_title' => 'Pulsa XL Reguler 100.000 | Pixel Komunika',
                            'seo_description' => 'Pulsa XL reguler 100.000 dengan pengisian instan setelah pembayaran. Harga partai dan grosir untuk konter pulsa dan reseller.',
                        ],
                    ),
                ],
            ],
        ];
    }

    private function product(
        string $posProductId,
        string $sku,
        string $name,
        array $brand,
        array $prices,
        int $wholesaleMinimum,
        int $stock,
        int $lowStockThreshold,
        array $enrichment = [],
        int $weightGrams = 500,
        ?int $lengthCm = 10,
        ?int $widthCm = 8,
        ?int $heightCm = 4,
    ): array {
        $slug = Str::slug($name);

        return [
            'pos_product_id' => $posProductId,
            'sku' => $sku,
            'name' => $name,
            'weight_grams' => $weightGrams,
            'length_cm' => $lengthCm,
            'width_cm' => $widthCm,
            'height_cm' => $heightCm,
            'is_active' => true,
            'synced_at' => now(),
            'brand' => $brand,
            'enrichment' => array_merge([
                'slug' => $slug,
                'short_description' => null,
                'description' => null,
                'label' => null,
                'display_order' => 0,
                'is_visible' => true,
            ], $enrichment),
            'prices' => [
                [
                    'price_type' => ProductPrice::RETAIL,
                    'amount' => $prices[0],
                    'minimum_quantity' => null,
                    'pos_price_id' => $posProductId.'-ECERAN',
                    'synced_at' => now(),
                ],
                [
                    'price_type' => ProductPrice::BULK,
                    'amount' => $prices[1],
                    'minimum_quantity' => 5,
                    'pos_price_id' => $posProductId.'-PARTAI',
                    'synced_at' => now(),
                ],
                [
                    'price_type' => ProductPrice::WHOLESALE,
                    'amount' => $prices[2],
                    'minimum_quantity' => $wholesaleMinimum,
                    'pos_price_id' => $posProductId.'-GROSIR',
                    'synced_at' => now(),
                ],
            ],
            'inventory' => [
                'quantity_available' => $stock,
                'low_stock_threshold' => $lowStockThreshold,
                'stock_status' => $stock <= 0
                    ? InventorySnapshot::OUT
                    : ($stock <= $lowStockThreshold ? InventorySnapshot::LOW : InventorySnapshot::AVAILABLE),
                'source_updated_at' => now(),
                'synced_at' => now(),
            ],
        ];
    }
}
