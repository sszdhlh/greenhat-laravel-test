<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\FilamentCustom\Pages\ViewRecord;
use App\Filament\Resources\EmployeeResource;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class ViewEmployee extends ViewRecord
{
    protected static string $resource = EmployeeResource::class;

    protected static ?string $title = 'View Employee';

    public function form(Form $form): Form
    {
        return $form
            ->schema($this->getResource()::getFormViewLayout($this->mainSchema(), []))
            ->model($this->getRecord())
            ->statePath($this->getFormStatePath())
            ->operation('view')
            ->statePath($this->getFormStatePath())
            ->columns($this->hasInlineLabels() ? 1 : 2)
            ->inlineLabel($this->hasInlineLabels());
    }

    protected function mainSchema(): array
    {
        return [
            Section::make('User Authentication Data')
                ->columns(2)
                ->schema([
                    Placeholder::make('first_name')
                        ->label('First Name')
                        ->content(fn ($record) => $record->user?->first_name ?? '—'),
                    Placeholder::make('last_name')
                        ->label('Last Name')
                        ->content(fn ($record) => $record->user?->last_name ?? '—'),
                    Placeholder::make('name')
                        ->label('Full Name (Auto-generated)')
                        ->columnSpanFull()
                        ->content(fn ($record) => $record->user?->name ?? '—'),
                    Placeholder::make('email')
                        ->label('Email')
                        ->content(fn ($record) => $record->user?->email ?? '—'),
                    Placeholder::make('password')
                        ->label('Password (Auto-generated)')
                        ->content(fn ($record) => $record->user && $record->user->password ? 'Set (' . strlen($record->user->password) . ' char hash)' : 'Not set'),
                ]),

            Section::make('Employee Profile Data')
                ->description('Employee profile fields stored in employees table')
                ->columns(2)
                ->schema([
                    Placeholder::make('role')
                        ->label('Role')
                        ->content(fn ($record) => $record->role ?? '—'),

                    Placeholder::make('highest_qualification')
                        ->label('Highest Qualification')
                        ->content(fn ($record) => $record->highest_qualification ?? '—'),

                    Placeholder::make('desired_salary')
                        ->label('Desired Salary')
                        ->content(fn ($record) => $record->desired_salary !== null ? ('$' . number_format((float)$record->desired_salary, 2)) : '—'),

                    Placeholder::make('note')
                        ->label('Notes')
                        ->columnSpanFull()
                        ->content(fn ($record) => $record->note ?: '—'),
                ]),

            Section::make('Email Confirmation Tracking')
                ->description('Email confirmation status')
                ->schema([
                    TextInput::make('confirmation_email_sent_at')
                        ->label('Email Confirmation Sent At')
                        ->disabled()
                        ->placeholder('Not sent yet')
                        ->formatStateUsing(fn ($state) => 
                            $state ? $state->format('M j, Y g:i A') : 'Not sent yet'
                        ),
                ]),
        ];
    }
}
