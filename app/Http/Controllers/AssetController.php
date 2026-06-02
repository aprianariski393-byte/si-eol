<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    // PERBAIKAN: Tambahkan Request $request di dalam kurung fungsi
    public function cetakPdf(Request $request)
    {
        // Mulai query dasar dengan eager loading relasi
        $query = Asset::with(['category', 'status', 'location', 'department']);

        // Jika dipanggil dari Bulk Action Filament (membawa data baris yang dicentang)
        if ($request->has('ids')) {
            $query->whereIn('id', $request->ids);
        }

        // Ambil data hasil filter ke dalam variabel $assets
        $assets = $query->get();

        // Load view blade dan kirimkan data aset
        $pdf = Pdf::loadView('assets.cetak-pdf', compact('assets'))
            ->setPaper('a4', 'landscape');

        // Unduh langsung file PDF-nya dengan nama tertentu
        return $pdf->download('Laporan_Inventaris_Aset_KMI_' . now()->format('Y-m-d') . '.pdf');
    }

    // 2. Fungsi Cetak Detail Satu Aset
    public function cetakDetailPdf($id)
    {
        $asset = Asset::with(['category', 'vendor', 'status', 'location', 'department', 'maintenanceLogs', 'softwareLicenseDetail'])->findOrFail($id);

        $pdf = Pdf::loadView('assets.cetak-detail-pdf', compact('asset'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('Detail_Aset_' . $asset->asset_code . '.pdf');
    }
}
