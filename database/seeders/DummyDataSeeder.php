<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\Proposal;
use App\Models\ProposalVersion;
use App\Models\Reminder;
use App\Models\Tender;
use App\Models\TenderCategory;
use App\Models\TenderStatusHistory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================================
        // TAMBAHAN USER (Marketing Staff 2–4)
        // ==========================================
        // Pastikan akun admin selalu aktif sebelum mulai
        User::where('email', 'admin@rayatender.id')->update(['status' => 'active']);
        $staffUsers = [
            [
                'name'     => 'Budi Santoso',
                'username' => 'budi.santoso',
                'email'    => 'budi@rayatender.id',
                'phone'    => '081234567001',
                'role'     => 'Marketing Staff',
            ],
            [
                'name'     => 'Siti Rahma',
                'username' => 'siti.rahma',
                'email'    => 'siti@rayatender.id',
                'phone'    => '081234567002',
                'role'     => 'Marketing Staff',
            ],
            [
                'name'     => 'Andi Wijaya',
                'username' => 'andi.wijaya',
                'email'    => 'andi@rayatender.id',
                'phone'    => '081234567003',
                'role'     => 'Marketing Manager',
            ],
            [
                'name'     => 'Dewi Kusuma',
                'username' => 'dewi.kusuma',
                'email'    => 'dewi@rayatender.id',
                'phone'    => '081234567004',
                'role'     => 'Marketing Staff',
            ],
            [
                'name'     => 'Rizky Pratama',
                'username' => 'rizky.pratama',
                'email'    => 'rizky@rayatender.id',
                'phone'    => '081234567005',
                'role'     => 'Marketing Staff',
            ],
        ];

        $createdUsers = [];
        foreach ($staffUsers as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'     => $data['name'],
                    'username' => $data['username'],
                    'password' => Hash::make('Password@123'),
                    'phone'    => $data['phone'],
                    'status'   => 'active',
                ]
            );
            if ($user->getRoleNames()->isEmpty()) {
                $user->assignRole($data['role']);
            }
            $createdUsers[] = $user;
        }

        // Gabungkan semua staff untuk PIC
        $allStaff = User::whereHas('roles', fn($q) => $q->whereIn('name', ['Marketing Staff', 'Marketing Manager']))->get();
        $admin    = User::where('email', 'admin@rayatender.id')->first();

        // ==========================================
        // CLIENTS (5 dummy)
        // ==========================================
        $clientsData = [
            [
                'code'         => 'CLT-0001',
                'name'         => 'PT. Nusantara Raya Konstruksi',
                'company_type' => 'PT',
                'address'      => 'Jl. Sudirman No. 45, Jakarta Selatan',
                'city'         => 'Jakarta',
                'phone'        => '021-5550001',
                'email'        => 'contact@nusantararaya.co.id',
                'website'      => 'https://nusantararaya.co.id',
                'industry'     => 'Konstruksi',
                'pic_name'     => 'Hendra Gunawan',
                'pic_position' => 'Director',
                'pic_phone'    => '081111000001',
                'pic_email'    => 'hendra@nusantararaya.co.id',
                'status'       => 'active',
                'notes'        => 'Klien prioritas, proyek gedung perkantoran.',
            ],
            [
                'code'         => 'CLT-0002',
                'name'         => 'PT. Karya Agung Persada',
                'company_type' => 'PT',
                'address'      => 'Jl. Gatot Subroto No. 12, Jakarta Barat',
                'city'         => 'Jakarta',
                'phone'        => '021-5550002',
                'email'        => 'info@karyaagung.id',
                'website'      => 'https://karyaagung.id',
                'industry'     => 'Property',
                'pic_name'     => 'Ratna Sari',
                'pic_position' => 'Procurement Manager',
                'pic_phone'    => '081111000002',
                'pic_email'    => 'ratna@karyaagung.id',
                'status'       => 'active',
                'notes'        => 'Fokus pada proyek perumahan dan komersial.',
            ],
            [
                'code'         => 'CLT-0003',
                'name'         => 'PT. Infrastruktur Mandiri Indonesia',
                'company_type' => 'PT',
                'address'      => 'Jl. Thamrin No. 8, Jakarta Pusat',
                'city'         => 'Jakarta',
                'phone'        => '021-5550003',
                'email'        => 'tender@imi.co.id',
                'website'      => 'https://imi.co.id',
                'industry'     => 'Infrastruktur',
                'pic_name'     => 'Bambang Sutrisno',
                'pic_position' => 'VP Operations',
                'pic_phone'    => '081111000003',
                'pic_email'    => 'bambang@imi.co.id',
                'status'       => 'active',
                'notes'        => 'Proyek jalan tol dan jembatan.',
            ],
            [
                'code'         => 'CLT-0004',
                'name'         => 'PT. Graha Swasta Utama',
                'company_type' => 'PT',
                'address'      => 'Jl. HR Rasuna Said No. 20, Jakarta Selatan',
                'city'         => 'Surabaya',
                'phone'        => '031-5550004',
                'email'        => 'info@grahaswasta.com',
                'website'      => 'https://grahaswasta.com',
                'industry'     => 'Property',
                'pic_name'     => 'Linda Wijaya',
                'pic_position' => 'Direktur Pengembangan',
                'pic_phone'    => '081111000004',
                'pic_email'    => 'linda@grahaswasta.com',
                'status'       => 'active',
                'notes'        => 'Pengembang perumahan di Jawa Timur.',
            ],
            [
                'code'         => 'CLT-0005',
                'name'         => 'PT. Mitra Bangun Sejahtera',
                'company_type' => 'PT',
                'address'      => 'Jl. Diponegoro No. 100, Bandung',
                'city'         => 'Bandung',
                'phone'        => '022-5550005',
                'email'        => 'marketing@mbs.co.id',
                'website'      => 'https://mbs.co.id',
                'industry'     => 'Konstruksi',
                'pic_name'     => 'Ahmad Fauzi',
                'pic_position' => 'Manager Teknik',
                'pic_phone'    => '081111000005',
                'pic_email'    => 'ahmad@mbs.co.id',
                'status'       => 'inactive',
                'notes'        => 'Klien lama, saat ini sedang tidak aktif.',
            ],
        ];

        $clients = [];
        foreach ($clientsData as $data) {
            $clients[] = Client::firstOrCreate(['code' => $data['code']], $data);
        }

        // ==========================================
        // TENDERS (5 dummy)
        // ==========================================
        $categories = TenderCategory::all()->keyBy('code');
        $pic1 = $allStaff->first();
        $pic2 = $allStaff->skip(1)->first() ?? $pic1;
        $pic3 = $allStaff->skip(2)->first() ?? $pic1;
        $pic4 = $allStaff->skip(3)->first() ?? $pic1;
        $pic5 = $allStaff->skip(4)->first() ?? $pic1;

        $tendersData = [
            [
                'client'              => $clients[0],
                'category_code'       => 'KON',
                'title'               => 'Pembangunan Gedung Kantor 20 Lantai - Nusantara Tower',
                'source'              => 'Direct',
                'source_reference'    => 'REF-NT-2026-001',
                'pic'                 => $pic1,
                'location'            => 'Jakarta Selatan',
                'estimated_value'     => 45000000000,
                'received_date'       => Carbon::now()->subDays(30),
                'submission_deadline' => Carbon::now()->addDays(15)->setTime(16, 0),
                'status'              => 'proposal',
                'priority'            => 'high',
                'description'         => 'Proyek pembangunan gedung kantor 20 lantai dengan spesifikasi green building. Termasuk pekerjaan struktur, arsitektur, MEP, dan interior.',
                'requirements'        => 'Pengalaman minimal 10 tahun di bidang konstruksi gedung tinggi. Sertifikasi ISO 9001. Grade M1.',
                'notes'               => 'Client sangat memperhatikan timeline dan kualitas material.',
                'no_bid_reason'       => null,
                'lost_reason'         => null,
            ],
            [
                'client'              => $clients[1],
                'category_code'       => 'PRV',
                'title'               => 'Pembangunan Perumahan Grand Residence Phase 2',
                'source'              => 'LPSE',
                'source_reference'    => 'LPSE-GR-2026-045',
                'pic'                 => $pic2,
                'location'            => 'Tangerang Selatan',
                'estimated_value'     => 28500000000,
                'received_date'       => Carbon::now()->subDays(20),
                'submission_deadline' => Carbon::now()->addDays(5)->setTime(10, 0),
                'status'              => 'qualification',
                'priority'            => 'urgent',
                'description'         => 'Pengembangan perumahan 150 unit tipe 45 dan 70 dengan fasilitas lengkap. Termasuk clubhouse, kolam renang, dan area komersial.',
                'requirements'        => 'SIUJK aktif. Pengalaman proyek perumahan minimal 500 unit. Dukungan bank.',
                'notes'               => 'Deadline sangat ketat, perlu koordinasi intensif dengan tim.',
                'no_bid_reason'       => null,
                'lost_reason'         => null,
            ],
            [
                'client'              => $clients[2],
                'category_code'       => 'INF',
                'title'               => 'Rehabilitasi Jembatan Cisadane - Paket 3',
                'source'              => 'LPSE',
                'source_reference'    => 'LPSE-PUPR-2026-112',
                'pic'                 => $pic3,
                'location'            => 'Tangerang, Banten',
                'estimated_value'     => 12750000000,
                'received_date'       => Carbon::now()->subDays(60),
                'submission_deadline' => Carbon::now()->subDays(10)->setTime(14, 0),
                'status'              => 'won',
                'priority'            => 'medium',
                'description'         => 'Rehabilitasi total jembatan rangka baja sepanjang 120 meter. Termasuk pekerjaan pondasi, deck, railing, dan pengaspalan.',
                'requirements'        => 'Pengalaman pekerjaan jembatan. Sub-kontraktor khusus baja.',
                'notes'               => 'Kontrak sudah ditandatangani 3 hari lalu.',
                'no_bid_reason'       => null,
                'lost_reason'         => null,
            ],
            [
                'client'              => $clients[3],
                'category_code'       => 'REN',
                'title'               => 'Renovasi Hotel Graha Utama - Wing Selatan',
                'source'              => 'Direct',
                'source_reference'    => 'REF-GHU-2026-008',
                'pic'                 => $pic4,
                'location'            => 'Surabaya',
                'estimated_value'     => 8200000000,
                'received_date'       => Carbon::now()->subDays(45),
                'submission_deadline' => Carbon::now()->subDays(15)->setTime(16, 0),
                'status'              => 'lost',
                'priority'            => 'medium',
                'description'         => 'Renovasi wing selatan hotel bintang 4 meliputi 80 kamar, koridor, lobby, dan restoran. Pekerjaan harus dilakukan bertahap agar operasional hotel tidak terganggu.',
                'requirements'        => 'Pengalaman renovasi hotel. Jaminan tidak mengganggu operasional.',
                'notes'               => 'Kalah dari kompetitor dengan selisih harga 5%.',
                'no_bid_reason'       => null,
                'lost_reason'         => 'Penawaran harga lebih tinggi 5% dari pemenang. Kompetitor menawarkan Rp 7.790.000.000.',
            ],
            [
                'client'              => $clients[4],
                'category_code'       => 'MEP',
                'title'               => 'Pemasangan Sistem MEP Pabrik Bandung Industrial Park',
                'source'              => 'Direct',
                'source_reference'    => 'REF-BIP-2026-022',
                'pic'                 => $pic5,
                'location'            => 'Bandung',
                'estimated_value'     => 6800000000,
                'received_date'       => Carbon::now()->subDays(10),
                'submission_deadline' => Carbon::now()->addDays(25)->setTime(16, 0),
                'status'              => 'new',
                'priority'            => 'medium',
                'description'         => 'Instalasi sistem MEP (Mechanical, Electrical, Plumbing) untuk pabrik manufaktur seluas 15.000 m². Meliputi HVAC, fire protection, dan sistem kelistrikan 3 phase.',
                'requirements'        => 'Memiliki tenaga ahli MEP bersertifikat. Pengalaman proyek industri.',
                'notes'               => 'Klien meminta presentasi teknis sebelum memasukkan dokumen.',
                'no_bid_reason'       => null,
                'lost_reason'         => null,
            ],
        ];

        $tenders = [];
        foreach ($tendersData as $i => $data) {
            $cat = $categories->get($data['category_code']);

            $tender = Tender::firstOrCreate(
                ['code' => sprintf('TR-%d-%04d', now()->year, $i + 1)],
                [
                    'client_id'           => $data['client']->id,
                    'category_id'         => $cat?->id ?? TenderCategory::first()->id,
                    'code'                => sprintf('TR-%d-%04d', now()->year, $i + 1),
                    'title'               => $data['title'],
                    'description'         => $data['description'],
                    'source'              => $data['source'],
                    'source_reference'    => $data['source_reference'],
                    'pic_id'              => $data['pic']->id,
                    'location'            => $data['location'],
                    'estimated_value'     => $data['estimated_value'],
                    'received_date'       => $data['received_date'],
                    'submission_deadline' => $data['submission_deadline'],
                    'status'              => $data['status'],
                    'priority'            => $data['priority'],
                    'requirements'        => $data['requirements'],
                    'notes'               => $data['notes'],
                    'no_bid_reason'       => $data['no_bid_reason'],
                    'lost_reason'         => $data['lost_reason'],
                    'created_by'          => $admin->id,
                    'updated_by'          => $admin->id,
                ]
            );

            $tenders[] = $tender;

            // Status history untuk setiap tender
            if (TenderStatusHistory::where('tender_id', $tender->id)->doesntExist()) {
                TenderStatusHistory::create([
                    'tender_id'   => $tender->id,
                    'from_status' => null,
                    'to_status'   => 'new',
                    'notes'       => 'Tender diterima dan diregistrasi.',
                    'changed_by'  => $admin->id,
                    'created_at'  => $data['received_date'],
                ]);

                if ($data['status'] !== 'new') {
                    TenderStatusHistory::create([
                        'tender_id'   => $tender->id,
                        'from_status' => 'new',
                        'to_status'   => $data['status'],
                        'notes'       => 'Status diperbarui sesuai perkembangan tender.',
                        'changed_by'  => $data['pic']->id,
                        'created_at'  => $data['received_date']->copy()->addDays(5),
                    ]);
                }
            }
        }

        // ==========================================
        // PROPOSALS (5 dummy)
        // ==========================================
        $proposalsData = [
            [
                'tender'          => $tenders[0],
                'title'           => 'Penawaran Teknis & Komersial - Nusantara Tower',
                'pic'             => $pic1,
                'status'          => 'internal_review',
                'bid_value'       => 43500000000,
                'deadline'        => Carbon::now()->addDays(12),
                'valid_until'     => Carbon::now()->addDays(42),
                'description'     => 'Proposal teknis mencakup metode pelaksanaan, jadwal proyek, dan RAB detail. Dibuat berdasarkan dokumen RKS yang diterima tanggal 1 Juli 2026.',
                'notes'           => 'Menunggu review dari Direktur Teknik sebelum dikirim.',
                'version_notes'   => 'Draft pertama, belum final.',
            ],
            [
                'tender'          => $tenders[0],
                'title'           => 'Penawaran Alternatif - Nusantara Tower (Value Engineering)',
                'pic'             => $pic2,
                'status'          => 'draft',
                'bid_value'       => 41800000000,
                'deadline'        => Carbon::now()->addDays(12),
                'valid_until'     => Carbon::now()->addDays(42),
                'description'     => 'Proposal alternatif dengan pendekatan value engineering untuk menurunkan biaya material tanpa mengurangi kualitas.',
                'notes'           => 'Opsi cadangan jika proposal utama terlalu tinggi.',
                'version_notes'   => 'Versi awal, masih dalam pengerjaan.',
            ],
            [
                'tender'          => $tenders[2],
                'title'           => 'Penawaran Rehabilitasi Jembatan Cisadane Paket 3',
                'pic'             => $pic3,
                'status'          => 'won',
                'bid_value'       => 12400000000,
                'deadline'        => Carbon::now()->subDays(12),
                'valid_until'     => Carbon::now()->addDays(18),
                'submitted_at'    => Carbon::now()->subDays(13),
                'description'     => 'Penawaran lengkap termasuk dokumen administrasi, teknis, dan harga. Memenangkan tender dengan penawaran terbaik secara teknis dan komersial.',
                'notes'           => 'SPMK sudah diterima, mobilisasi mulai minggu depan.',
                'version_notes'   => 'Versi final yang disubmit.',
            ],
            [
                'tender'          => $tenders[3],
                'title'           => 'Penawaran Renovasi Hotel Graha Utama',
                'pic'             => $pic4,
                'status'          => 'lost',
                'bid_value'       => 8050000000,
                'deadline'        => Carbon::now()->subDays(17),
                'valid_until'     => Carbon::now()->subDays(2),
                'submitted_at'    => Carbon::now()->subDays(18),
                'description'     => 'Penawaran renovasi hotel mencakup pekerjaan sipil, MEP, dan interior. Menggunakan material premium sesuai standar bintang 4.',
                'notes'           => 'Kalah tipis dari kompetitor, perlu evaluasi strategi harga.',
                'version_notes'   => 'Versi final yang disubmit.',
            ],
            [
                'tender'          => $tenders[1],
                'title'           => 'Penawaran Grand Residence Phase 2 - Dokumen Kualifikasi',
                'pic'             => $pic2,
                'status'          => 'draft',
                'bid_value'       => 27500000000,
                'deadline'        => Carbon::now()->addDays(3),
                'valid_until'     => Carbon::now()->addDays(33),
                'description'     => 'Dokumen kualifikasi untuk tender perumahan Grand Residence Phase 2. Meliputi profil perusahaan, pengalaman, dan kapasitas teknis.',
                'notes'           => 'Perlu melengkapi dokumen SIUJK terbaru.',
                'version_notes'   => 'Draft kualifikasi pertama.',
            ],
        ];

        $proposals = [];
        foreach ($proposalsData as $i => $data) {
            $code = sprintf('PR-%d-%04d', now()->year, $i + 1);

            $proposal = Proposal::firstOrCreate(
                ['code' => $code],
                [
                    'tender_id'       => $data['tender']->id,
                    'code'            => $code,
                    'title'           => $data['title'],
                    'pic_id'          => $data['pic']->id,
                    'description'     => $data['description'],
                    'bid_value'       => $data['bid_value'],
                    'status'          => $data['status'],
                    'current_version' => 1,
                    'deadline'        => $data['deadline'],
                    'valid_until'     => $data['valid_until'],
                    'submitted_at'    => $data['submitted_at'] ?? null,
                    'notes'           => $data['notes'],
                    'created_by'      => $data['pic']->id,
                    'updated_by'      => $data['pic']->id,
                ]
            );

            $proposals[] = $proposal;

            // Versi proposal
            if (ProposalVersion::where('proposal_id', $proposal->id)->doesntExist()) {
                ProposalVersion::create([
                    'proposal_id'    => $proposal->id,
                    'version_number' => 1,
                    'change_notes'   => $data['version_notes'],
                    'created_by'     => $data['pic']->id,
                    'created_at'     => now()->subDays(rand(1, 10)),
                ]);
            }
        }

        // ==========================================
        // REMINDERS (5 dummy)
        // ==========================================
        $remindersData = [
            [
                'tender'      => $tenders[0],
                'proposal'    => $proposals[0],
                'user'        => $pic1,
                'title'       => 'Deadline Submission Tender Nusantara Tower',
                'description' => 'Pastikan seluruh dokumen sudah lengkap dan ditandatangani sebelum deadline.',
                'reminder_at' => Carbon::now()->addDays(13)->setTime(8, 0),
                'type'        => 'deadline',
                'status'      => 'pending',
            ],
            [
                'tender'      => $tenders[1],
                'proposal'    => null,
                'user'        => $pic2,
                'title'       => 'Meeting Presentasi Teknis - Grand Residence',
                'description' => 'Presentasi kepada tim teknis PT. Karya Agung Persada.',
                'reminder_at' => Carbon::now()->addDays(2)->setTime(9, 0),
                'type'        => 'meeting',
                'status'      => 'pending',
            ],
            [
                'tender'      => $tenders[0],
                'proposal'    => null,
                'user'        => $pic1,
                'title'       => 'Follow Up Klarifikasi Dokumen - Nusantara Tower',
                'description' => 'Menghubungi client untuk klarifikasi item pekerjaan di BOQ item 3.5.',
                'reminder_at' => Carbon::now()->addDays(1)->setTime(10, 0),
                'type'        => 'follow_up',
                'status'      => 'pending',
            ],
            [
                'tender'      => $tenders[2],
                'proposal'    => $proposals[2],
                'user'        => $pic3,
                'title'       => 'Mobilisasi Proyek Jembatan Cisadane',
                'description' => 'Persiapan mobilisasi alat dan tenaga kerja ke lokasi proyek.',
                'reminder_at' => Carbon::now()->addDays(7)->setTime(7, 0),
                'type'        => 'custom',
                'status'      => 'pending',
            ],
            [
                'tender'      => $tenders[4],
                'proposal'    => null,
                'user'        => $pic5,
                'title'       => 'Deadline Pemasukan Dokumen MEP Pabrik Bandung',
                'description' => 'Deadline submission dokumen penawaran proyek MEP Industrial Park.',
                'reminder_at' => Carbon::now()->addDays(24)->setTime(15, 0),
                'type'        => 'deadline',
                'status'      => 'pending',
            ],
        ];

        foreach ($remindersData as $data) {
            Reminder::firstOrCreate(
                [
                    'tender_id' => $data['tender']->id,
                    'user_id'   => $data['user']->id,
                    'title'     => $data['title'],
                ],
                [
                    'proposal_id' => $data['proposal']?->id,
                    'description' => $data['description'],
                    'reminder_at' => $data['reminder_at'],
                    'type'        => $data['type'],
                    'status'      => $data['status'],
                ]
            );
        }

        // ==========================================
        // ACTIVITY LOGS (5 dummy)
        // ==========================================
        $activityData = [
            [
                'user'        => $admin,
                'action'      => 'CREATE_TENDER',
                'module'      => 'Tender',
                'subject_id'  => $tenders[0]->id,
                'description' => 'Membuat tender baru: ' . $tenders[0]->title,
                'properties'  => ['code' => $tenders[0]->code, 'status' => 'new'],
                'ip_address'  => '192.168.1.100',
                'created_at'  => Carbon::now()->subDays(30),
            ],
            [
                'user'        => $pic2,
                'action'      => 'CREATE_PROPOSAL',
                'module'      => 'Proposal',
                'subject_id'  => $proposals[4]->id,
                'description' => 'Membuat proposal: ' . $proposals[4]->title,
                'properties'  => ['code' => $proposals[4]->code, 'tender_id' => $tenders[1]->id],
                'ip_address'  => '192.168.1.102',
                'created_at'  => Carbon::now()->subDays(15),
            ],
            [
                'user'        => $pic3,
                'action'      => 'STATUS_CHANGE_TENDER',
                'module'      => 'Tender',
                'subject_id'  => $tenders[2]->id,
                'description' => 'Mengubah status tender: new → won',
                'properties'  => ['old_status' => 'submitted', 'new_status' => 'won'],
                'ip_address'  => '192.168.1.103',
                'created_at'  => Carbon::now()->subDays(10),
            ],
            [
                'user'        => $pic4,
                'action'      => 'UPDATE_PROPOSAL',
                'module'      => 'Proposal',
                'subject_id'  => $proposals[3]->id,
                'description' => 'Memperbarui proposal: ' . $proposals[3]->title,
                'properties'  => ['old_status' => 'submitted', 'new_status' => 'lost'],
                'ip_address'  => '192.168.1.104',
                'created_at'  => Carbon::now()->subDays(14),
            ],
            [
                'user'        => $admin,
                'action'      => 'CREATE_CLIENT',
                'module'      => 'Client',
                'subject_id'  => $clients[0]->id,
                'description' => 'Membuat klien baru: ' . $clients[0]->name,
                'properties'  => ['code' => $clients[0]->code],
                'ip_address'  => '192.168.1.100',
                'created_at'  => Carbon::now()->subDays(35),
            ],
        ];

        foreach ($activityData as $data) {
            ActivityLog::create([
                'user_id'      => $data['user']->id,
                'action'       => $data['action'],
                'module'       => $data['module'],
                'subject_type' => null,
                'subject_id'   => $data['subject_id'],
                'description'  => $data['description'],
                'properties'   => $data['properties'],
                'ip_address'   => $data['ip_address'],
                'user_agent'   => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at'   => $data['created_at'],
            ]);
        }

        $this->command->info('✅ Dummy data berhasil dibuat:');
        $this->command->info('   • 5 Users tambahan');
        $this->command->info('   • 5 Clients');
        $this->command->info('   • 5 Tenders (dengan status history)');
        $this->command->info('   • 5 Proposals (dengan versi)');
        $this->command->info('   • 5 Reminders');
        $this->command->info('   • 5 Activity Logs');
    }
}
