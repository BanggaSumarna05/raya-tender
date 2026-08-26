<?php

namespace Database\Seeders;

use App\Models\TenderCategory;
use Illuminate\Database\Seeder;

class TenderCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['code' => 'GOV', 'name' => 'Government', 'description' => 'Tender dari instansi pemerintah'],
            ['code' => 'PRV', 'name' => 'Private', 'description' => 'Tender dari perusahaan swasta'],
            ['code' => 'BUMN', 'name' => 'BUMN', 'description' => 'Tender dari Badan Usaha Milik Negara'],
            ['code' => 'KON', 'name' => 'Konstruksi', 'description' => 'Pekerjaan konstruksi bangunan'],
            ['code' => 'INF', 'name' => 'Infrastruktur', 'description' => 'Pekerjaan infrastruktur'],
            ['code' => 'REN', 'name' => 'Renovasi', 'description' => 'Pekerjaan renovasi'],
            ['code' => 'INT', 'name' => 'Interior', 'description' => 'Pekerjaan interior'],
            ['code' => 'MEP', 'name' => 'MEP', 'description' => 'Mechanical, Electrical, Plumbing'],
            ['code' => 'MNT', 'name' => 'Maintenance', 'description' => 'Pekerjaan pemeliharaan'],
            ['code' => 'IT', 'name' => 'IT', 'description' => 'Proyek teknologi informasi'],
            ['code' => 'PRO', 'name' => 'Procurement', 'description' => 'Pengadaan barang dan jasa'],
            ['code' => 'OTH', 'name' => 'Lainnya', 'description' => 'Kategori lainnya'],
        ];

        foreach ($categories as $category) {
            TenderCategory::firstOrCreate(
                ['code' => $category['code']],
                array_merge($category, ['status' => 'active'])
            );
        }
    }
}
