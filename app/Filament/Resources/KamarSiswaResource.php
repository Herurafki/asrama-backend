<?php

namespace App\Filament\Resources;

use App\Exports\KamarSiswaExcelExport;
use App\Filament\Resources\KamarSiswaResource\Pages;
use App\Filament\Resources\KamarSiswaResource\RelationManagers;
use App\Models\Kamar;
use App\Models\KamarSiswa;
use App\Models\Siswa;
use App\Services\AssignRoomsService;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class KamarSiswaResource extends Resource
{
    protected static ?string $model = KamarSiswa::class;

    //protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Data Master';
    protected static ?int    $navigationSort = 4;

    protected static ?string $navigationLabel = 'Siswa per Kamar';
    protected static ?string $pluralModelLabel = 'Siswa per Kamar';
    protected static ?string $modelLabel = 'Penempatan Kamar';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            // Assign manual satu siswa ke kamar (menciptakan baris aktif)
            Forms\Components\Select::make('siswa_id')
                ->label('Siswa')
                ->searchable()
                ->preload()
                ->options(fn () =>
                    \App\Models\Siswa::whereDoesntHave('kamarAktif')
                        ->orderBy('nama_lengkap')
                        ->pluck('nama_lengkap', 'id')->toArray()
                )
                ->required(),

            Forms\Components\Select::make('kamar_id')
                ->label('Kamar')
                ->searchable()
                ->preload()
                ->options(function () {
                    return \App\Models\Kamar::withCount(['penghuniAktif as terisi'])
                        ->orderBy('nama_kamar')
                        ->get()
                        ->filter(fn ($k) => $k->kapasitas > $k->terisi)
                        ->mapWithKeys(fn ($k) => [
                            $k->id => $k->nama_kamar.' (sisa: '.($k->kapasitas - $k->terisi).')'
                        ])
                        ->toArray();
                })
                ->required(),

            Forms\Components\Textarea::make('keterangan')
                ->label('Keterangan')
                ->rows(2),

            Forms\Components\DateTimePicker::make('tgl_masuk')
                ->label('Tanggal/Jam Masuk')
                ->default(now())
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('tgl_masuk', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('siswa.nama_lengkap')
                ->label('Siswa')
                ->formatStateUsing(fn ($state) => $state ?? '-')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('siswa.jenis_kelamin')
                ->label('Jenis Kelamin')
                ->formatStateUsing(fn ($state) => $state ?? '-')    
                ->badge(),
                Tables\Columns\TextColumn::make('kamar.nama_kamar')->label('Kamar')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('tgl_masuk')->label('Masuk')->date('d M Y')->sortable(),   
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(fn (KamarSiswa $r) => $r->tgl_keluar ? 'Nonaktif' : 'Aktif')
                    ->colors(['success' => 'Aktif', 'secondary' => 'Nonaktif']),
                Tables\Columns\TextColumn::make('keterangan')->label('Keterangan')->searchable()->sortable(),
            ])
            ->filters([
                //
                // Filter kamar: id => nama
                Tables\Filters\SelectFilter::make('kamar_id')
                    ->label('Filter Kamar')
                    ->options(fn () => \App\Models\Kamar::orderBy('nama_kamar')
                        ->pluck('nama_kamar', 'id')->toArray())
                    ->query(function (Builder $q, array $data) {
                        $id = $data['value'] ?? null;           // <<— ambil nilai dari array state
                        return $id ? $q->where('kamar_id', $id) : $q;
                    }),

                // Filter Jenis Kelamin (via siswa) — pakai subquery biar anti “call to siswa() on null”
                Tables\Filters\SelectFilter::make('jk')
                    ->label('Jenis Kelamin')
                    ->options([
                        'Laki-laki' => 'Laki-laki',
                        'Perempuan' => 'Perempuan',
                    ])
                    ->query(function (Builder $q, array $data) {
                        $jk = $data['value'] ?? null;           // <<— ambil nilai dari array state
                        if (!$jk) return $q;

                        return $q->whereIn(
                            'siswa_id',
                            \App\Models\Siswa::where('jenis_kelamin', $jk)->select('id')
                        );
                    }),

                // Ini sudah OK dari awal
                Tables\Filters\TernaryFilter::make('aktif')
                    ->label('Status Aktif')
                    ->trueLabel('Aktif saja')
                    ->falseLabel('Nonaktif saja')
                    ->queries(
                        true: fn (Builder $q) => $q->whereNull('tgl_keluar'),
                        false: fn (Builder $q) => $q->whereNotNull('tgl_keluar'),
                        blank: fn (Builder $q) => $q,
                    ),
            ])
            ->actions([
                // Pindahkan: tutup baris aktif ini lalu buat baris baru di kamar tujuan
                Tables\Actions\Action::make('move')
                    ->label('Pindahkan')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->visible(fn (KamarSiswa $r) => $r->tgl_keluar === null)
                    ->form([
                        Forms\Components\Select::make('kamar_id')
                            ->label('Kamar Tujuan')
                            ->searchable()
                            ->options(function () {
                                return Kamar::withCount(['penghuniAktif as terisi'])->orderBy('nama_kamar')
                                    ->get()
                                    ->filter(fn ($k) => $k->kapasitas > $k->terisi)
                                    ->mapWithKeys(fn ($k) => [$k->id => $k->nama_kamar.' (sisa: '.($k->kapasitas - $k->terisi).')'])
                                    ->toArray();
                            })
                            ->required(),
                        Forms\Components\Textarea::make('keterangan')->label('Keterangan')->rows(2),
                    ])
                    ->action(function (KamarSiswa $record, array $data) {
                        DB::transaction(function () use ($record, $data) {
                            // tutup baris aktif lama
                            $rec = KamarSiswa::where('id', $record->id)->lockForUpdate()->first();
                            abort_if(!$rec || $rec->tgl_keluar, 422, 'Baris ini sudah nonaktif.');
                            $rec->tgl_keluar = now();
                            $rec->save();

                            // cek kapasitas kamar baru + kunci gender netral
                            $k = Kamar::where('id', $data['kamar_id'])->lockForUpdate()
                                ->withCount(['penghuniAktif as terisi'])->firstOrFail();
                            abort_if($k->terisi >= $k->kapasitas, 422, 'Kamar penuh.');

                            $s = \App\Models\Siswa::findOrFail($record->siswa_id);
                            if (is_null($k->jenis_kelamin)) {
                                $k->jenis_kelamin = $s->jenis_kelamin;
                                $k->save();
                            }

                            KamarSiswa::create([
                                'siswa_id'    => $s->id,
                                'kamar_id'    => $k->id,
                                'tgl_masuk'    => now(),
                                'keterangan'  => $data['keterangan'] ?? null,
                            ]);
                        });
                    }),

                // Keluarkan: set keluar_at sekarang
                Tables\Actions\Action::make('release')
                    ->label('Keluarkan')
                    ->icon('heroicon-o-user-minus')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (KamarSiswa $r) => $r->tgl_keluar === null)
                    ->form([
                        Forms\Components\Textarea::make('keterangan')->label('Keterangan')->rows(2),
                    ])
                    ->action(function (KamarSiswa $record, array $data) {
                        DB::transaction(function () use ($record, $data) {
                            $rec = KamarSiswa::where('id', $record->id)->lockForUpdate()->first();
                            abort_if(!$rec || $rec->tgl_keluar, 422, 'Baris ini sudah nonaktif.');
                            $rec->tgl_keluar = now();
                            if (!empty($data['keterangan'])) $rec->keterangan = $data['keterangan'];
                            $rec->save();
                        });
                    }),
            ])
            ->headerActions([
                // // Assign manual cepat dari header (tanpa masuk ke Create page)
                Tables\Actions\CreateAction::make()
                    ->label('Assign Manual'),

                // Batch otomatis
                Tables\Actions\Action::make('lockAndDistribute')
                    ->label('Kunci & Bagi Kamar')
                    ->icon('heroicon-o-lock-closed')
                    ->requiresConfirmation()
                    ->action(fn () => app(AssignRoomsService::class)->lockAndDistribute())
                    ->successNotificationTitle('Distribusi berhasil'),

                Action::make('export-excel')
                    ->label('Export Excel')
                    ->action(function () {
                        $path = KamarSiswaExcelExport::export();
                        return response()->download($path)->deleteFileAfterSend(true);
                    })
                    ->color('success')
                    ->icon('heroicon-o-arrow-down-tray'),

                Action::make('exportPdf')
                    ->label('Export PDF')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->action(function () {
                        $records = KamarSiswa::query()
                        ->orderBy('kamar_id', 'desc')
                        ->orderBy('siswa_id', 'asc')
                        ->get(); 

                        $pdf = Pdf::loadView('pdf.kamarsiswa', ['records' => $records]);
                        $pdf->setPaper('a4', 'landscape');

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, 'data_siswa_per_kamar_' . date('d-m-Y') . '.pdf');
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
            'index' => Pages\ListKamarSiswas::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['siswa', 'kamar']);
    }
}
