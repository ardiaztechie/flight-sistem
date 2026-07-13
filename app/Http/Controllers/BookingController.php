<?php

namespace App\Http\Controllers;

use App\Interfaces\FlightRepositoryInterface;
use App\Interfaces\TransactionRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Requests\StorePassengerDetailRequest;

class BookingController extends Controller
{
    private FlightRepositoryInterface $flightRepository;
    private TransactionRepositoryInterface $transactionRepository;

    public function __construct(
        FlightRepositoryInterface $flightRepository,
        TransactionRepositoryInterface $transactionRepository
    ) {
        $this->flightRepository = $flightRepository;
        $this->transactionRepository = $transactionRepository;
    }

    public function booking(Request $request, string $flightNumber)
    {
        $this->transactionRepository->saveTransactionDataToSession($request->all());

        return redirect()->route('booking.chooseSeat', ['flightNumber' => $flightNumber]);
    }

    public function chooseSeat(Request $request, string $flightNumber)
    {
        $transaction = $this->transactionRepository->getTransactionDataFromSession();

        if (!$transaction) {
            return redirect()->route('flight.show', ['flightNumber' => $flightNumber])
                ->with('error', 'Session telah berakhir, silakan pilih kelas kembali.');
        }

        $flight = $this->flightRepository->getFlightByFlightNumber($flightNumber);

        $flightClassId = isset($transaction['flight_class_id']) ? $transaction['flight_class_id'] : null;
        $tier = $flightClassId ? $flight->classes->find($flightClassId) : null;

        if (!$tier) {
            return redirect()->route('flight.show', ['flightNumber' => $flightNumber])
                ->with('error', 'Kelas penerbangan tidak ditemukan.');
        }

        return view('pages.booking.choose-seat', compact('transaction', 'flight', 'tier'));
    }

    public function confirmSeat(Request $request, string $flightNumber)
    {
        $this->transactionRepository->saveTransactionDataToSession($request->all());

        return redirect()->route('booking.passengerDetails', ['flightNumber' => $flightNumber]);
    }

    public function passengerDetails(Request $request, string $flightNumber)
    {
        $transaction = $this->transactionRepository->getTransactionDataFromSession();
        $flight = $this->flightRepository->getFlightByFlightNumber($flightNumber);
        $tier = $flight->classes->find($transaction['flight_class_id']);

        return view('pages.booking.passenger-details', compact('transaction', 'flight', 'tier'));
    }

    public function savePassengerDetails(StorePassengerDetailRequest $request, $flightNumber)
    {
        $this->transactionRepository->saveTransactionDataToSession($request->all());

        return redirect()->route('booking.checkout', ['flightNumber' => $flightNumber]);
    }

    public function checkout($flightNumber)
    {
        $transaction = $this->transactionRepository->getTransactionDataFromSession();
        $flight = $this->flightRepository->getFlightByFlightNumber($flightNumber);
        $tier = $flight->classes->find($transaction['flight_class_id']);

        return view('pages.booking.checkout', compact('transaction', 'flight', 'tier'));
    }

    public function processPayment(Request $request, string $flightNumber)
    {
        $request->validate([
            'payment_method' => 'required',
        ]);

        $transaction = $this->transactionRepository->getTransactionDataFromSession();

        if (!$transaction) {
            return redirect()->route('home')->with('error', 'Session telah berakhir.');
        }

        // Merge payment method ke session data
        $transaction['payment_method'] = $request->input('payment_method');
        $transaction['promo_code']     = $request->input('promo_code');

        // Simpan ke database
        $savedTransaction = $this->transactionRepository->saveTransaction($transaction);

        return redirect()->route('booking.check')
            ->with('success', 'Booking berhasil! Kode booking Anda: ' . $savedTransaction->code);
    }

    public function checkBooking()
    {
        return view('pages.booking.check-booking');
    }
}