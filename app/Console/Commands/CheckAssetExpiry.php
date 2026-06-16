<?php

namespace App\Console\Commands;

use App\Models\Asset;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CheckAssetExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-asset-expiry';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengecek aset yang EOL atau Langganan-nya akan habis dalam 7 hari dan mengirimkan notifikasi Filament.';
    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Target tanggal = 7 hari dari sekarang
        $targetDate = Carbon::now()->addDays(7)->toDateString();

        // Cari aset yang EOL atau subscription_expiry-nya persis 7 hari lagi
        $expiringAssets = Asset::whereDate('eol_date', $targetDate)
            ->orWhereDate('subscription_expiry', $targetDate)
            ->get();

        if ($expiringAssets->isEmpty()) {
            $this->info('Tidak ada aset yang kedaluwarsa dalam 7 hari.');
            return;
        }

        // Tentukan siapa yang akan menerima notifikasi.
        // Contoh ini mengambil semua user. Anda bisa memfilter misalnya hanya Admin:
        // $usersToNotify = User::whereHas('roles', fn($q) => $q->where('name', 'Admin'))->get();
        $usersToNotify = User::all();

        foreach ($expiringAssets as $asset) {
            // Cek apakah yang habis itu masa langganan atau umur ekonomis (EOL)
            $isSubscriptionEnding = $asset->is_subscription
                && $asset->subscription_expiry
                && Carbon::parse($asset->subscription_expiry)->toDateString() === $targetDate;

            $reason = $isSubscriptionEnding ? 'Masa Langganan (SaaS)' : 'End of Life (EOL)';
            $date = $isSubscriptionEnding ? $asset->subscription_expiry->format('d M Y') : $asset->eol_date->format('d M Y');

            foreach ($usersToNotify as $user) {
                // Mengirim Notifikasi ke Database Filament
                Notification::make()
                    ->title('Peringatan Aset Kedaluwarsa')
                    ->body("Aset **{$asset->name}** ({$asset->asset_code}) akan mencapai batas **{$reason}** pada **{$date}**.")
                    ->warning()
                    ->icon('heroicon-o-exclamation-triangle')
                    // Opsional: Jika di-klik akan mengarah ke halaman view aset tersebut
                    // ->actions([
                    //     \Filament\Notifications\Actions\Action::make('Lihat Aset')
                    //         ->url(route('filament.admin.resources.assets.view', $asset->id)),
                    // ])
                    ->sendToDatabase($user);
            }
        }

        $this->info("Notifikasi berhasil dikirim untuk {$expiringAssets->count()} aset.");
    }
}
