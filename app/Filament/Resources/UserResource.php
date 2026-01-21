<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users'; // Поменял иконку на "люди"
    protected static ?string $navigationLabel = 'Пользователи';
    protected static ?string $modelLabel = 'Пользователь';
    protected static ?string $pluralModelLabel = 'Пользователи';
    protected static ?string $navigationGroup = 'Управление';
    protected static ?int $navigationSort = 1; // Поднимем повыше в меню

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Данные пользователя')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Имя')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('password')
                            ->label('Пароль')
                            ->password()
                            // Логика пароля: обновляем хеш только если поле заполнено
                            ->dehydrated(fn ($state) => filled($state))
                            // Обязателен только при создании нового пользователя
                            ->required(fn (string $context): bool => $context === 'create'),

                        // --- ВЫБОР КЛУБА (САМОЕ ВАЖНОЕ) ---
                        Forms\Components\Select::make('club_id')
                            ->relationship('club', 'name')
                            ->label('Клуб')
                            ->helperText('Если оставить пустым — пользователь будет полным АДМИНИСТРАТОРОМ')
                            ->searchable()
                            ->preload(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Имя')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable(),

                // Показываем клуб. Если пусто — пишем "Администратор"
                Tables\Columns\TextColumn::make('club.name')
                    ->label('Клуб / Роль')
                    ->placeholder('Администратор') // Текст, если club_id = null
                    ->sortable()
                    ->badge() // Делаем красивым значком
                    ->color(fn ($state) => $state ? 'info' : 'danger'), // Админы - красные, Тренеры - синие

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d.m.Y H:i')
                    ->label('Создан')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Можно добавить фильтр "Показать только админов" или "Показать клуб"
                Tables\Filters\SelectFilter::make('club')
                    ->relationship('club', 'name')
                    ->label('Клуб'),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
