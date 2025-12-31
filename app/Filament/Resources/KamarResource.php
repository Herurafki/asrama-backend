<?php

namespace App\Filament\Resources;

use App\Exports\KamarExcelExport;
use App\Filament\Resources\KamarResource\Pages;
use App\Filament\Resources\KamarResource\RelationManagers;
use App\Models\Kamar;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KamarResource extends Resource
{
    protected static ?string $model = Kamar::class;

    //protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = 'Data Master';
    protected static ?int    $navigationSort = 1;

    protected static ?string $navigationLabel = 'Kamar';
    protected static ?string $pluralModelLabel = 'Kamar';
    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_kamar')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('kapasitas')
                    ->required()
                    ->numeric()
                    ->maxLength(255),
                Forms\Components\Select::make('jenis_kelamin')
                    ->options([
                        'Laki-laki' => 'Laki-laki',
                        'Perempuan' => 'Perempuan',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Action::make('export-excel')
                    ->label('Export Excel')
                    ->action(function () {
                        $path = KamarExcelExport::export();
                        return response()->download($path)->deleteFileAfterSend(true);
                    })
                    ->color('success')
                    ->icon('heroicon-o-arrow-down-tray'),

                Action::make('exportPdf')
                    ->label('Export PDF')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->action(function () {
                        $records = Kamar::all(); 

                        $pdf = Pdf::loadView('pdf.kamar', ['records' => $records]);
                        $pdf->setPaper('a4', 'landscape');

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, 'data_kamar_asrama_' . date('d-m-Y') . '.pdf');
                    }),
                
            ])
            ->columns([
                Tables\Columns\TextColumn::make('nama_kamar')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kapasitas')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenis_kelamin')
                    ->label('Keterangan')
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
        return __('Kamar'); 
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKamars::route('/'),
            'create' => Pages\CreateKamar::route('/create'),
            'edit' => Pages\EditKamar::route('/{record}/edit'),
        ];
    }

    
}
