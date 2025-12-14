<?php

namespace App\Filament\Resources;

use App\Exports\SiswaExcelExport;
use App\Filament\Resources\SiswaResource\Pages;
use App\Filament\Resources\SiswaResource\RelationManagers;
use App\Models\Siswa;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Rap2hpoutre\FastExcel\FastExcel;
use Barryvdh\DomPDF\Facade\Pdf;

class SiswaResource extends Resource
{
    protected static ?string $model = Siswa::class;

   // protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = 'Data Master';
    protected static ?int    $navigationSort = 2;

    protected static ?string $navigationLabel = 'Siswa';
    protected static ?string $pluralModelLabel = 'Siswa';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_lengkap')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nama_panggilan')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nis')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('tempat_lahir')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('tanggal_lahir')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('jenis_kelamin')
                    ->required(),
                Forms\Components\TextInput::make('kewarganegaraan')
                    ->required(),
                Forms\Components\TextInput::make('status_keluarga')
                    ->required(),
                Forms\Components\TextInput::make('status_orangtua'),
                Forms\Components\TextInput::make('anak_ke')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('tgl_masuk')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('kelas')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Orang tua')
                    ->default(null),
                FileUpload::make('foto')
                    ->label('Foto')
                    ->image()
                    ->directory('siswa')  // disimpan di storage/app/public/siswa
                    ->disk('public')      // pastikan pakai disk public
                    ->imagePreviewHeight('140')
                    ->openable()
                    ->downloadable(),
            ]);
    }

    public static function table(Table $table): Table
{
    return $table
        ->headerActions([
            Action::make('export-excel')
                ->label('Export Excel')
                ->action(function () {
                    $path = SiswaExcelExport::export();
                    return response()->download($path)->deleteFileAfterSend(true);
                })
                ->color('success')
                ->icon('heroicon-o-arrow-down-tray'),

            Tables\Actions\Action::make('exportPdf')
                ->label('Export PDF')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->action(function () {
                    $records = Siswa::all(); 

                    $pdf = Pdf::loadView('pdf.siswa', ['records' => $records]);
                    $pdf->setPaper('a4', 'landscape');

                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->output();
                    }, 'data_siswa_asrama_' . date('d-m-Y') . '.pdf');
                }),
        ])
        ->columns([
            Tables\Columns\TextColumn::make('nama_lengkap')->searchable(),
            Tables\Columns\TextColumn::make('nama_panggilan')->searchable(),
            Tables\Columns\TextColumn::make('user.name')->label('Orang tua'),
            Tables\Columns\TextColumn::make('nis')->label('NIS')->searchable(),
            Tables\Columns\TextColumn::make('tempat_lahir')->searchable(),
            Tables\Columns\TextColumn::make('tanggal_lahir')
            ->date('d M Y')
            ->searchable(),
            Tables\Columns\TextColumn::make('jenis_kelamin')->sortable(),
            Tables\Columns\TextColumn::make('kewarganegaraan'),
            Tables\Columns\TextColumn::make('status_keluarga'),
            Tables\Columns\TextColumn::make('status_orangtua'),
            Tables\Columns\TextColumn::make('anak_ke')->searchable(),
            Tables\Columns\TextColumn::make('tgl_masuk')->date()->searchable(),
            Tables\Columns\TextColumn::make('kelas')->searchable(),
            ImageColumn::make('foto')
                ->label('Foto')
                ->disk('public')
                ->width(60)
                ->height(60)
                ->circular(),
            Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSiswas::route('/'),
            'create' => Pages\CreateSiswa::route('/create'),
            'edit' => Pages\EditSiswa::route('/{record}/edit'),
        ];
    }

}
