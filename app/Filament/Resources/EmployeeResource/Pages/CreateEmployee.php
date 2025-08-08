<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\FilamentCustom\Pages\CreateRecord;
use App\Filament\Resources\EmployeeResource;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Employee\Models\Employee;
use Modules\Employee\Events\EmployeeRegistered;

class CreateEmployee extends CreateRecord
{
    protected static string $resource = EmployeeResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema($this->getFormSchema())
            ->model(Employee::class);
    }

    protected function getFormRules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255', 
            'email' => 'required|email|unique:users,email',
            'role' => 'required|string',
            'highest_qualification' => 'nullable|string|max:255',
            'desired_salary' => 'nullable|numeric|min:0',
            'note' => 'nullable|string',
            'code' => 'required|in:ACME',
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('User Information')
                ->description('Basic authentication data for the user account')
                ->columns(2)
                ->schema([
                    TextInput::make('first_name')
                        ->label('First Name')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            $set('name', trim(($get('first_name') ?? '') . ' ' . ($get('last_name') ?? '')));
                        }),

                    TextInput::make('last_name')
                        ->label('Last Name')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            $set('name', trim(($get('first_name') ?? '') . ' ' . ($get('last_name') ?? '')));
                        }),

                    TextInput::make('name')
                        ->label('Full Name (Auto-generated)')
                        ->disabled()
                        ->dehydrated(false)
                        ->helperText('This will be automatically generated from first and last name'),

                    TextInput::make('email')
                        ->label('Email')
                        ->required()
                        ->email()
                        ->unique('users', 'email')
                        ->maxLength(255),

                    TextInput::make('generated_password')
                        ->label('Generated Password')
                        ->default(fn() => Str::random(16))
                        ->disabled()
                        ->dehydrated(false)
                        ->password()
                        ->revealable()
                        ->helperText('This password will be automatically set for the new user account'),
                ]),

            Section::make('Employee Profile')
                ->description('Employee-specific information for the job profile')
                ->columns(2)
                ->schema([
                    Select::make('role')
                        ->label('Role')
                        ->options(Employee::getRoleOptions())
                        ->required()
                        ->helperText('Select the employee role'),

                    TextInput::make('highest_qualification')
                        ->label('Highest Qualification')
                        ->maxLength(255)
                        ->placeholder('e.g., Bachelor\'s Degree, Master\'s Degree')
                        ->helperText('Enter the highest educational qualification'),

                    TextInput::make('desired_salary')
                        ->label('Desired Salary')
                        ->numeric()
                        ->prefix('$')
                        ->minValue(0)
                        ->step(1000)
                        ->placeholder('50000')
                        ->helperText('Enter desired annual salary'),

                    Textarea::make('note')
                        ->label('Notes')
                        ->rows(3)
                        ->columnSpanFull()
                        ->placeholder('Additional notes about the employee')
                        ->helperText('Any additional information about the employee'),
                ]),

            Section::make('Registration Code')
                ->description('Verify ACME registration code')
                ->schema([
                    TextInput::make('code')
                        ->label('ACME Registration Code')
                        ->required()
                        ->placeholder('Enter ACME code')
                        ->helperText('You must enter the correct ACME code to register as an employee')
                        ->rules(['in:ACME'])
                        ->validationMessages([
                            'in' => 'Invalid ACME code. Please enter the correct registration code.',
                        ]),
                ]),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Debug: Log the incoming data
        Log::info('Filament CreateEmployee data:', $data);
        
        // Generate password
        $generatedPassword = Str::random(16);
        
        // Extract user data - all required fields
        $userData = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'], 
            'name' => trim($data['first_name'] . ' ' . $data['last_name']),
            'email' => $data['email'],
            'password' => Hash::make($generatedPassword), // Auto-generated password
        ];
        
        Log::info('Generated password for user:', ['email' => $data['email'], 'password_length' => strlen($generatedPassword)]);
        
        // Create the user first
        $user = \Modules\User\Models\User::create($userData);
        
        Log::info('User created with ID: ' . $user->id . ', password hash length: ' . strlen($user->password));
        
        // Prepare employee data with user_id - handle all Challenge 01 fields
        $employeeData = [
            'user_id' => $user->id,
            'role' => $data['role'], // Required field
            'highest_qualification' => !empty($data['highest_qualification']) ? $data['highest_qualification'] : null,
            'desired_salary' => !empty($data['desired_salary']) ? (float)$data['desired_salary'] : null,
            'note' => !empty($data['note']) ? $data['note'] : null,
        ];
        
        Log::info('Employee data to be created:', $employeeData);
        
        return $employeeData;
    }

    protected function afterCreate(): void
    {
        // Fire the EmployeeRegistered event to trigger email sending
        // This follows the same logic as the API registration
        event(new EmployeeRegistered($this->record));
    }
}
