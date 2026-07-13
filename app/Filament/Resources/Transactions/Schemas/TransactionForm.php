<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Umum')
                    ->components([
                        TextInput::make('code'),
                        Select::make('flight_id')
                            ->relationship('flight', 'flight_number'),
                        Select::make('flight_class_id')
                            ->relationship('class', 'class_type'),
                    ]),

                Section::make('Informasi Penumpang')
                    ->components([
                        TextInput::make('number_of_passengers'),
                        TextInput::make('name'),
                        TextInput::make('email'),
                        TextInput::make('phone'),

                        Section::make('Daftar Penumpang')
                            ->components([
                                Repeater::make('passenger')
                                    ->relationship('passengers')
                                    ->components([
                                        TextInput::make('seat_name'),
                                        TextInput::make('name'),
                                        DatePicker::make('date_of_birth'),
                                        TextInput::make('nationality'),
                                    ]),
                            ]),
                    ]),

                Section::make('Pembayaran')
                    ->components([
                        TextInput::make('promo.code'),
                        TextInput::make('promo.discount_type'),
                        TextInput::make('promo.discount'),
                        TextInput::make('payment_status'),
                        TextInput::make('subtotal'),
                        TextInput::make('grandtotal'),
                    ]),
            ]);
    }
}
