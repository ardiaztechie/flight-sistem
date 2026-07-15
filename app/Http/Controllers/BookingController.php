<?php

namespace App\Http\Controllers;

use App\Interfaces\FlightRepositoryInterface;
use App\Interfaces\TransactionRepositoryInterface;
use App\Models\Transaction;
use App\Services\XenditService;
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
        $this->flightRepository      = $flightRepository;
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
        $tier          = $flightClassId ? $flight->classes->find($flightClassId) : null;

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
        $flight      = $this->flightRepository->getFlightByFlightNumber($flightNumber);
        $tier        = $flight->classes->find($transaction['flight_class_id']);

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
        $flight      = $this->flightRepository->getFlightByFlightNumber($flightNumber);
        $tier        = $flight->classes->find($transaction['flight_class_id']);

        return view('pages.booking.checkout', compact('transaction', 'flight', 'tier'));
    }

    /**
     * Proses pembayaran: simpan transaksi ke DB, buat Xendit invoice, redirect ke Xendit
     */
    public function processPayment(Request $request, string $flightNumber)
    {
        $request->validate([
            'payment_method' => 'required',
        ]);

        $transaction = $this->transactionRepository->getTransactionDataFromSession();

        if (!$transaction) {
            return redirect()->route('home')->with('error', 'Session telah berakhir, silakan mulai booking kembali.');
        }

        // Ambil data tambahan dari form
        $transaction['payment_method'] = $request->input('payment_method');
        $transaction['promo_code']     = $request->input('promo_code');
        $transaction['discount']       = (int) $request->input('discount_amount', 0);

        // Simpan transaksi ke database (status: pending)
        $savedTransaction = $this->transactionRepository->saveTransaction($transaction);

        // Hitung grand total (sudah include diskon + pajak dari repository)
        $grandTotal = $savedTransaction->grandtotal;

        // Buat Xendit Invoice
        try {
            $xendit = new XenditService();

            $flight = $this->flightRepository->getFlightByFlightNumber($flightNumber);
            $tier   = $flight->classes->find($transaction['flight_class_id']);

            $invoiceData = $xendit->createInvoice([
                'external_id'   => $savedTransaction->code,
                'amount'        => $grandTotal,
                'email'         => $savedTransaction->email,
                'customer_name' => $savedTransaction->name,
                'phone'         => $savedTransaction->phone,
                'description'   => 'Tiket Penerbangan ' . $flight->flight_number . ' - ' . $flight->segments->first()->airport->iata_code . ' → ' . $flight->segments->last()->airport->iata_code,
                'success_url'   => route('booking.payment.success') . '?code=' . $savedTransaction->code,
                'failure_url'   => route('booking.payment.failed') . '?code=' . $savedTransaction->code,
                'items'         => [
                    [
                        'name'     => 'Tiket ' . \Str::ucfirst($tier->class_type) . ' ' . $flight->flight_number,
                        'quantity' => $savedTransaction->number_of_passengers,
                        'price'    => $tier->price,
                        'category' => 'Flight Ticket',
                    ]
                ],
            ]);

            // Update transaksi dengan Xendit invoice data
            $savedTransaction->update([
                'xendit_invoice_id'  => $invoiceData['invoice_id'],
                'xendit_invoice_url' => $invoiceData['invoice_url'],
            ]);

            // Redirect ke Xendit payment page
            return redirect($invoiceData['invoice_url']);

        } catch (\Exception $e) {
            // Jika Xendit gagal, hapus transaksi dan kembalikan dengan error
            $savedTransaction->forceDelete();
            return redirect()->route('booking.checkout', ['flightNumber' => $flightNumber])
                ->with('error', 'Gagal membuat invoice pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Halaman sukses setelah redirect dari Xendit
     */
    public function paymentSuccess(Request $request)
    {
        $code        = $request->query('code');
        $transaction = Transaction::where('code', $code)
            ->with(['flight.airline', 'flight.segments.airport', 'passengers'])
            ->first();

        if (!$transaction) {
            return redirect()->route('home')->with('error', 'Transaksi tidak ditemukan.');
        }

        // Verifikasi status dari Xendit (kalau invoice_id ada)
        if ($transaction->xendit_invoice_id) {
            try {
                $xendit  = new XenditService();
                $invoice = $xendit->getInvoice($transaction->xendit_invoice_id);

                if ($invoice['status'] === 'PAID' && $transaction->payment_status !== 'paid') {
                    $transaction->update(['payment_status' => 'paid']);
                }
            } catch (\Exception $e) {
                // Tetap tampilkan halaman sukses meskipun verifikasi gagal
            }
        }

        // Tandai pembayaran sebagai paid (simulasi development)
        if ($transaction->payment_status !== 'paid') {
            $transaction->update(['payment_status' => 'paid']);
        }

        // Bersihkan session booking
        session()->forget('transaction');

        return view('pages.booking.success', compact('transaction'));
    }

    /**
     * Halaman gagal/dibatalkan dari Xendit
     */
    public function paymentFailed(Request $request)
    {
        $code        = $request->query('code');
        $transaction = Transaction::where('code', $code)->first();

        if ($transaction) {
            $transaction->update(['payment_status' => 'failed']);
        }

        return redirect()->route('home')
            ->with('error', 'Pembayaran dibatalkan atau gagal. Silakan coba lagi.');
    }

    /**
     * Xendit Webhook (notifikasi otomatis dari Xendit)
     */
    public function xenditWebhook(Request $request)
    {
        // Verifikasi token webhook dari Xendit
        $token = $request->header('x-callback-token');
        if ($token !== config('xendit.webhook_token')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data   = $request->all();
        $status = strtoupper($data['status'] ?? '');
        $externalId = $data['external_id'] ?? null;

        if (!$externalId) {
            return response()->json(['message' => 'No external_id'], 200);
        }

        $transaction = Transaction::where('code', $externalId)->first();

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 200);
        }

        if ($status === 'PAID') {
            $transaction->update(['payment_status' => 'paid']);
        } elseif (in_array($status, ['EXPIRED', 'FAILED'])) {
            $transaction->update(['payment_status' => 'failed']);
        }

        return response()->json(['message' => 'OK'], 200);
    }

    public function checkBooking()
    {
        return view('pages.booking.check-booking');
    }

    public function showBooking(Request $request)
    {
        $request->validate([
            'booking_trx_id' => 'required|string',
            'phone'          => 'required|string',
        ]);

        $transaction = Transaction::where('code', $request->booking_trx_id)
                                  ->where('phone', $request->phone)
                                  ->with(['flight.airline', 'flight.segments.airport', 'passengers', 'flightClass'])
                                  ->first();

        if (!$transaction) {
            return redirect()->back()->with('error', 'Booking tidak ditemukan. Periksa kembali Kode Booking dan Nomor HP Anda.');
        }

        // Tampilkan halaman detail booking / success dengan data transaction tersebut
        return view('pages.booking.success', compact('transaction'));
    }
}