<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\MaintenanceLog;
use App\Models\User;
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
        // ==========================================
        // SEED DATA INVENTARIS ASET & RELASINYA
        // ==========================================

        // --- ASET 1: SERVER PABRIK (Mendekati EOL) ---
        $serverAset = Asset::create([
            'asset_code' => 'KMI-SRV-2021-001',
            'name' => 'Server Core Database SCADA',
            'category' => 'IT Equipment',
            'brand' => 'Dell PowerEdge',
            'serial_number' => 'DL-SCADA-99812A',
            'purchase_date' => '2021-06-15',
            'eol_date' => '2026-06-15', // EOL Bulan depan (Sangat kritis)
            'status' => 'Active',
            'department' => 'Operations',
            'description' => 'Server utama penampung data telemetri produksi methanol di control room.',
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

        // --- ASET 2: LISENSI OPERATING SYSTEM (Subscription / Berlangganan) ---
        $osAset = Asset::create([
            'asset_code' => 'KMI-SFT-2025-014',
            'name' => 'Lisensi Red Hat Enterprise Linux Server',
            'category' => 'Software',
            'brand' => 'Red Hat',
            'serial_number' => 'RH-8839-2100-LL9',
            'purchase_date' => '2025-07-01',
            'eol_date' => '2026-07-01',
            'status' => 'Active',
            'department' => 'IT',
            'description' => 'Sistem operasi dasar untuk server core database pabrik.',
        ]);

        // --- ASET 3: PC WORKSTATION (Kondisi Aman / Baru) ---
        $pcAset = Asset::create([
            'asset_code' => 'KMI-WKS-2025-089',
            'name' => 'PC Administrasi Keuangan',
            'category' => 'IT Equipment',
            'brand' => 'HP ProDesk',
            'serial_number' => 'HP-PRO-8821039A',
            'purchase_date' => '2025-03-10',
            'eol_date' => '2030-03-10', // Masih lama
            'status' => 'Active',
            'department' => 'Finance',
            'description' => 'PC operasional harian untuk staf administrasi keuangan dan perpajakan.',
        ]);
    }
}
