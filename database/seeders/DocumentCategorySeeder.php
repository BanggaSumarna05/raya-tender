<?php

namespace Database\Seeders;

use App\Models\DocumentCategory;
use Illuminate\Database\Seeder;

class DocumentCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['code' => 'ADM', 'name' => 'Administrasi', 'description' => 'Dokumen administrasi tender'],
            ['code' => 'TEC', 'name' => 'Teknis', 'description' => 'Dokumen teknis dan spesifikasi'],
            ['code' => 'COM', 'name' => 'Komersial', 'description' => 'Dokumen penawaran harga'],
            ['code' => 'LEG', 'name' => 'Legal', 'description' => 'Dokumen legalitas perusahaan'],
            ['code' => 'CP', 'name' => 'Company Profile', 'description' => 'Profil perusahaan'],
            ['code' => 'PRO', 'name' => 'Proposal', 'description' => 'Dokumen proposal'],
            ['code' => 'TOR', 'name' => 'TOR/RKS', 'description' => 'Term of Reference / Rencana Kerja & Syarat'],
            ['code' => 'BOQ', 'name' => 'BOQ', 'description' => 'Bill of Quantity'],
            ['code' => 'OTH', 'name' => 'Lainnya', 'description' => 'Dokumen lainnya'],
        ];

        foreach ($categories as $category) {
            DocumentCategory::firstOrCreate(
                ['code' => $category['code']],
                array_merge($category, ['status' => 'active'])
            );
        }
    }
}
