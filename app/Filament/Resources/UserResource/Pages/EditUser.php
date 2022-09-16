<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getFormActions(): array
    {
        $formActions = parent::getFormActions();

        // Cancel
        $formActions[1]->url(route('filament.pages.dashboard'));

        return $formActions;
    }

    protected function getRedirectUrl(): string
    {
        return url(route('filament.pages.dashboard'));
    }
}
