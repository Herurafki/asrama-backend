<?php

namespace App\Filament\Resources;

use App\Exports\DonasiExcelExport;
use App\Filament\Resources\DonasiResource\Pages;
use App\Models\Donasi;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Cache;

class DonasiResource extends Resource
{
    protected static ?string $model = Donasi::class;

    protected static ?string $navigationGroup = 'Laporan Donasi';
    protected static ?string $navigationLabel = 'Donasi';
    protected static ?string $pluralModelLabel = 'Donasi';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nama')->required(),
            Forms\Components\TextInput::make('alamat')->required(),
            Forms\Components\TextInput::make('no_hp')->label('Nomor HP')->required(),
            Forms\Components\TextInput::make('tgl_kirim')->label('Tanggal Kirim')->required(),
            Forms\Components\TextInput::make('jumlah')->numeric()->required(),
            Forms\Components\TextInput::make('bukti_tf')->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Action::make('export-excel')
                    ->label('Export Excel')
                    ->action(function () {
                        $path = DonasiExcelExport::export();
                        return response()->download($path)->deleteFileAfterSend(true);
                    })
                    ->color('success')
                    ->icon('heroicon-o-arrow-down-tray'),

                Action::make('exportPdf')
                    ->label('Export PDF')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->action(function () {
                        $records = Donasi::all(); 

                        $pdf = Pdf::loadView('pdf.donasi', ['records' => $records]);
                        $pdf->setPaper('a4', 'landscape');

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, 'data_donasi_asrama_' . date('d-m-Y') . '.pdf');
                    }),
                
            ])
            ->columns([
                Tables\Columns\TextColumn::make('nama')->searchable(),
                Tables\Columns\TextColumn::make('alamat')->searchable(),
                Tables\Columns\TextColumn::make('no_hp')->label('Nomor HP'),
                Tables\Columns\TextColumn::make('tgl_kirim')->label('Tanggal Kirim'),
                Tables\Columns\TextColumn::make('jumlah')->numeric()->sortable(),
                Tables\Columns\ImageColumn::make('bukti_tf')
                    ->label('Bukti Transfer')
                    ->disk('public')
                    ->extraImgAttributes([
                        'class' => 'cursor-pointer object-contain',
                        'onclick' => "window.open(this.src, '_blank')",
                    ]),
            ])

            ->actions([
                Action::make('konfirmasi')
                    ->label('Konfirmasi')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(function ($record) {
                        $confirmed = Cache::get('confirmed_donations', []);
                        return !in_array($record->id, $confirmed);
                    })
                    ->action(function ($record) {
                        $confirmed = Cache::get('confirmed_donations', []);
                        if (!in_array($record->id, $confirmed)) {
                            $confirmed[] = $record->id;
                            Cache::put('confirmed_donations', $confirmed, now()->addDays(7));
                        }

                        Notification::make()
                            ->title('Donasi dikonfirmasi')
                            ->success()
                            ->send();
                    }),

                Action::make('hapus')
                    ->label('Hapus')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(function ($record) {
                        $confirmed = Cache::get('confirmed_donations', []);
                        return !in_array($record->id, $confirmed);
                    })
                    ->action(fn($record) => $record->delete()),
            ])

            ->contentFooter(function () {
                $confirmed = Cache::get('confirmed_donations', []);
                $total = Donasi::whereIn('id', $confirmed)->sum('jumlah');
                $formatted = 'Rp ' . number_format($total, 0, ',', '.');

                return view('filament.custom-footer', ['total' => $formatted]);
            })

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDonasis::route('/'),
        ];
    }

    public static function canEdit($record): bool
    {
        return false;
    }
}
