<?php

namespace Z3d0X\FilamentLogger\Resources;

use Filament\Facades\Filament;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;
use Z3d0X\FilamentLogger\Resources\ActivityResource\Pages;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;
    protected static ?string $label = 'Activity Log';
    protected static ?string $slug = 'activity-logs';

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-list';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make([
                    Card::make([
                        TextInput::make('causer_id')
                            ->afterStateHydrated(fn ($component, ?Model $record) => $component->state($record->causer?->name))
                            ->label('User'),

                        TextInput::make('subject_type')
                            ->afterStateHydrated(
                                fn ($component, ?Model $record, $state) => 
                                $state ? $component->state(str($state)->afterLast('\\')->headline().' # '.$record->subject_id) : '-'
                            )
                            ->label('Subject'),

                        Textarea::make('description')
                            ->label('Description')
                            ->rows(2)
                            ->columnSpan(2),
                    ])
                    ->columns(2),
                ])
                ->columnSpan(['sm' => 3]),

                Group::make([
                    Card::make([
                        Placeholder::make('log_name')
                            ->content(
                                fn (?Model $record): string => $record?->log_name
                                    ? ucwords($record?->log_name)
                                    : '-'
                            )
                            ->label('Type'),

                        Placeholder::make('event')
                            ->content(
                                fn (?Model $record): string => $record?->event
                                    ? ucwords($record?->event)
                                    : '-'
                            )
                            ->label('Event'),

                        Placeholder::make('created_at')
                            ->label('Logged At')
                            ->content(
                                fn (?Model $record): string => $record?->created_at
                                    ? "{$record?->created_at->format('d/m/Y H:i')}"
                                    : '-'
                            ),
                    ])
                ]),
                Card::make([
                    KeyValue::make('properties')
                        ->label('Properties'),
                ])
                ->visible(fn ($record) => $record->properties?->count() > 0)
            ])
            ->columns(['sm' => 4, 'lg' => null]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                BadgeColumn::make('log_name')
                    ->colors(static::getLogNameColors())
                    ->label('Type')
                    ->formatStateUsing(fn ($state) => ucwords($state))
                    ->sortable(),

                TextColumn::make('event')
                    ->sortable(),
                
                TextColumn::make('description')
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->wrap(),

                TextColumn::make('subject_type')
                    ->label('Subject')
                    ->formatStateUsing(function ($state, Model $record) {
                        if (!$state) {
                            return '-';
                        }
                        return str($state)->afterLast('\\')->headline().' # '.$record->subject_id;
                    }),

                TextColumn::make('causer.name')
                    ->label('User'),
                
                TextColumn::make('created_at')
                    ->label('Logged At')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->bulkActions([])
            ->filters([
                SelectFilter::make('log_name')
                    ->label('Type')
                    ->options(static::getLogNameList()),
                SelectFilter::make('subject_type')
                    ->label('Subject Type')
                    ->options(static::getSubjectTypeList()),
                
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('logged_at')
                            ->label('Logged At')
                            ->displayFormat('d/m/Y'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['logged_at'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', $date),
                            );
                            
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivities::route('/'),
            'view' => Pages\ViewActivity::route('/{record}'),
        ];
    }

    protected static function getSubjectTypeList(): array
    {
        if (config('filament-logger.resources.enabled', true)) {
            $subjects = [];
            $exceptResources = [...config('filament-logger.resources.exclude'), ActivityResource::class];
            $removedExcludedResources = collect(Filament::getResources())->filter(function ($resource) use ($exceptResources) {
                return ! in_array($resource, $exceptResources);
            });
            foreach ($removedExcludedResources as $resource) {
                $model = $resource::getModel();
                $subjects[$model] = Str::of(class_basename($model))->headline();
            }
            return $subjects; 
        }
        return [];
    }

    protected static function getLogNameList(): array
    {
        $customs = [];
        
        foreach (config('filament-logger.custom') ?? [] as $custom)
        {
            $customs[$custom['name']] = $custom['name'];
        }

        return array_merge(
            config('filament-logger.resources.enabled') ? [
                config('filament-logger.resources.log_name') => config('filament-logger.resources.log_name'),
            ] : [],
            
            config('filament-logger.access.enabled') 
                ? [config('filament-logger.access.log_name') => config('filament-logger.access.log_name')]
                : [],

            config('filament-logger.notifications.enabled') ? [
                config('filament-logger.notifications.log_name') => config('filament-logger.notifications.log_name'),
            ] : [],
            $customs,
        );
    }

    protected static function getLogNameColors(): array
    {
        $customs = [];
        
        foreach (config('filament-logger.custom') ?? [] as $custom)
        {
            if (filled($custom['color'] ?? null))
            {
                $customs[$custom['color']] = $custom['name'];
            }
        }

        return array_merge(
            (config('filament-logger.resources.enabled') && config('filament-logger.resources.color')) ? [
                config('filament-logger.resources.color') => config('filament-logger.resources.log_name'),
            ] : [],
            
            (config('filament-logger.access.enabled') && config('filament-logger.access.color')) ? [
                config('filament-logger.access.color') => config('filament-logger.access.log_name'),
            ] : [],

            (config('filament-logger.notifications.enabled') &&  config('filament-logger.notifications.color')) ? [
                config('filament-logger.notifications.color') => config('filament-logger.notifications.log_name'),
            ] : [],
            $customs,
        );
    }
}