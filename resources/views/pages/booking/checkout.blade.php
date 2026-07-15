@extends('layouts.app')

@section('include')
<div id="Background" class="absolute top-0 w-full h-[810px] bg-[linear-gradient(180deg,#85C8FF_0%,#D4D1FE_47.05%,#F3F6FD_100%)]">
    <img src="{{ asset('assets/images/backgrounds/Jumbo Jet Sky (1) 1.png') }}" class="absolute right-0 top-[147px] object-contain max-h-[481px]" alt="background image">
</div>
@endsection

@php
    $qty      = count($transaction['selected_seats']);
    $pricePerPax = $tier->price;
    $subtotal = $pricePerPax * $qty;
    $tax      = $subtotal * 0.11;
    $grandTotal = $subtotal + $tax;
@endphp

@section('content')
<main class="relative flex flex-col w-full max-w-[1280px] px-[75px] mx-auto mt-[50px] mb-[62px]">
    <a href="{{ route('booking.passengerDetails', $flight->flight_number) }}"
        class="flex items-center rounded-[50px] py-3 px-5 gap-[10px] w-fit bg-garuda-black">
        <img src="{{ asset('assets/images/icons/arrow-left-white.svg') }}" class="w-6 h-6" alt="icon">
        <p class="font-semibold text-white">Back to Passenger Details</p>
    </a>
    <h1 class="font-extrabold text-[50px] leading-[75px] mt-[30px]">Checkout</h1>

    @if(session('error'))
        <div class="mb-4 p-4 rounded-xl bg-red-100 text-red-700 font-semibold">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex gap-[30px] mt-[30px]">

        {{-- ========== LEFT CONTENT ========== --}}
        <div id="Left-Content" class="flex flex-col gap-[30px] w-[470px] shrink-0">

            {{-- Your Flight --}}
            <div id="Flight-Info"
                class="accordion group flex flex-col h-fit rounded-[20px] bg-white overflow-hidden has-[:checked]:!h-[75px] transition-all duration-300">
                <label class="flex items-center justify-between p-5">
                    <h2 class="font-bold text-xl leading-[30px]">Your Flight</h2>
                    <img src="{{ asset('assets/images/icons/arrow-up-circle-black.svg') }}"
                        class="w-9 h-8 group-has-[:checked]:rotate-180 transition-all duration-300" alt="icon">
                    <input type="checkbox" class="hidden">
                </label>
                <div class="accordion-content p-5 pt-0 flex flex-col gap-5">
                    <div class="flex justify-between">
                        <div>
                            <p class="text-sm text-garuda-grey">Departure</p>
                            <p class="font-semibold text-lg">
                                {{ $flight->segments->first()->airport->name }}
                                ({{ $flight->segments->first()->airport->iata_code }})
                            </p>
                        </div>
                        <div class="text-end">
                            <p class="text-sm text-garuda-grey">Arrival</p>
                            <p class="font-semibold text-lg">
                                {{ $flight->segments->last()->airport->name }}
                                ({{ $flight->segments->last()->airport->iata_code }})
                            </p>
                        </div>
                    </div>
                    <div class="flex justify-between">
                        <div>
                            <p class="text-sm text-garuda-grey">Date</p>
                            <p class="font-semibold text-lg">{{ $flight->segments->first()->time->format('d M Y') }}</p>
                        </div>
                        <div class="text-end">
                            <p class="text-sm text-garuda-grey">Quantity</p>
                            <p class="font-semibold text-lg">{{ $qty }} people</p>
                        </div>
                    </div>
                    <div class="flex flex-col rounded-[20px] border border-[#E8EFF7] p-5 gap-5">
                        <div class="flex flex-col gap-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-[10px]">
                                    <img src="{{ asset('storage/' . $flight->airline->logo) }}"
                                        class="h-[60px] flex shrink-0" alt="logo"
                                        onerror="this.src='https://placehold.co/120x60/e8eff7/888?text={{ urlencode($flight->airline->name) }}'">
                                </div>
                                <a href="#" class="flex items-center rounded-[50px] py-3 px-5 gap-[10px] w-fit bg-garuda-black">
                                    <p class="font-semibold text-white">Details</p>
                                </a>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-semibold">{{ $flight->airline->name }}</p>
                                    <p class="text-sm text-garuda-grey mt-[2px]">
                                        {{ $flight->segments->first()->time->format('H:i') }} -
                                        {{ $flight->segments->last()->time->format('H:i') }}
                                    </p>
                                </div>
                                <div class="flex flex-col gap-[2px] items-center justify-center">
                                    <p class="text-sm text-garuda-grey">
                                        {{ number_format($flight->segments->first()->time->diffInHours($flight->segments->last()->time), 0) }} Hours
                                    </p>
                                    <div class="flex items-center gap-[6px]">
                                        <p class="font-semibold">{{ $flight->segments->first()->airport->iata_code }}</p>
                                        @if ($flight->segments->count() > 2)
                                            <img src="{{ asset('assets/images/icons/transit-black.svg') }}" alt="icon">
                                        @else
                                            <img src="{{ asset('assets/images/icons/direct-black.svg') }}" alt="icon">
                                        @endif
                                        <p class="font-semibold">{{ $flight->segments->last()->airport->iata_code }}</p>
                                    </div>
                                    <p class="text-sm text-garuda-grey">
                                        @if ($flight->segments->count() > 2) Transit @else Direct @endif
                                    </p>
                                </div>
                                <p class="font-semibold text-garuda-green text-center">
                                    Rp. {{ number_format($pricePerPax, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Transaction Details (update otomatis via JS) --}}
            <div id="Transaction-Info"
                class="accordion group flex flex-col h-fit rounded-[20px] bg-white overflow-hidden has-[:checked]:!h-[75px] transition-all duration-300">
                <label class="flex items-center justify-between p-5">
                    <h2 class="font-bold text-xl leading-[30px]">Transaction Details</h2>
                    <img src="{{ asset('assets/images/icons/arrow-up-circle-black.svg') }}"
                        class="w-9 h-8 group-has-[:checked]:rotate-180 transition-all duration-300" alt="icon">
                    <input type="checkbox" class="hidden">
                </label>
                <div class="accordion-content p-5 pt-0 flex flex-col gap-5">
                    <div class="flex justify-between">
                        <div>
                            <p class="text-sm text-garuda-grey">Quantity</p>
                            <p class="font-semibold text-lg leading-[27px] mt-[2px]">{{ $qty }} People</p>
                        </div>
                        <div>
                            <p class="text-sm text-garuda-grey">Tiers</p>
                            <p class="font-semibold text-lg leading-[27px] mt-[2px]">{{ \Str::ucfirst($tier->class_type) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-garuda-grey">Seats</p>
                            <p class="font-semibold text-lg leading-[27px] mt-[2px]">
                                {{ implode(', ', $flight->seats->whereIn('id', $transaction['selected_seats'])->pluck('name')->toArray()) }}
                            </p>
                        </div>
                    </div>
                    <div class="flex justify-between">
                        <div>
                            <p class="text-sm text-garuda-grey">Price / Pax</p>
                            <p class="font-semibold text-lg leading-[27px] mt-[2px]">Rp. {{ number_format($pricePerPax, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-garuda-grey">Govt. Tax</p>
                            <p class="font-semibold text-lg leading-[27px] mt-[2px]">11%</p>
                        </div>
                        <div>
                            <p class="text-sm text-garuda-grey">Sub Total</p>
                            <p class="font-semibold text-lg leading-[27px] mt-[2px]">Rp. {{ number_format($subtotal, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    {{-- Discount row (tersembunyi kalau belum ada promo) --}}
                    <div class="flex justify-between items-center" id="discount-row">
                        <div>
                            <p class="text-sm text-garuda-grey">Discount</p>
                            <p class="font-semibold text-lg leading-[27px] mt-[2px] text-garuda-green" id="display-discount">Rp 0</p>
                        </div>
                        <div>
                            <p class="text-sm text-garuda-grey">Promo Code</p>
                            <p class="font-semibold text-lg leading-[27px] mt-[2px] text-garuda-blue" id="display-promo-code">-</p>
                        </div>
                    </div>
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-garuda-grey">Total Tax</p>
                            <p class="font-semibold text-lg leading-[27px] mt-[2px]" id="display-tax">
                                Rp. {{ number_format($tax, 0, ',', '.') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-garuda-grey">Grand Total</p>
                            <p class="font-bold text-2xl leading-9 text-garuda-blue mt-[2px]" id="display-grand-total">
                                Rp. {{ number_format($grandTotal, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========== RIGHT CONTENT (FORM) ========== --}}
        <form action="{{ route('booking.processPayment', $flight->flight_number) }}"
              method="POST"
              id="Right-Content"
              class="flex flex-col gap-[30px] w-[490px] shrink-0">
            @csrf

            {{-- Hidden fields untuk dikirim ke server --}}
            <input type="hidden" name="promo_code"     id="hidden-promo-code"     value="">
            <input type="hidden" name="discount_amount" id="hidden-discount-amount" value="0">

            {{-- Customer Information --}}
            <div id="Customer-Info"
                class="accordion group flex flex-col h-fit rounded-[20px] bg-white overflow-hidden has-[:checked]:!h-[75px] transition-all duration-300">
                <label class="flex items-center justify-between p-5">
                    <h2 class="font-bold text-xl leading-[30px]">Customer Information</h2>
                    <img src="{{ asset('assets/images/icons/arrow-up-circle-black.svg') }}"
                        class="w-9 h-8 group-has-[:checked]:rotate-180 transition-all duration-300" alt="icon">
                    <input type="checkbox" class="hidden">
                </label>
                <div class="accordion-content p-5 pt-0 flex flex-col gap-5">
                    <label class="flex flex-col gap-[10px]">
                        <p class="font-semibold">Complete Name</p>
                        <div class="flex items-center rounded-full border border-garuda-black py-3 px-5 gap-[10px] bg-gray-50">
                            <img src="{{ asset('assets/images/icons/profile-black.svg') }}" class="w-5 flex shrink-0" alt="icon">
                            <input type="text" readonly value="{{ $transaction['name'] ?? '' }}"
                                class="appearance-none outline-none w-full font-semibold bg-transparent text-garuda-grey cursor-not-allowed">
                        </div>
                    </label>
                    <label class="flex flex-col gap-[10px]">
                        <p class="font-semibold">Email Address</p>
                        <div class="flex items-center rounded-full border border-garuda-black py-3 px-5 gap-[10px] bg-gray-50">
                            <img src="{{ asset('assets/images/icons/sms-black.png') }}" class="w-5 flex shrink-0" alt="icon">
                            <input type="email" readonly value="{{ $transaction['email'] ?? '' }}"
                                class="appearance-none outline-none w-full font-semibold bg-transparent text-garuda-grey cursor-not-allowed">
                        </div>
                    </label>
                    <label class="flex flex-col gap-[10px]">
                        <p class="font-semibold">Phone No.</p>
                        <div class="flex items-center rounded-full border border-garuda-black py-3 px-5 gap-[10px] bg-gray-50">
                            <img src="{{ asset('assets/images/icons/call-black.svg') }}" class="w-5 flex shrink-0" alt="icon">
                            <input type="tel" readonly value="{{ $transaction['phone'] ?? '' }}"
                                class="appearance-none outline-none w-full font-semibold bg-transparent text-garuda-grey cursor-not-allowed">
                        </div>
                    </label>
                </div>
            </div>

            {{-- Passenger Details --}}
            @if (!empty($transaction['passengers']))
                @foreach ($transaction['passengers'] as $index => $passenger)
                <div id="Passenger-{{ $index + 1 }}"
                    class="accordion-with-select group flex flex-col h-fit rounded-[20px] bg-white overflow-hidden transition-all duration-300">
                    <button type="button" class="accordion-btn flex items-center justify-between p-5">
                        <h2 class="font-bold text-xl leading-[30px]">Passenger {{ $index + 1 }}</h2>
                        <img src="{{ asset('assets/images/icons/arrow-up-circle-black.svg') }}"
                            class="arrow w-9 h-8 transition-all duration-300" alt="icon">
                    </button>
                    <div class="accordion-content p-5 pt-0 flex flex-col gap-5">
                        <label class="flex flex-col gap-[10px]">
                            <p class="font-semibold">Complete Name</p>
                            <div class="flex items-center rounded-full border border-garuda-black py-3 px-5 gap-[10px] bg-gray-50">
                                <img src="{{ asset('assets/images/icons/profile-black.svg') }}" class="w-5 flex shrink-0" alt="icon">
                                <input type="text" readonly value="{{ $passenger['name'] ?? '' }}"
                                    class="appearance-none outline-none w-full font-semibold bg-transparent text-garuda-grey cursor-not-allowed">
                            </div>
                        </label>
                        <div class="flex flex-col gap-[10px]">
                            <p class="font-semibold">Date of Birth</p>
                            <div class="flex items-center gap-[10px]">
                                @php $dob = isset($passenger['date_of_birth']) ? \Carbon\Carbon::parse($passenger['date_of_birth']) : null; @endphp
                                <label class="relative flex items-center w-full rounded-full overflow-hidden border border-garuda-black">
                                    <img src="{{ asset('assets/images/icons/note-add-black.svg') }}" class="absolute left-5 w-5 shrink-0" alt="icon">
                                    <select class="appearance-none w-full outline-none pl-[50px] py-3 px-5 font-semibold bg-gray-50 pointer-events-none" disabled>
                                        <option selected>{{ $dob ? $dob->format('d') : 'DD' }}</option>
                                    </select>
                                </label>
                                <label class="relative flex items-center w-full rounded-full overflow-hidden border border-garuda-black">
                                    <img src="{{ asset('assets/images/icons/note-add-black.svg') }}" class="absolute left-5 w-5 shrink-0" alt="icon">
                                    <select class="appearance-none w-full outline-none pl-[50px] py-3 px-5 font-semibold bg-gray-50 pointer-events-none" disabled>
                                        <option selected>{{ $dob ? $dob->format('m') : 'MM' }}</option>
                                    </select>
                                </label>
                                <label class="relative flex items-center w-full rounded-full overflow-hidden border border-garuda-black">
                                    <img src="{{ asset('assets/images/icons/note-add-black.svg') }}" class="absolute left-5 w-5 shrink-0" alt="icon">
                                    <select class="appearance-none w-full outline-none pl-[50px] py-3 px-5 font-semibold bg-gray-50 pointer-events-none" disabled>
                                        <option selected>{{ $dob ? $dob->format('Y') : 'YYYY' }}</option>
                                    </select>
                                </label>
                            </div>
                        </div>
                        <label class="flex flex-col gap-[10px]">
                            <p class="font-semibold">Nationality</p>
                            <div class="relative flex items-center w-full rounded-full overflow-hidden border border-garuda-black bg-gray-50">
                                <img src="{{ asset('assets/images/icons/global-black.svg') }}" class="absolute left-5 w-5 shrink-0" alt="icon">
                                <select class="appearance-none w-full outline-none pl-[50px] py-3 px-5 font-semibold bg-transparent text-garuda-grey pointer-events-none" disabled>
                                    <option selected>{{ $passenger['nationality'] ?? '-' }}</option>
                                </select>
                            </div>
                        </label>
                    </div>
                </div>
                @endforeach
            @endif

            {{-- Apply Promo --}}
            <div id="Promo" class="flex flex-col rounded-[20px] p-5 gap-5 bg-white overflow-hidden">
                <h2 class="font-bold text-xl leading-[30px]">Apply Promo</h2>
                <div class="flex flex-col gap-[10px]">
                    <p class="font-semibold">Your Promo Code</p>
                    <div class="flex items-center gap-[10px]">
                        <div class="flex items-center flex-1 rounded-full border border-garuda-black py-3 px-5 gap-[10px] focus-within:border-[#0068FF] transition-all duration-300">
                            <img src="{{ asset('assets/images/icons/receipt-discount-black.svg') }}" class="w-5 flex shrink-0" alt="icon">
                            <input type="text" id="promo-input"
                                class="appearance-none outline-none w-full font-semibold placeholder:font-normal uppercase"
                                placeholder="Input promo code"
                                oninput="this.value = this.value.toUpperCase()">
                        </div>
                        <button type="button" id="btn-apply-promo"
                            onclick="applyPromo()"
                            class="flex-shrink-0 px-5 py-3 rounded-full bg-garuda-blue text-white font-semibold hover:shadow-[0px_8px_20px_0px_#0068FF66] transition-all duration-300">
                            Apply
                        </button>
                    </div>
                    {{-- Alert promo --}}
                    <div id="promo-alert" class="hidden flex items-center gap-2 rounded-xl px-4 py-3 text-sm font-semibold"></div>
                </div>
            </div>

            {{-- Payment Method --}}
            <div id="Payment-Method" class="flex flex-col rounded-[20px] p-5 gap-5 bg-white overflow-hidden">
                <h2 class="font-bold text-xl leading-[30px]">Payment Method</h2>
                @error('payment_method')
                    <p class="text-sm text-red-500">{{ $message }}</p>
                @enderror
                <div class="flex flex-col gap-[10px]">
                    <p class="font-semibold">Choose Payment</p>
                    <div class="flex items-center flex-nowrap gap-[10px]">
                        <label class="group relative flex items-center w-full rounded-full py-3 px-5 bg-garuda-bg-dark-grey gap-[10px] has-[:checked]:bg-garuda-blue transition-all duration-300 cursor-pointer">
                            <svg class="w-5 h-5 flex shrink-0 group-has-[:checked]:brightness-0 group-has-[:checked]:invert" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                            <span class="font-semibold group-has-[:checked]:text-white">Xendit Gateway</span>
                            <input type="radio" name="payment_method" value="xendit" class="absolute opacity-0 left-1/2" required>
                        </label>
                    </div>
                </div>
            </div>

            <button type="submit" id="btn-pay"
                class="w-full rounded-full py-3 px-5 text-center bg-garuda-blue hover:shadow-[0px_14px_30px_0px_#0068FF66] transition-all duration-300 flex items-center justify-center gap-2">
                <span class="font-semibold text-white">Continue to Payment</span>
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </button>
        </form>
    </div>
</main>
@endsection

@section('scripts')
<script>
    // ── Data awal dari PHP ──────────────────────────────────────────────────
    const BASE_SUBTOTAL = {{ $subtotal }};
    const BASE_TAX_RATE = 0.11;

    // ── Format angka Rupiah ─────────────────────────────────────────────────
    function formatRp(num) {
        return 'Rp. ' + Math.round(num).toLocaleString('id-ID');
    }

    // ── Update tampilan transaction details ─────────────────────────────────
    function updateTransactionDisplay(discountAmount, promoCode) {
        const afterDiscount = Math.max(0, BASE_SUBTOTAL - discountAmount);
        const tax           = afterDiscount * BASE_TAX_RATE;
        const grandTotal    = afterDiscount + tax;

        document.getElementById('display-discount').textContent   = '- ' + formatRp(discountAmount);
        document.getElementById('display-tax').textContent        = formatRp(tax);
        document.getElementById('display-grand-total').textContent = formatRp(grandTotal);
        document.getElementById('display-promo-code').textContent  = promoCode || '-';

        // Simpan ke hidden field
        document.getElementById('hidden-promo-code').value      = promoCode || '';
        document.getElementById('hidden-discount-amount').value = Math.round(discountAmount);
    }

    // ── Apply promo AJAX ────────────────────────────────────────────────────
    function applyPromo() {
        const code    = document.getElementById('promo-input').value.trim();
        const alert   = document.getElementById('promo-alert');
        const btn     = document.getElementById('btn-apply-promo');

        if (!code) {
            showPromoAlert('error', 'Masukkan kode promo terlebih dahulu.');
            return;
        }

        btn.disabled    = true;
        btn.textContent = '...';

        fetch(`/promo/check?code=${encodeURIComponent(code)}&subtotal=${BASE_SUBTOTAL}`)
            .then(r => r.json())
            .then(data => {
                btn.disabled    = false;
                btn.textContent = 'Apply';

                if (data.valid) {
                    // ✅ Promo valid
                    showPromoAlert('success',
                        `🎉 Promo <strong>${code}</strong> berhasil! Diskon ${data.discount_label}`
                    );
                    updateTransactionDisplay(data.discount_amount, code);
                } else {
                    // ❌ Promo tidak valid
                    showPromoAlert('error', '❌ ' + data.message);
                    updateTransactionDisplay(0, '');
                }
            })
            .catch(() => {
                btn.disabled    = false;
                btn.textContent = 'Apply';
                showPromoAlert('error', 'Gagal menghubungi server. Coba lagi.');
            });
    }

    // ── Helper tampilkan alert promo ────────────────────────────────────────
    function showPromoAlert(type, html) {
        const el = document.getElementById('promo-alert');
        el.innerHTML = html;
        el.classList.remove('hidden', 'bg-green-50', 'text-green-700', 'bg-red-50', 'text-red-700');

        if (type === 'success') {
            el.classList.add('bg-green-50', 'text-green-700');
        } else {
            el.classList.add('bg-red-50', 'text-red-700');
        }
    }

    // ── Enter key di input promo ────────────────────────────────────────────
    document.getElementById('promo-input').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') { e.preventDefault(); applyPromo(); }
    });
</script>
@endsection
