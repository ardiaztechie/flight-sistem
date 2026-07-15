<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\FlightController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PromoCodeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('flights', [FlightController::class, 'index'])->name('flight.index');
Route::get('flight/{flightNumber}/choose-tier', [FlightController::class, 'show'])->name('flight.show');

Route::get('flight/booking/{flightNumber}', [BookingController::class, 'booking'])->name('booking');

Route::get('flight/booking/{flightNumber}/choose-seat', [BookingController::class, 'chooseSeat'])->name('booking.chooseSeat');
Route::post('flight/booking/{flightNumber}/confirm-seat', [BookingController::class, 'confirmSeat'])->name('booking.confirmSeat');

Route::get('flight/booking/{flightNumber}/passenger-details', [BookingController::class, 'passengerDetails'])->name('booking.passengerDetails');
Route::post('flight/booking/{flightNumber}/save-passenger-details', [BookingController::class, 'savePassengerDetails'])->name('booking.savePassengerDetails');

Route::get('flight/booking/{flightNumber}/checkout', [BookingController::class, 'checkout'])->name('booking.checkout');
Route::post('flight/booking/{flightNumber}/process-payment', [BookingController::class, 'processPayment'])->name('booking.processPayment');

// Xendit redirect callback routes
Route::get('booking/payment/success', [BookingController::class, 'paymentSuccess'])->name('booking.payment.success');
Route::get('booking/payment/failed', [BookingController::class, 'paymentFailed'])->name('booking.payment.failed');

// Xendit webhook (skip CSRF verification via bootstrap/app.php)
Route::post('xendit/webhook', [BookingController::class, 'xenditWebhook'])->name('xendit.webhook');

// AJAX promo check
Route::get('promo/check', [PromoCodeController::class, 'check'])->name('promo.check');

Route::get('check-booking', [BookingController::class, 'checkBooking'])->name('booking.check');