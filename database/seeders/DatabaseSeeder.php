<?php

namespace Database\Seeders;

use App\Domains\SeedDataSupport\SampleCatalogImporter;
use App\Models\CustomerProfile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function __construct(
        private readonly SampleCatalogImporter $sampleCatalogImporter,
    ) {}

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminRoleId = Role::query()->where('code', Role::ADMIN)->value('id');
        $customerRoleId = Role::query()->where('code', Role::CUSTOMER)->value('id');

        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@pixelkomunika.test'],
            [
                'role_id' => $adminRoleId,
                'name' => 'Admin Pixel Komunika',
                'phone' => '081111111111',
                'password' => 'password',
            ],
        );

        $customer = User::query()->updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'role_id' => $customerRoleId,
                'name' => 'Test User',
                'phone' => '082222222222',
                'password' => 'password',
            ],
        );

        $customer->customerProfile()->updateOrCreate(
            [],
            [
                'business_name' => 'Toko Test',
                'verification_status' => CustomerProfile::ACTIVE,
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
            ],
        );

        $this->sampleCatalogImporter->import();
    }
}
