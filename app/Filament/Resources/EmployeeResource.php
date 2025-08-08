<?php

namespace App\Filament\Resources;

use App\Filament\FilamentCustom\Tables\Actions\ViewAction;
use App\Filament\Resources\EmployeeResource\Pages;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\Employee\Models\Employee;
use App\Filament\Resources\UserResource;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 2;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getRecordTitleAttribute(): ?string
    {
        return 'userName';
    }

    public static function getFormViewLayout(array $mainSchema, ?array $sidebar = null): array
    {
        $schema = [
            Grid::make()
                ->schema([
                    Section::make()
                        ->schema($mainSchema)
                        ->columns(),
                ])
                ->columns(2)
                ->columnSpan(['lg' => 2]),
        ];

        if (is_null($sidebar)) {
            $sidebar = static::sidebarSchema();
        }

        if (!empty($sidebar)) {
            $schema[] = Grid::make()
                ->schema($sidebar)
                ->columnSpan(['lg' => 1]);
        }

        return [
            Grid::make()
                ->schema($schema)
                ->columns(empty($sidebar) ? 1 : 3),
        ];
    }

    public static function sidebarSchema(): array
    {
        return [
            Section::make('Linked User Account')
                ->description('User authentication data stored in users table')
                ->schema([
                    Placeholder::make('linkedUser')
                        ->label('User Account')
                        ->inlineLabel()
                        ->content(fn (Employee $record): string => 
                            $record->user->name . ' (' . $record->user->email . ')'
                        ),
                ]),
            
            Section::make('Employee Profile Data')
                ->description('Challenge 01: Employee-specific data stored in employees table')
                ->schema([
                    Placeholder::make('employeeRole')
                        ->label('Role')
                        ->inlineLabel()
                        ->content(fn (Employee $record): string => $record->role),

                    Placeholder::make('highestQualification')
                        ->label('Highest Qualification')
                        ->inlineLabel()
                        ->content(fn (Employee $record): string => $record->highest_qualification ?? 'Not specified'),

                    Placeholder::make('desiredSalary')
                        ->label('Desired Salary')
                        ->inlineLabel()
                        ->content(function (Employee $record): string {
                            if ($record->desired_salary) {
                                return '$' . number_format($record->desired_salary, 2);
                            }
                            return 'Not specified';
                        }),

                    Placeholder::make('note')
                        ->label('Notes')
                        ->inlineLabel()
                        ->content(fn (Employee $record): string => $record->note ?? 'No notes'),
                ]),

            Section::make('Email Confirmation Tracking')
                ->description('Challenge 01: Track Email Confirmation')
                ->schema([
                    Placeholder::make('confirmationEmailSent')
                        ->label('Email Status')
                        ->inlineLabel()
                        ->content(function (Employee $record): string {
                            if ($record->confirmation_email_sent_at) {
                                return '✅ Sent on ' . $record->confirmation_email_sent_at->format('M j, Y g:i A');
                            }
                            return '❌ Not sent yet';
                        }),
                ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('Employee ID')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('User Name')
                    ->searchable()
                    ->sortable()
                    ->url(fn (Employee $record): string => UserResource::getUrl('view', ['record' => $record->user]))
                    ->tooltip('Click to view user details'),

                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('role')
                    ->badge()
                    ->color('primary'),

                TextColumn::make('highest_qualification')
                    ->label('Qualification')
                    ->placeholder('Not specified')
                    ->wrap(),

                TextColumn::make('desired_salary')
                    ->label('Desired Salary')
                    ->money('USD')
                    ->placeholder('Not specified'),

                TextColumn::make('note')
                    ->label('Notes')
                    ->limit(30)
                    ->placeholder('No notes')
                    ->tooltip(fn (Employee $record): string => $record->note ?? 'No notes'),

                TextColumn::make('confirmation_email_sent_at')
                    ->label('Email Sent')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Not sent')
                    ->tooltip('When the registration confirmation email was sent'),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Role')
                    ->options(Employee::getRoleOptions()),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([

            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewEmployee::class,
            Pages\EditEmployeeProfile::class,
            Pages\ViewEmployeeAuthenticationLogs::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'               => Pages\ListEmployees::route('/'),
            'create'              => Pages\CreateEmployee::route('/create'),
            'view'                => Pages\ViewEmployee::route('/{record}'),
            'edit-profile'        => Pages\EditEmployeeProfile::route('/{record}/profile'),
            'authentication-logs' => Pages\ViewEmployeeAuthenticationLogs::route('/{record}/authentication-logs'),
        ];
    }
}
