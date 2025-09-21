<?php

namespace App\Filament\Resources;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\SettingResource\Pages;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Einstellungen';
    protected static ?string $navigationGroup = 'System';
    protected static ?int $navigationSort = 90;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Einstellung Details')
                ->schema([
                    Forms\Components\Select::make('group_name')
                        ->label('Gruppe')
                        ->options([
                            'general' => 'Allgemein',
                            'pricing' => 'Preise',
                            'email' => 'E-Mail',
                            'api' => 'API',
                            'ui' => 'Benutzeroberfläche',
                            'moving' => 'Umzug',
                            'cleaning' => 'Reinigung',
                            'decluttering' => 'Entrümpelung'
                        ])
                        ->required()
                        ->native(false)
                        ->searchable(),
                        
                    Forms\Components\TextInput::make('key_name')
                        ->label('Schlüssel')
                        ->required()
                        ->maxLength(100),
                        
                    Forms\Components\Select::make('type')
                        ->label('Typ')
                        ->options([
                            'string' => 'Text',
                            'integer' => 'Ganzzahl',
                            'decimal' => 'Dezimalzahl',
                            'boolean' => 'Ja/Nein',
                            'json' => 'JSON'
                        ])
                        ->required()
                        ->native(false)
                        ->live(),
                        
                    Forms\Components\Textarea::make('value')
                        ->label('Wert')
                        ->rows(3)
                        ->rules(fn (Forms\Get $get) => $get('type') === 'json' ? ['json'] : [])
                        ->visible(fn (Forms\Get $get) => in_array($get('type'), ['string', 'json']))
                        ->helperText(fn (Forms\Get $get) => $get('type') === 'json' ? 'Gültiges JSON-Format erforderlich' : null),
                        
                    Forms\Components\TextInput::make('value')
                        ->label('Wert')
                        ->numeric()
                        ->integer()
                        ->rules(['integer'])
                        ->visible(fn (Forms\Get $get) => $get('type') === 'integer'),
                        
                    Forms\Components\TextInput::make('value')
                        ->label('Wert')
                        ->numeric()
                        ->step(0.01)
                        ->rules(['numeric', 'regex:/^\d+(\.\d{1,2})?$/'])
                        ->visible(fn (Forms\Get $get) => $get('type') === 'decimal'),
                        
                    Forms\Components\Toggle::make('value')
                        ->label('Wert')
                        ->visible(fn (Forms\Get $get) => $get('type') === 'boolean'),
                        
                    Forms\Components\Textarea::make('description')
                        ->label('Beschreibung')
                        ->rows(2),
                        
                    Forms\Components\Toggle::make('is_public')
                        ->label('Öffentlich sichtbar')
                        ->helperText('Kann von der Frontend-API abgerufen werden'),
                ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('group_name')
                    ->label('Gruppe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'general' => 'gray',
                        'pricing' => 'success',
                        'email' => 'info',
                        'api' => 'warning',
                        'ui' => 'primary',
                        'moving' => 'success',
                        'cleaning' => 'info',
                        'decluttering' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'general' => 'Allgemein',
                        'pricing' => 'Preise',
                        'email' => 'E-Mail',
                        'api' => 'API',
                        'ui' => 'Benutzeroberfläche',
                        'moving' => 'Umzug',
                        'cleaning' => 'Reinigung',
                        'decluttering' => 'Entrümpelung',
                        default => ucfirst($state),
                    })
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('key_name')
                    ->label('Schlüssel')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                    
                Tables\Columns\TextColumn::make('value')
                    ->label('Wert')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 50 ? $state : null;
                    })
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->type === 'boolean') {
                            return $state === '1' ? 'Ja' : 'Nein';
                        }
                        if ($record->type === 'decimal' && is_numeric($state)) {
                            return number_format((float)$state, 2, ',', '.') . ' €';
                        }
                        return $state;
                    }),
                    
                Tables\Columns\TextColumn::make('type')
                    ->label('Typ')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'string' => 'gray',
                        'integer' => 'info',
                        'decimal' => 'success',
                        'boolean' => 'warning',
                        'json' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'string' => 'Text',
                        'integer' => 'Ganzzahl',
                        'decimal' => 'Dezimalzahl',
                        'boolean' => 'Ja/Nein',
                        'json' => 'JSON',
                        default => $state,
                    }),
                    
                Tables\Columns\IconColumn::make('is_public')
                    ->label('Öffentlich')
                    ->boolean()
                    ->trueIcon('heroicon-o-eye')
                    ->falseIcon('heroicon-o-eye-slash')
                    ->trueColor('success')
                    ->falseColor('gray'),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Aktualisiert')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('group_name')
                    ->label('Gruppe')
                    ->options([
                        'general' => 'Allgemein',
                        'pricing' => 'Preise',
                        'email' => 'E-Mail',
                        'api' => 'API',
                        'ui' => 'Benutzeroberfläche',
                        'moving' => 'Umzug',
                        'cleaning' => 'Reinigung',
                        'decluttering' => 'Entrümpelung'
                    ])
                    ->multiple(),
                    
                Tables\Filters\SelectFilter::make('type')
                    ->label('Typ')
                    ->options([
                        'string' => 'Text',
                        'integer' => 'Ganzzahl',
                        'decimal' => 'Dezimalzahl',
                        'boolean' => 'Ja/Nein',
                        'json' => 'JSON'
                    ])
                    ->multiple(),
                    
                Tables\Filters\TernaryFilter::make('is_public')
                    ->label('Öffentlich sichtbar')
                    ->placeholder('Alle')
                    ->trueLabel('Nur öffentliche')
                    ->falseLabel('Nur private'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                
                Tables\Actions\Action::make('clear_cache')
                    ->label('Cache leeren')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->action(function (Setting $record): void {
                        // Clear specific setting cache
                        \Illuminate\Support\Facades\Cache::forget("setting.{$record->group_name}.{$record->key_name}");
                        \Illuminate\Support\Facades\Cache::forget("settings.group.{$record->group_name}");
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Cache geleert')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    Tables\Actions\BulkAction::make('clear_all_cache')
                        ->label('Alle Caches leeren')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->action(function (): void {
                            // Clear all settings cache
                            \Illuminate\Support\Facades\Cache::flush();
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Alle Caches geleert')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('group_name');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
