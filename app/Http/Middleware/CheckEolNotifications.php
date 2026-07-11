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

            // Gunakan LIKE agar kompatibel dengan semua jenis database (termasuk SQLite)
            $hasUnreadEolNotification = $user->unreadNotifications()
                ->where('data', 'like', '%Peringatan End of Life (EOL)%')
                ->exists();

            if (!$hasUnreadEolNotification) {
                // Cari aset yang eol_date-nya <= 3 bulan ke depan, tapi belum dipensiunkan
                $criticalAssetsCount = Asset::where('eol_date', '<=', now()->addMonths(3))
                    ->whereNotIn('status', ['End of Life', 'Disposed', 'Lost'])
                    ->count();

                if ($criticalAssetsCount > 0) {
                    Notification::make()
                        ->danger()
                        ->icon('heroicon-o-exclamation-triangle')
                        ->title('Peringatan End of Life (EOL)')
                        ->body("Terdapat {$criticalAssetsCount} aset yang sudah mendekati atau melewati batas EOL. Segera lakukan peninjauan!")
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
