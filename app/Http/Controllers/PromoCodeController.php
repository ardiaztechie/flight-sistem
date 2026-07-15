<?php

namespace App\Http\Controllers;

use App\Models\PromoCode;
use Illuminate\Http\Request;

class PromoCodeController extends Controller
{
    /**
     * AJAX endpoint: cek validitas promo code
     * GET /promo/check?code=XXX&subtotal=1500000
     */
    public function check(Request $request)
    {
        $code     = strtoupper(trim($request->query('code', '')));
        $subtotal = (float) $request->query('subtotal', 0);

        if (!$code) {
            return response()->json(['valid' => false, 'message' => 'Kode promo tidak boleh kosong.']);
        }

        $promo = PromoCode::where('code', $code)
            ->where('valid_until', '>=', now())
            ->where('is_used', false)
            ->first();

        if (!$promo) {
            return response()->json([
                'valid'   => false,
                'message' => 'Kode promo tidak valid atau sudah kadaluarsa.',
            ]);
        }

        // Hitung nilai diskon
        if ($promo->discount_type === 'percentage') {
            $discountAmount = $subtotal * ($promo->discount / 100);
            $label          = $promo->discount . '%';
        } else {
            $discountAmount = $promo->discount;
            $label          = 'Rp ' . number_format($promo->discount, 0, ',', '.');
        }

        $afterDiscount = max(0, $subtotal - $discountAmount);
        $tax           = $afterDiscount * 0.11;
        $grandTotal    = $afterDiscount + $tax;

        return response()->json([
            'valid'          => true,
            'message'        => 'Kode promo berhasil diterapkan!',
            'discount_type'  => $promo->discount_type,
            'discount_value' => $promo->discount,
            'discount_label' => $label,
            'discount_amount' => (int) $discountAmount,
            'after_discount' => (int) $afterDiscount,
            'tax'            => (int) $tax,
            'grand_total'    => (int) $grandTotal,
        ]);
    }
}
