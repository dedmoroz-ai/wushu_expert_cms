<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgeGroupResource\Pages;
use App\Models\AgeGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AgeGroupResource extends Resource
{
    protected static ?string $model = AgeGroup::class;

    protected static ?string $navigationIcon = 'heroicon-o-users'; // Иконка
    
    protected static ?string $navigationLabel = 'Возрастные группы';
    protected static ?string $modelLabel = 'Группа';
    protected static ?string $pluralModelLabel = 'Возрастные группы';
    protected static ?string $navigationGroup = 'Справочники';
    protected static ?int $navigationSort = 2;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Название')
                    ->placeholder('Например: Юниоры'),

                Forms\Components\Select::make('gender')
                    ->options([
                        'male' => 'Мужчины / Мальчики',
                        'female' => 'Женщины / Девочки',
                        'mixed' => 'Смешанная',
                    ])
                    ->required()
                    ->label('Пол'),
                
                // --- НОВОЕ ПОЛЕ: ПОРЯДОК СОРТИРОВКИ ---
                Forms\Components\TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->label('Порядок в протоколе')
                    ->helperText('Укажите цифру: 1 - выступают первыми, 2 - вторыми и т.д.'),

                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('min_age')
                            ->numeric()
                            ->required()
                            ->label('Мин. возраст')
                            ->suffix('лет'),
                        
                        Forms\Components\TextInput::make('max_age')
                            ->numeric()
                            ->required()
                            ->label('Макс. возраст')
                            ->suffix('лет'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // --- НОВАЯ КОЛОНКА: № ПОРЯДКА ---
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('№ пор.')
                    ->sortable()
                    ->alignCenter()
                    ->width(80),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->weight('bold')
                    ->label('Название группы'),

                Tables\Columns\TextColumn::make('gender')
                    ->badge()
                    ->colors([
                        'info' => 'male',
                        'danger' => 'female',
                        'success' => 'mixed',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'male' => 'Муж.',
                        'female' => 'Жен.',
                        'mixed' => 'Смеш.',
                        default => $state,
                    })
                    ->label('Пол'),

                Tables\Columns\TextColumn::make('min_age')
                    ->sortable()
                    ->label('От (возраст)'),

                Tables\Columns\TextColumn::make('max_age')
                    ->label('До (возраст)'),
            ])
            ->defaultSort('sort_order', 'asc') // Сортируем таблицу по этому полю
            ->filters([
                Tables\Filters\SelectFilter::make('gender')
                    ->options([
                        'male' => 'Мужчины',
                        'female' => 'Женщины',
                    ])
                    ->label('Пол'),
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
            'index' => Pages\ListAgeGroups::route('/'),
            'create' => Pages\CreateAgeGroup::route('/create'),
            'edit' => Pages\EditAgeGroup::route('/{record}/edit'),
        ];
    }
}
