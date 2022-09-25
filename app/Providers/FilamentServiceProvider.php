<?php

namespace App\Providers;

use App\Filament\Resources\UserResource;
use Filament\Facades\Filament;
use Filament\Navigation\UserMenuItem;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Foundation\Vite;
use Illuminate\Support\ServiceProvider;

class FilamentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Filament::serving(function () {
            Filament::registerTheme(
                app(Vite::class)('resources/css/filament.css'),
            );
        });

        TextColumn::configureUsing(fn (TextColumn $column) => $column->timezone(auth()->user()->timezone));

        Filament::serving(function () {
            Filament::registerUserMenuItems([
                'account' => UserMenuItem::make()->url(
                    UserResource::getUrl('edit', ['record' => auth()->user()])
                ),
                'logout' => UserMenuItem::make()->url(route('logout')),
            ]);
        });

        Filament::serving(function () {
            BooleanColumn::macro('toggle', function () {
                /** @var BooleanColumn $this */
                $this->action(function ($record, $column) {
                    $name = $column->getName();
                    $record->update([
                        $name => ! $record->$name,
                    ]);
                });

                return $this;
            });
        });
    }
}
