<?php

namespace App\Filament\Resources\NotificationJobResource\Pages;

use App\Filament\Resources\NotificationJobResource;
use Filament\Resources\Pages\CreateRecord;

class CreateNotificationJob extends CreateRecord
{
    protected static string $resource = NotificationJobResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
