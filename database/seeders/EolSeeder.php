<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\AssetAttachment;
use App\Models\AssetHistory;
use App\Models\Category;
use App\Models\Department;
use App\Models\Location;
use App\Models\MaintenanceLog;
use App\Models\SoftwareLicenseDetail;
use App\Models\Status;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::create([
            'name' => 'Rizky Apriana',
            'email' => 'admin@kaltimmethanol.co.id',
            'password' => Hash::make('12345678'),
        ]);

        $staffUser = User::create([
            'name' => 'Ahmad Kurniawan',
            'email' => 'staff.it@kaltimmethanol.co.id',
            'password' => Hash::make('12345678'),
        ]);

        $pimpinanUser = User::create([
            'name' => 'Bambang Sugeng',
            'email' => 'pimpinan@kaltimmethanol.co.id',
            'password' => Hash::make('12345678'),
        ]);

        // 2. SEED DATA MASTER: STATUS
        $statusAktif = Status::create(['name' => 'Aktif / Operasional', 'color' => 'green']);
        $statusPerbaikan = Status::create(['name' => 'Dalam Perbaikan', 'color' => 'yellow']);
        $statusEol = Status::create(['name' => 'End of Life (EOL)', 'color' => 'red']);
        $statusRusak = Status::create(['name' => 'Rusak / Afkir', 'color' => 'gray']);


        // 3. SEED DATA MASTER: KATEGORI
        $katServer = Category::create(['name' => 'Server & Infrastruktur', 'code' => 'SRV']);
        $katWorkstation = Category::create(['name' => 'Komputer Kerja', 'code' => 'WKS']);
        $katNetwork = Category::create(['name' => 'Perangkat Jaringan', 'code' => 'NET']);
        $katSoftware = Category::create(['name' => 'Perangkat Lunak', 'code' => 'SFT']);


        // 4. SEED DATA MASTER: LOKASI
        $lokGedungUtama = Location::create(['name' => 'Gedung Kantor Utama (Head Office)']);
        $lokServerRoom = Location::create(['name' => 'Ruang Server Gedung IT']);
        $lokControlRoom = Location::create(['name' => 'Central Control Room (CCR) PPL']);
        $lokLaboratorium = Location::create(['name' => 'Gedung Laboratorium Analisa']);


        // 5. SEED DATA MASTER: DEPARTEMEN
        $deptIT = Department::create(['name' => 'Teknologi Informasi (IT)']);
        $deptProduksi = Department::create(['name' => 'Produksi & Operasi Pabrik']);
        $deptMaintenance = Department::create(['name' => 'Pemeliharaan & Instrumen']);
        $deptFinance = Department::create(['name' => 'Keuangan & Akuntansi']);


        // 6. SEED DATA MASTER: VENDOR
        $vendorA = Vendor::create([
            'name' => 'PT Nusantara Solusi Teknologi',
            'contact_person' => 'Budi Santoso',
            'email' => 'info@nusantaratech.com',
            'phone' => '021-5551234',
            'address' => 'Jl. Jendral Sudirman No. 45, Jakarta Pusat'
        ]);

        $vendorB = Vendor::create([
            'name' => 'CV Borneo Prima Komputindo',
            'contact_person' => 'Hendra Wijaya',
            'email' => 'sales@borneokomputindo.co.id',
            'phone' => '0548-22344',
            'address' => 'Jl. Mulawarman No. 12, Bontang, Kalimantan Timur'
        ]);


        // ==========================================
        // 7. SEED DATA INVENTARIS ASET & RELASINYA
        // ==========================================

        // --- ASET 1: SERVER PABRIK (Mendekati EOL) ---
        $serverAset = Asset::create([
            'asset_code' => 'KMI-SRV-2021-001',
            'name' => 'Server Core Database SCADA',
            'category_id' => $katServer->id,
            'vendor_id' => $vendorA->id,
            'brand' => 'Dell PowerEdge',
            'model_number' => 'R750',
            'serial_number' => 'DL-SCADA-99812A',
            'asset_type' => 'Hardware',
            'purchase_date' => '2021-06-15',
            'purchase_cost' => 150000000.00,
            'useful_life_years' => 5,
            'eol_date' => '2026-06-15', // EOL Bulan depan (Sangat kritis)
            'is_subscription' => false,
            'status_id' => $statusAktif->id,
            'location_id' => $lokServerRoom->id,
            'department_id' => $deptProduksi->id,
            'is_critical' => true,
            'description' => 'Server utama penampung data telemetri produksi methanol di control room.',
        ]);

        // Attachment untuk Aset 1
        AssetAttachment::create([
            'asset_id' => $serverAset->id,
            'title' => 'Sertifikat Garansi & Spesifikasi Teknis Dell',
            'file_path' => 'attachments/dokumen_dell_r750_signed.pdf',
            'file_type' => 'pdf'
        ]);

        // Log Pemeliharaan Aset 1
        MaintenanceLog::create([
            'asset_id' => $serverAset->id,
            'maintenance_date' => '2025-12-10',
            'maintenance_type' => 'Perawatan Rutin',
            'description' => 'Pembersihan debu internal, pengecekan harddisk RAID 5, dan penggantian pasta prosesor.',
            'cost' => 2500000.00,
            'performed_by' => 'Tim IT KMI & Teknisi Dell',
            'next_maintenance_date' => '2026-06-10'
        ]);

        // Histori Perubahan Aset 1
        AssetHistory::create([
            'asset_id' => $serverAset->id,
            'user_id' => $staffUser->id,
            'action' => 'Update Status',
            'old_value' => ['status_id' => $statusPerbaikan->id],
            'new_value' => ['status_id' => $statusAktif->id],
        ]);


        // --- ASET 2: LISENSI OPERATING SYSTEM (Subscription / Berlangganan) ---
        $osAset = Asset::create([
            'asset_code' => 'KMI-SFT-2025-014',
            'name' => 'Lisensi Red Hat Enterprise Linux Server',
            'category_id' => $katSoftware->id,
            'vendor_id' => $vendorA->id,
            'brand' => 'Red Hat',
            'model_number' => 'RHEL Premium Standard',
            'serial_number' => 'RH-8839-2100-LL9',
            'asset_type' => 'Software',
            'purchase_date' => '2025-07-01',
            'purchase_cost' => 45000000.00,
            'useful_life_years' => 1,
            'eol_date' => '2026-07-01',
            'is_subscription' => true,
            'subscription_expiry' => '2026-07-01', // Expired sebentar lagi
            'status_id' => $statusAktif->id,
            'location_id' => $lokServerRoom->id,
            'department_id' => $deptIT->id,
            'is_critical' => true,
            'description' => 'Sistem operasi dasar untuk server core database pabrik.',
        ]);

        // Detail Lisensi untuk Aset 2
        SoftwareLicenseDetail::create([
            'asset_id' => $osAset->id,
            'license_type' => 'Subscription Tahunan',
            'seats_count' => 2,
            'activated_at' => '2025-07-01',
            'is_active' => true
        ]);


        // --- ASET 3: PC WORKSTATION (Kondisi Aman / Baru) ---
        $pcAset = Asset::create([
            'asset_code' => 'KMI-WKS-2025-089',
            'name' => 'PC Administrasi Keuangan',
            'category_id' => $katWorkstation->id,
            'vendor_id' => $vendorB->id,
            'brand' => 'HP ProDesk',
            'model_number' => '400 G9',
            'serial_number' => 'HP-PRO-8821039A',
            'asset_type' => 'Hardware',
            'purchase_date' => '2025-03-10',
            'purchase_cost' => 14500000.00,
            'useful_life_years' => 5,
            'eol_date' => '2030-03-10', // Masih lama
            'is_subscription' => false,
            'status_id' => $statusAktif->id,
            'location_id' => $lokGedungUtama->id,
            'department_id' => $deptFinance->id,
            'is_critical' => false,
            'description' => 'PC operasional harian untuk staf administrasi keuangan dan perpajakan.',
        ]);

        // Histori Input Pertama untuk Aset 3
        AssetHistory::create([
            'asset_id' => $pcAset->id,
            'user_id' => $adminUser->id,
            'action' => 'Pencatatan Baru',
            'old_value' => null,
            'new_value' => ['asset_code' => 'KMI-WKS-2025-089', 'name' => 'PC Administrasi Keuangan'],
        ]);
    }
}
