<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StyleResource\Pages;
use App\Models\Style;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StyleResource extends Resource
{
    protected static ?string $model = Style::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles'; // Иконка
    
    protected static ?string $navigationLabel = 'Виды программы'; // Название в меню
    protected static ?string $modelLabel = 'Вид';
    protected static ?string $pluralModelLabel = 'Виды программы';
    protected static ?string $navigationGroup = 'Справочники';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Название вида')
                    ->placeholder('Например: Чанцюань'),
                    
                Forms\Components\Select::make('category')
                    ->options([
                        'taolu' => 'Таолу (Комплексы)',
                        'sanda' => 'Саньда (Поединки)',
                        'traditional' => 'Традиционное ушу',
                    ])
                    ->required()
                    ->default('taolu')
                    ->label('Категория'),

                // --- НОВОЕ ПОЛЕ ДЛЯ СОРТИРОВКИ ---
                Forms\Components\TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->label('Порядок в протоколе')
                    ->helperText('Укажите цифру: 1 - выступают первыми, 2 - вторыми и т.д.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // --- НОВАЯ КОЛОНКА (Чтобы сразу видеть порядок) ---
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('№ пор.')
                    ->sortable()
                    ->alignCenter()
                    ->width(80),

                // Колонка с названием
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->label('Вид'),

                // Колонка с категорией (цветной значок)
                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->colors([
                        'success' => 'taolu',
                        'warning' => 'sanda',
                        'info' => 'traditional',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'taolu' => 'Таолу',
                        'sanda' => 'Саньда',
                        'traditional' => 'Традиционное',
                        default => $state,
                    })
                    ->label('Категория'),
            ])
            ->defaultSort('sort_order', 'asc') // Сразу сортируем таблицу по порядку выступления
            ->filters([
                // Фильтр по категории (справа сверху над таблицей)
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'taolu' => 'Таолу',
                        'sanda' => 'Саньда',
                        'traditional' => 'Традиционное',
                    ])
                    ->label('Категория'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStyles::route('/'),
            'create' => Pages\CreateStyle::route('/create'),
            'edit' => Pages\EditStyle::route('/{record}/edit'),
        ];
    }
}
