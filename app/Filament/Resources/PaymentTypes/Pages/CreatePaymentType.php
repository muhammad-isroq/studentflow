<?php

namespace App\Filament\Resources\PaymentTypes\Pages;

use App\Filament\Resources\PaymentTypes\PaymentTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePaymentType extends CreateRecord
{
    protected static string $resource = PaymentTypeResource::class;

    protected function getRedirectUrl(): string
{
    return $this->getResource()::getUrl('index');
}
}
