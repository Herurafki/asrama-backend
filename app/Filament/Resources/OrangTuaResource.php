<?php

namespace App\Filament\Resources;

use App\Exports\OrangtuaExcelExport;
use App\Exports\OrangtuaPdfExport;
use App\Filament\Resources\OrangTuaResource\Pages;
use App\Filament\Resources\OrangTuaResource\RelationManagers;
use App\Models\OrangTua;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrangTuaResource extends Resource
{
    protected static ?string $model = OrangTua::class;

   // protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Data Master';
    protected static ?int    $navigationSort = 3;

    protected static ?string $navigationLabel = 'Orang Tua';
    protected static ?string $pluralModelLabel = 'Orang Tua';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Select::make('tipe_orangtua')
                ->label('Tipe Data Orang Tua')
                ->options([
                    'ayah_ibu' => 'Ayah & Ibu',
                    'wali' => 'Wali',
                ])
                ->reactive()
                ->required(),

            // === Data Ayah ===
            TextInput::make('nama_ayah')
                ->label('Nama Ayah')
                ->maxLength(255)
                ->visible(fn ($get) => $get('tipe_orangtua') === 'ayah_ibu')
                ->required(fn ($get) => $get('tipe_orangtua') === 'ayah_ibu'),

            TextInput::make('pendidikan_ayah')
                ->label('Pendidikan Ayah')
                ->maxLength(100)
                ->visible(fn ($get) => $get('tipe_orangtua') === 'ayah_ibu'),

            TextInput::make('pekerjaan_ayah')
                ->label('Pekerjaan Ayah')
                ->maxLength(100)
                ->visible(fn ($get) => $get('tipe_orangtua') === 'ayah_ibu'),

            // === Data Ibu ===
            TextInput::make('nama_ibu')
                ->label('Nama Ibu')
                ->maxLength(255)
                ->visible(fn ($get) => $get('tipe_orangtua') === 'ayah_ibu')
                ->required(fn ($get) => $get('tipe_orangtua') === 'ayah_ibu'),

            TextInput::make('pendidikan_ibu')
                ->label('Pendidikan Ibu')
                ->maxLength(100)
                ->visible(fn ($get) => $get('tipe_orangtua') === 'ayah_ibu'),

            TextInput::make('pekerjaan_ibu')
                ->label('Pekerjaan Ibu')
                ->maxLength(100)
                ->visible(fn ($get) => $get('tipe_orangtua') === 'ayah_ibu'),

            // === Data Wali ===
            TextInput::make('nama_wali')
                ->label('Nama Wali')
                ->maxLength(255)
                ->visible(fn ($get) => $get('tipe_orangtua') === 'wali')
                ->required(fn ($get) => $get('tipe_orangtua') === 'wali'),

            TextInput::make('pekerjaan_wali')
                ->label('Pekerjaan Wali')
                ->maxLength(100)
                ->visible(fn ($get) => $get('tipe_orangtua') === 'wali'),

            Textarea::make('alamat_wali')
                ->label('Alamat Wali')
                ->rows(3)
                ->visible(fn ($get) => $get('tipe_orangtua') === 'wali'),

            // === Kontak Utama ===
            Textarea::make('alamat')
                ->label('Alamat Rumah')
                ->rows(3)
                ->required(),

            TextInput::make('no_hp')
                ->label('No HP')
                ->tel()
                ->maxLength(15)
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Action::make('export-excel')
                    ->label('Export Excel')
                    ->action(function () {
                        $path = OrangtuaExcelExport::export();
                        return response()->download($path)->deleteFileAfterSend(true);
                    })
                    ->color('success')
                    ->icon('heroicon-o-arrow-down-tray'),

                Action::make('exportPdf')
                    ->label('Export PDF')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->action(function () {
                        $records = OrangTua::all(); 
    
                        $pdf = Pdf::loadView('pdf.orangtua', ['records' => $records]);
                        $pdf->setPaper('a4', 'landscape');
    
                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, 'data_orangtua_siswa_' . date('d-m-Y') . '.pdf');
                    }),
                
            ])
            ->columns([
                Tables\Columns\TextColumn::make('nama_ayah')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pendidikan_ayah')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pekerjaan_ayah')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_ibu')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pendidikan_ibu')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pekerjaan_ibu')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_wali')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pekerjaan_wali')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_hp')
                    ->searchable(),
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
                Tables\Actions\EditAction::make(),
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

    public static function getNavigationLabel(): string
    {
        return __('Orang Tua'); // Mengubah label navigasi menjadi "Orang Tua"
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrangTua::route('/'),
            'create' => Pages\CreateOrangTua::route('/create'),
            'edit' => Pages\EditOrangTua::route('/{record}/edit'),
        ];
    }
}
