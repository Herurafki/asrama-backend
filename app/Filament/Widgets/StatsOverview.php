<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Siswa;
use App\Models\Kamar;
use App\Models\Donasi;
use App\Models\Perizinan;
use Illuminate\Support\Facades\Cache;

class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '10s'; // auto refresh tiap 10 detik
    protected int|string|array $columnSpan = 12;

    protected function getStats(): array
    {
        $confirmedIds = Cache::get('confirmed_donations', []);
        if (empty($confirmedIds)) {
            $totalDonasi = 0; 
        } else {
            $totalDonasi = Donasi::whereIn('id', $confirmedIds)->sum('jumlah');
        }

        $formatted = 'Rp ' . number_format($totalDonasi, 0, ',', '.');

        return [
            Card::make('Total Siswa', Siswa::count())
                ->description('Penghuni aktif asrama')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),

            Card::make('Izin yang belum disetujui', Perizinan::where('status', 'Menunggu')->count())
                ->description('Pengajuan Izin Keluar')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('info'),

            Card::make('Total Kamar', Kamar::count())
                ->description('Kamar terisi & kosong')
                ->descriptionIcon('heroicon-m-home-modern')
                ->color('warning'),

            Card::make('Total Donasi', $formatted)
                ->description('Dana terkumpul')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),
        ];
    }
}
