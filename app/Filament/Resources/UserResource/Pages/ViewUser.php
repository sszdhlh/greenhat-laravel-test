<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('User Authentication Data')
                    ->description('Stored in users table')
                    ->schema([
                        TextEntry::make('first_name')->label('First Name'),
                        TextEntry::make('last_name')->label('Last Name'),
                        TextEntry::make('name')->label('Full Name (Auto-generated)')->columnSpanFull(),
                        TextEntry::make('email')->label('Email'),
                        TextEntry::make('password')
                            ->label('Password (Auto-generated)')
                            ->formatStateUsing(fn (?string $state) => $state ? 'Set (' . strlen($state) . ' char hash)' : 'Not set')
                            ->helperText('Auto-generated during employee registration'),
                    ])->columns(2),
            ]);
    }
}
