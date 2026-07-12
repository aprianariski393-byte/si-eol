<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Asset;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class CheckEolNotifications
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Cari aset yang eol_date-nya <= 3 bulan ke depan, tapi belum dipensiunkan
            $criticalAssets = Asset::where('eol_date', '<=', now()->addMonths(3))
                ->whereNotIn('status', ['End of Life', 'Disposed', 'Lost'])
                ->get();

            foreach ($criticalAssets as $asset) {
                // Gunakan LIKE agar kompatibel dengan semua jenis database
                $hasUnreadEolNotification = $user->unreadNotifications()
                    ->where('data', 'like', "%Aset {$asset->name} mendekati%")
                    ->exists();

                if (!$hasUnreadEolNotification) {
                    Notification::make()
                        ->danger()
                        ->icon('heroicon-o-exclamation-triangle')
                        ->title('Peringatan End of Life (EOL)')
                        ->body("Aset {$asset->name} mendekati atau melewati batas EOL. Segera lakukan peninjauan!")
                        ->actions([
                            Action::make('Lihat Aset')
                                ->button()
                                ->color('danger')
                                ->url(route('filament.admin.resources.assets.index')),
                        ])
                        ->sendToDatabase($user);
                }
            }
        }

        return $next($request);
    }
}
