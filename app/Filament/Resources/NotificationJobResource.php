<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotificationJobResource\Pages;
use App\Models\NotificationJob;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class NotificationJobResource extends Resource
{
    protected static ?string $model = NotificationJob::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereBelongsTo(auth()->user());
    }

    protected static function getNavigationBadge(): ?string
    {
        return static::getEloquentQuery()->where('is_active', true)->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Schedule')
                    // ->description('Description')
                    ->schema([
                        Forms\Components\TextInput::make('minute')
                            ->default('*')
                            ->required(),
                        Forms\Components\TextInput::make('hour')
                            ->default('*')
                            ->required(),
                        Forms\Components\TextInput::make('day')
                            ->default('*')
                            ->required(),
                        Forms\Components\TextInput::make('month')
                            ->default('*')
                            ->required(),
                        Forms\Components\TextInput::make('weekday')
                            ->default('*')
                            ->required(),
                        Forms\Components\Select::make('timezone')
                            ->options(array_combine(
                                timezone_identifiers_list(),
                                timezone_identifiers_list(),
                            ))
                            ->default(auth()->user()->timezone)
                            ->searchable()
                            ->columnSpan(2),
                    ])
                    ->columns(5),
                Forms\Components\Grid::make(1)
                    ->schema([
                        Forms\Components\TextInput::make('event')->required(),
                        Forms\Components\TextInput::make('title'),
                        Forms\Components\Repeater::make('content')
                            ->schema([
                                Forms\Components\TextInput::make('content')
                                    ->required(),
                            ])
                            ->required(),
                        Forms\Components\Hidden::make('is_active')
                            ->default(true)
                            ->required(),
                    ])
                    ->columnSpan(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('minute'),
                Tables\Columns\TextColumn::make('hour'),
                Tables\Columns\TextColumn::make('day'),
                Tables\Columns\TextColumn::make('month'),
                Tables\Columns\TextColumn::make('weekday'),
                Tables\Columns\TextColumn::make('timezone'),
                Tables\Columns\TextColumn::make('event'),
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TagsColumn::make('content')
                    ->getStateUsing(function (NotificationJob $record) {
                        return Arr::pluck($record->content, 'content');
                    }),
                Tables\Columns\BooleanColumn::make('is_active')
                    ->toggle(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListNotificationJobs::route('/'),
            'create' => Pages\CreateNotificationJob::route('/create'),
            'edit' => Pages\EditNotificationJob::route('/{record}/edit'),
        ];
    }
}
