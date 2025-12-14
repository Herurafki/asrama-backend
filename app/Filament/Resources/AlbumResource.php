<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AlbumResource\Pages;
use App\Filament\Resources\AlbumResource\RelationManagers;
use App\Models\Album;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\BulkAction;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AlbumResource extends Resource
{
    protected static ?string $model = Album::class;

    protected static ?string $navigationGroup = 'Pengumuman & Berita';

    protected static ?string $navigationLabel = 'Album';
    protected static ?string $pluralModelLabel = 'Album';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            // BAGIAN 1: DATA UTAMA ALBUM
            Section::make('Informasi Album')
                ->schema([
                    TextInput::make('judul')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => 
                            $operation === 'create' ? $set('slug', Str::slug($state)) : null
                        ),
                        
                    TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->disabled()
                        ->dehydrated()
                        ->unique(Album::class, 'slug', ignoreRecord: true),
                        
                    FileUpload::make('cover')
                        ->label('Cover Album')
                        ->image()
                        ->directory('album-covers')
                        ->required(),
                        
                    Forms\Components\Textarea::make('deskripsi')
                        ->columnSpanFull(),
                ])->columns(2),

            // BAGIAN 2: GALERI FOTO (REPEATER)
            Section::make('Galeri Foto')
                ->description('Upload foto-foto kegiatan untuk album ini')
                ->schema([
                    Repeater::make('gambars')
                        ->relationship()
                        ->schema([
                            
                            FileUpload::make('path')
                                ->label('Foto')
                                ->image()
                                ->directory('galeri')
                                ->required()
                                ->columnSpan(2),
                            
                            // TextInput::make('nama') 
                            //    ->label('Caption/Nama Foto')
                            //    ->placeholder('Kegiatan santri...'),
                                
                        ])
                        ->grid(3)
                        ->defaultItems(1)
                        ->addActionLabel('Tambah Foto Lagi'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('judul')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('cover'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAlbums::route('/'),
            'create' => Pages\CreateAlbum::route('/create'),
            'edit' => Pages\EditAlbum::route('/{record}/edit'),
        ];
    }
}
