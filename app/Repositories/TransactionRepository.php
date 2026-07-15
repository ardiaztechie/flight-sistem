<?php

namespace App\Repositories;

use App\Interfaces\TransactionRepositoryInterface;
use App\Jobs\SendMailTransactionSuccessJob;
use App\Models\Transaction;
use App\Models\TransactionPassenger;
use App\Models\FlightClass;
use App\Models\PromoCode;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function getTransactionDataFromSession()
    {
        return session()->get('transaction');
    }

    public function saveTransactionDataToSession($data)
    {
        $transaction = session()->get('transaction', []);

        foreach ($data as $key => $value) {
            $transaction[$key] = $value;
        }

        session()->put('transaction', $transaction);
    }

    public function saveTransaction($data)
    {
        $data['code'] = $this->generateTransactionCode();
        $data['number_of_passengers'] = $this->countPassengers($data['passengers']);

        // Hitung subtotal
        $data['subtotal'] = $this->calculateSubtotal($data['flight_class_id'], $data['number_of_passengers']);

        // Jika ada discount yang sudah dihitung dari frontend (promo code check AJAX)
        $manualDiscount = isset($data['discount']) ? (int) $data['discount'] : 0;

        if ($manualDiscount > 0) {
            $data['discount']   = $manualDiscount;
            $data['grandtotal'] = $data['subtotal'] - $manualDiscount;
        } else {
            $data['discount']   = 0;
            $data['grandtotal'] = $data['subtotal'];

            // Fallback: terapkan promo jika ada dan discount belum dihitung
            if (!empty($data['promo_code'])) {
                $data = $this->applyPromoCode($data);
            }
        }

        // Tambahkan PPN 11%
        $data['grandtotal'] = $this->addPPN($data['grandtotal']);

        // Pastikan grandtotal tidak negatif
        $data['grandtotal'] = max(0, $data['grandtotal']);

        // Simpan transaksi dan penumpang
        $transaction = $this->createTransaction($data);
        $this->savePassengers($data['passengers'], $transaction->id);

        // JANGAN forget session di sini - biarkan controller yang handle
        // session()->forget('transaction');

        return $transaction;
    }


    private function generateTransactionCode()
    {
        return "BWAGARUDA" . rand(1000, 9999); 
    }

    private function countPassengers($passengers)
    {
        return count($passengers);
    }

    private function calculateSubtotal($flightClassId, $numberOfPassengers)
    {
        $price = FlightClass::findOrFail($flightClassId)->price;
        return $price * $numberOfPassengers;
    }

    private function applyPromoCode($data)
    {
        $promo = PromoCode::where('code', $data['promo_code'])
            ->where('valid_until', '>=', now())
            ->where('is_used', false)
            ->first();

        if ($promo) {
            if ($promo->discount_type === 'percentage') {
                $data['discount'] = $data['grandtotal'] * ($promo->discount / 100);
            } else {
                $data['discount'] = $promo->discount;
            }

            $data['grandtotal'] -= $data['discount'];
            $data['promo_code_id'] = $promo->id;

            // Tandai promo code sebagai sudah digunakan
            $promo->update(['is_used' => true]);
        }

        return $data;
    }

    private function addPPN($grandtotal)
    {
        $ppn = $grandtotal * 0.11; // 11% PPN
        return $grandtotal + $ppn;
    }

    private function createTransaction($data)
    {
        return Transaction::create($data);
    }

    private function savePassengers($passengers, $transactionId)
    {
        foreach ($passengers as $passenger) {
            $passenger['transaction_id'] = $transactionId;
            TransactionPassenger::create($passenger);
        }
    }

    public function getTransactionByCode($code)
    {
        return Transaction::where('code', $code)->first();
    }

    public function getTransactionByCodeEmailPhone($code, $email, $phone)
    {
        return Transaction::where('code', $code)->where('email', $email)->where('phone_number', $phone)->first();
    }
   

}
