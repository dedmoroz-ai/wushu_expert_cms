<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClubResource\Pages;
use App\Models\Club;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ClubResource extends Resource
{
    protected static ?string $model = Club::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationLabel = 'Клубы';
    protected static ?string $modelLabel = 'Клуб';
    protected static ?string $pluralModelLabel = 'Клубы';
    protected static ?string $navigationGroup = 'Управление';
    protected static ?int $navigationSort = 1;

    // --- СКРЫВАЕМ ОТ СУДЕЙ ---
    public static function shouldRegisterNavigation(): bool
    {
        return ! auth()->user()->isJudge();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Профиль клуба')
                    ->schema([
                        // Загрузка логотипа
                        Forms\Components\FileUpload::make('logo_path')
                            ->label('Логотип')
                            ->image() // Только картинки
                            ->directory('club-logos') // Папка на диске
                            ->columnSpanFull(), // На всю ширину

                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->label('Название клуба')
                            ->maxLength(255),
                        
                        // Город и Регион в одну строку
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('city')
                                    ->label('Город')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('region')
                                    ->label('Регион / Область')
                                    ->maxLength(255),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Отображение логотипа
                Tables\Columns\ImageColumn::make('logo_path')
                    ->label('Лого')
                    ->circular(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->label('Название'),
                
                Tables\Columns\TextColumn::make('city')
                    ->searchable()
                    ->label('Город'),

                Tables\Columns\TextColumn::make('region')
                    ->searchable()
                    ->label('Регион'),

                Tables\Columns\TextColumn::make('athletes_count')
                    ->counts('athletes')
                    ->label('Спортсменов')
                    ->badge()
                    ->color('success'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClubs::route('/'),
            'create' => Pages\CreateClub::route('/create'),
            'edit' => Pages\EditClub::route('/{record}/edit'),
        ];
    }
}
