<?php

namespace App\Filament\Resources;

use App\Exports\PerizinanExcelExport;
use App\Filament\Resources\PerizinanResource\Pages;
use App\Filament\Resources\PerizinanResource\RelationManagers;
use App\Models\Perizinan;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;

class PerizinanResource extends Resource
{
    protected static ?string $model = Perizinan::class;

    //protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Data Master';
    protected static ?int    $navigationSort = 5;

    protected static ?string $navigationLabel = 'Perizinan';
    protected static ?string $pluralModelLabel = 'Perizinan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('alasan')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('tanggal_keluar')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('tanggal_masuk')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('waktu_keluar'),
                Forms\Components\TextInput::make('waktu_masuk'),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\Textarea::make('keterangan')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('siswa_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Action::make('export-excel')
                    ->label('Export Excel')
                    ->action(function () {
                        $path = PerizinanExcelExport::export();
                        return response()->download($path)->deleteFileAfterSend(true);
                    })
                    ->color('success')
                    ->icon('heroicon-o-arrow-down-tray'),

                Action::make('exportPdf')
                    ->label('Export PDF')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->action(function () {
                        $records = Perizinan::all(); 

                        $pdf = Pdf::loadView('pdf.perizinan', ['records' => $records]);
                        $pdf->setPaper('a4', 'landscape');

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, 'data_perizinan_asrama_' . date('d-m-Y') . '.pdf');
                    }),
                
            ])
            ->columns([
                Tables\Columns\TextColumn::make('siswa.nama_lengkap')
                    ->searchable()
                    ->label('Nama Siswa')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('alasan'),
                Tables\Columns\TextColumn::make('tanggal_keluar')
                    ->label('Tanggal Keluar')
                    ->date('d M Y')
                    ->searchable(),
                Tables\Columns\TextColumn::make('waktu_keluar'),
                Tables\Columns\TextColumn::make('tanggal_masuk')
                    ->label('Tanggal Kembali')
                    ->date('d M Y')
                    ->searchable(),
                Tables\Columns\TextColumn::make('waktu_masuk')
                    ->label('Waktu Kembali'),
                Tables\Columns\TextColumn::make('status')
                    ->sortable(),
                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan Penolakan'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Orang Tua')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('setujui')
                ->label('Setujui')
                ->color('success')
                ->icon('heroicon-o-check')
                ->requiresConfirmation()
                ->modalHeading('Konfirmasi Persetujuan')
                ->modalDescription('Apakah kamu yakin ingin menyetujui perizinan ini?')
                ->modalSubmitActionLabel('Ya, Setujui')
                ->modalCancelActionLabel('Batal')
                ->visible(fn ($record) => $record->status === 'Menunggu')
                ->action(fn ($record) => $record->update(['status' => 'Diterima'])),

                Action::make('tolak')
                ->label('Tolak')
                ->color('danger')
                ->icon('heroicon-o-x-mark')
                ->visible(fn ($record) => in_array($record->status, ['Menunggu']))
                ->form([
                    Textarea::make('keterangan')->label('Alasan Penolakan')->rows(3),
                ])
                ->modalHeading('Tolak Perizinan')
                ->modalSubmitActionLabel('Tolak Sekarang')
                ->action(function ($record, array $data) {
                    $record->update([
                        'status' => 'Ditolak',
                        'keterangan' => $data['keterangan'],
                    ]);
                    Notification::make()->success()->title('Perizinan ditolak')->send();
                }),

                Action::make('konfirmasiKembali')
                ->label('Konfirmasi Kembali')
                ->color('primary')
                ->icon('heroicon-o-arrow-uturn-left')
                // tampil hanya kalau masuknya belum tercatat & izin bukan ditolak
                ->visible(fn ($record) =>
                    is_null($record->tanggal_masuk) &&
                    is_null($record->waktu_masuk) &&
                    in_array($record->status, ['Diterima','Berangkat'])
                )
                ->requiresConfirmation()
                ->modalHeading('Konfirmasi Kepulangan')
                ->modalDescription('Setel tanggal & waktu masuk ke saat ini?')
                ->modalSubmitActionLabel('Konfirmasi')
                ->action(function ($record) {
                    // pakai timezone app, atau set manual:
                    $now = Carbon::now(config('app.timezone', 'Asia/Jakarta'));
                    $record->update([
                        'tanggal_masuk' => $now->toDateString(),   // YYYY-MM-DD
                        'waktu_masuk'   => $now->format('H:i:s'),   // HH:MM:SS
                    ]);

                    Notification::make()
                        ->success()
                        ->title('Kepulangan dikonfirmasi')
                        ->body('Tanggal & waktu masuk sudah diisi otomatis.')
                        ->send();
                }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPerizinans::route('/'),
            'create' => Pages\CreatePerizinan::route('/create'),
            'edit' => Pages\EditPerizinan::route('/{record}/edit'),
        ];
    }
}
