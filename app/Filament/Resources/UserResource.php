<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\User\Models\User;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 1;

    protected static bool $shouldRegisterNavigation = true;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('User Authentication Data')
                    ->description('Challenge 01: Store User Authentication Data in users table')
                    ->schema([
                        TextInput::make('first_name')
                            ->label('First Name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('last_name')
                            ->label('Last Name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('name')
                            ->label('Full Name (Auto-generated)')
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('This will be automatically generated from first and last name'),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->required(fn (string $context): bool => $context === 'create')
                            ->dehydrated(fn ($state) => filled($state))
                            ->dehydrateStateUsing(fn ($state) => \Illuminate\Support\Facades\Hash::make($state))
                            ->helperText('For employees, password is auto-generated via API'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('first_name')
                    ->label('First Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('last_name')
                    ->label('Last Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Full Name (Auto-generated)')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('password_status')
                    ->label('Password')
                    ->getStateUsing(fn ($record): string => 
                        !empty($record->password) ? '✅ Set (' . strlen($record->password) . ' chars)' : '❌ Not set'
                    )
                    ->badge()
                    ->color(fn (string $state): string => str_contains($state, '✅') ? 'success' : 'danger'),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('employee.role')
                    ->label('Employee Role')
                    ->badge()
                    ->color('primary')
                    ->placeholder('No employee record'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
