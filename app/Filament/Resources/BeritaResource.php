<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BeritaResource\Pages;
use App\Filament\Resources\BeritaResource\RelationManagers;
use App\Models\Berita;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BeritaResource extends Resource
{
    protected static ?string $model = Berita::class;

    
    protected static ?string $navigationGroup = 'Pengumuman & Berita';

    protected static ?string $navigationLabel = 'Berita';
    protected static ?string $pluralModelLabel = 'Berita';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('judul')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),

                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->disabled()
                                    ->dehydrated()
                                    ->unique(Berita::class, 'slug', ignoreRecord: true),
                                
                                Forms\Components\Textarea::make('ringkasan')
                                    ->required()
                                    ->columnSpanFull(),

                                Forms\Components\RichEditor::make('isi')
                                    ->required()
                                    ->fileAttachmentsDirectory('berita-images')
                                    ->columnSpanFull(),
                            ])->columns(2),
                        
                    ])->columnSpan(['lg' => 2]),
                
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Status')
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'publish' => 'Publish',
                                    ])
                                    ->required()
                                    ->default('draft'),
                                
                                Forms\Components\DateTimePicker::make('published_at')
                                    ->label('Tanggal Publikasi')
                                    ->default(now()),
                            ]),

                        Forms\Components\Section::make('Author & Cover')
                            ->schema([
                                Forms\Components\FileUpload::make('cover')
                                    ->label('Cover Berita')
                                    ->directory('berita-covers')
                                    ->visibility('public')
                                    ->image()
                                    ->imagePreviewHeight('200')
                                    ->preserveFilenames()
                                    ->maxSize(2048)
                                    ->downloadable()
                                    ->openable(),
                                Forms\Components\Select::make('author_id')
                            ]),
                    ])->columnSpan(['lg' => 1]),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\ImageColumn::make('cover'),
                Tables\Columns\TextColumn::make('judul')
                    ->searchable(),
                Tables\Columns\TextColumn::make('author.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'publish' => 'success',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Tanggal Publikasi')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
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
            'index' => Pages\ListBeritas::route('/'),
            'create' => Pages\CreateBerita::route('/create'),
            'edit' => Pages\EditBerita::route('/{record}/edit'),
        ];
    }
}
