<?php

namespace App\Filament\Resources\Flights\Pages;

use App\Filament\Resources\Flights\FlightResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions;
use App\Models\Flight;

class CreateFlight extends CreateRecord
{
    protected static string $resource = FlightResource::class;
    protected function afterCreate(): void
    {
        $flight = Flight::find($this->record->id);

        $flight->generateSeats();
    }
}
