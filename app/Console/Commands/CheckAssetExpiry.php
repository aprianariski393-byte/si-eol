<?php

namespace App\Console\Commands;

use App\Filament\Resources\Assets\AssetResource;
use App\Models\Asset;
use App\Models\User;
use Filament\Actions\Action;
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
    protected $description = 'Mengecek aset yang EOL atau Langganan-nya akan habis dalam 30, 7, atau 1 hari dan mengirimkan notifikasi Filament.';
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $target30 = $now->copy()->addDays(30)->toDateString();
        $target7 = $now->copy()->addDays(7)->toDateString();
        $target1 = $now->copy()->addDays(1)->toDateString();

        // Cari aset yang EOL atau subscription_expiry-nya persis H-30, H-7, atau H-1
        $expiringAssets = Asset::where(function ($query) use ($target30, $target7, $target1) {
            $query->whereDate('eol_date', $target30)
                ->orWhereDate('eol_date', $target7)
                ->orWhereDate('eol_date', $target1)
                ->orWhereDate('subscription_expiry', $target30)
                ->orWhereDate('subscription_expiry', $target7)
                ->orWhereDate('subscription_expiry', $target1);
        })->get();

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
            $isSubscriptionEnding = false;
            $targetDateStr = '';
            $daysLeft = 0;

            if ($asset->is_subscription && $asset->subscription_expiry) {
                $expiryDate = Carbon::parse($asset->subscription_expiry)->toDateString();
                if (in_array($expiryDate, [$target30, $target7, $target1])) {
                    $isSubscriptionEnding = true;
                    $targetDateStr = $expiryDate;
                    $daysLeft = Carbon::parse($expiryDate)->diffInDays($now->copy()->startOfDay());
                }
            }

            if (!$isSubscriptionEnding && $asset->eol_date) {
                $eolDate = Carbon::parse($asset->eol_date)->toDateString();
                if (in_array($eolDate, [$target30, $target7, $target1])) {
                    $targetDateStr = $eolDate;
                    $daysLeft = Carbon::parse($eolDate)->diffInDays($now->copy()->startOfDay());
                }
            }

            if (empty($targetDateStr)) {
                continue;
            }

            $reason = $isSubscriptionEnding ? 'Masa Langganan (SaaS)' : 'End of Life (EOL)';
            $date = Carbon::parse($targetDateStr)->format('d M Y');

            foreach ($usersToNotify as $user) {
                // Mengirim Notifikasi ke Database Filament
                Notification::make()
                    ->title("Peringatan Aset Kedaluwarsa (H-{$daysLeft})")
                    ->body("Aset **{$asset->name}** ({$asset->asset_code}) akan mencapai batas **{$reason}** dalam **{$daysLeft} hari** (pada {$date}).")
                    ->warning()
                    ->icon('heroicon-o-exclamation-triangle')
                    ->actions([
                        Action::make('Lihat Aset')
                            ->url(class_exists(AssetResource::class) ? AssetResource::getUrl('edit', ['record' => $asset]) : '#'),
                    ])
                    ->sendToDatabase($user);
            }
        }

        $this->info("Notifikasi berhasil dikirim untuk {$expiringAssets->count()} aset.");
    }
}
