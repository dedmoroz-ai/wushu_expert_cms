<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FederationResource\Pages;
use App\Models\Federation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FederationResource extends Resource
{
    protected static ?string $model = Federation::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag'; // Поменял иконку на флаг
    
    protected static ?string $navigationLabel = 'Федерации';
    protected static ?string $modelLabel = 'Федерация';
    protected static ?string $pluralModelLabel = 'Федерации';
    protected static ?string $navigationGroup = 'Управление';

    // --- СКРЫВАЕМ ОТ СУДЕЙ ---
    public static function shouldRegisterNavigation(): bool
    {
        return ! auth()->user()->isJudge();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основная информация')
                    ->schema([
                        // Загрузка логотипа
                        Forms\Components\FileUpload::make('logo_path')
                            ->label('Логотип')
                            ->image() // Только картинки
                            ->directory('federation-logos') // Папка для сохранения
                            ->columnSpanFull(), // На всю ширину

                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Название федерации'),

                        Forms\Components\TextInput::make('city')
                            ->maxLength(255)
                            ->label('Город'),

                        Forms\Components\TextInput::make('president')
                            ->maxLength(255)
                            ->label('Президент'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Отображение логотипа в таблице
                Tables\Columns\ImageColumn::make('logo_path')
                    ->label('Лого')
                    ->circular(), // Круглое изображение

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->label('Название'),

                Tables\Columns\TextColumn::make('city')
                    ->searchable()
                    ->label('Город'),

                Tables\Columns\TextColumn::make('president')
                    ->searchable()
                    ->label('Президент'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListFederations::route('/'),
            'create' => Pages\CreateFederation::route('/create'),
            'edit' => Pages\EditFederation::route('/{record}/edit'),
        ];
    }
}
