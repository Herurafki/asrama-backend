<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengumumanResource\Pages;
use App\Filament\Resources\PengumumanResource\RelationManagers;
use App\Models\Pengumuman;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PengumumanResource extends Resource
{
    protected static ?string $model = Pengumuman::class;

    protected static ?string $navigationGroup = 'Pengumuman & Berita';
    protected static ?int    $navigationSort = 1;
    protected static ?string $navigationLabel = 'Pengumuman';
    protected static ?string $pluralModelLabel = 'Pengumuman';
    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('judul')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('status')
                ->options([
                    'orangtua' => 'Orang Tua',
                    'publik' => 'Publik',
                ])
                ->required()
                ->native(false),
                Forms\Components\Textarea::make('isi')
                    ->columnSpanFull(),
                Forms\Components\DateTimePicker::make('start_at')
                            ->label('Tampilkan Mulai'),
                            
                Forms\Components\DateTimePicker::make('end_at')
                    ->label('Tampilkan Hingga'),
        
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('judul')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_at')
                    ->label('Tanggal Mulai')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_at')
                    ->label('Tanggal Selesai')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'publik' => 'success',
                    'orangtua' => 'warning',
                })
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                ->label('Terakhir Diubah')
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPengumuman::route('/'),
            'create' => Pages\CreatePengumuman::route('/create'),
            'edit' => Pages\EditPengumuman::route('/{record}/edit'),
        ];
    }
}
