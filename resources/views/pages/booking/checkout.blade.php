@extends('layouts.app')

@section('include')
<div id="Background" class="absolute top-0 w-full h-[810px] bg-[linear-gradient(180deg,#85C8FF_0%,#D4D1FE_47.05%,#F3F6FD_100%)]">
    <img src="{{ asset('assets/images/backgrounds/Jumbo Jet Sky (1) 1.png') }}" class="absolute right-0 top-[147px] object-contain max-h-[481px]" alt="background image">
</div>
@endsection

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
                            <p class="font-semibold text-lg">
                                {{ $flight->segments->first()->time->format('d M Y') }}
                            </p>
                        </div>
                        <div class="text-end">
                            <p class="text-sm text-garuda-grey">Quantity</p>
                            <p class="font-semibold text-lg">
                                {{ count($transaction['selected_seats']) }} people
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-col rounded-[20px] border border-[#E8EFF7] p-5 gap-5">
                        <div class="flex flex-col gap-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-[10px]">
                                    <img src="{{ asset('storage/' . $flight->airline->logo) }}"
                                        class="h-[100px] flex shrink-0" alt="logo">
                                </div>
                                <a href="#"
                                    class="flex items-center rounded-[50px] py-3 px-5 gap-[10px] w-fit bg-garuda-black">
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
                                        {{ number_format($flight->segments->first()->time->diffInHours($flight->segments->last()->time), 0) }}
                                        Hours
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
                                    @if ($flight->segments->count() > 2)
                                        <p class="text-sm text-garuda-grey">Transit 1x{{ $flight->segments->count() - 2 }}</p>
                                    @else
                                        <p class="text-sm text-garuda-grey">Direct</p>
                                    @endif
                                </div>
                                <p class="font-semibold text-garuda-green text-center">
                                    {{ 'Rp. ' . number_format($tier->price, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Transaction Details --}}
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
                            <p class="font-semibold text-lg leading-[27px] mt-[2px]">
                                {{ count($transaction['selected_seats']) }} People
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-garuda-grey">Tiers</p>
                            <p class="font-semibold text-lg leading-[27px] mt-[2px]">
                                {{ \Str::ucfirst($tier->class_type) }}
                            </p>
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
                            <p class="font-semibold text-lg leading-[27px] mt-[2px]">
                                {{ 'Rp. ' . number_format($tier->price, 0, ',', '.') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-garuda-grey">Govt. Tax</p>
                            <p class="font-semibold text-lg leading-[27px] mt-[2px]">11%</p>
                        </div>
                        <div>
                            <p class="text-sm text-garuda-grey">Sub Total</p>
                            <p class="font-semibold text-lg leading-[27px] mt-[2px]">
                                {{ 'Rp. ' . number_format($tier->price * count($transaction['selected_seats']), 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-garuda-grey">Discount</p>
                            <p class="font-semibold text-lg leading-[27px] mt-[2px] text-garuda-green" id="discount">
                                Rp 0
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-garuda-grey">Promo Code</p>
                            <p class="font-semibold text-lg leading-[27px] mt-[2px]" id="promo-code">
                                <!-- Nilai promo code biasanya ditaruh di sini -->
                            </p>
                        </div>
                    </div>
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-garuda-grey">Total Tax</p>
                            <p class="font-semibold text-lg leading-[27px] mt-[2px]">
                                {{ 'Rp. ' . number_format($tier->price * count($transaction['selected_seats']) * 0.11, 0, ',', '.') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-garuda-grey">Grand Total</p>
                            <p class="font-bold text-2xl leading-9 text-garuda-blue mt-[2px]">
                                {{ 'Rp. ' . number_format($tier->price * count($transaction['selected_seats']) * 1.11, 0, ',', '.') }}
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

            {{-- Customer Information (readonly, dari session) --}}
            <div id="Customer-Info"
                class="accordion group flex flex-col h-fit rounded-[20px] bg-white overflow-hidden has-[:checked]:!h-[75px] transition-all duration-300">
                <label class="flex items-center justify-between p-5">
                    <h2 class="font-bold text-xl leading-[30px]">Customer Information</h2>
                    <img src="{{ asset('assets/images/icons/arrow-up-circle-black.svg') }}"
                        class="w-9 h-8 group-has-[:checked]:rotate-180 transition-all duration-300" alt="icon">
                    <input type="checkbox" class="hidden">
                </label>
                <div class="accordion-content p-5 pt-0 flex flex-col gap-5">
                    {{-- Complete Name --}}
                    <label class="flex flex-col gap-[10px]">
                        <p class="font-semibold">Complete Name</p>
                        <div class="flex items-center rounded-full border border-garuda-black py-3 px-5 gap-[10px] bg-gray-50">
                            <img src="{{ asset('assets/images/icons/profile-black.svg') }}" class="w-5 flex shrink-0" alt="icon">
                            <input type="text" readonly
                                value="{{ $transaction['name'] ?? '' }}"
                                class="appearance-none outline-none w-full font-semibold bg-transparent text-garuda-grey cursor-not-allowed">
                        </div>
                    </label>
                    {{-- Email --}}
                    <label class="flex flex-col gap-[10px]">
                        <p class="font-semibold">Email Address</p>
                        <div class="flex items-center rounded-full border border-garuda-black py-3 px-5 gap-[10px] bg-gray-50">
                            <img src="{{ asset('assets/images/icons/sms-black.png') }}" class="w-5 flex shrink-0" alt="icon">
                            <input type="email" readonly
                                value="{{ $transaction['email'] ?? '' }}"
                                class="appearance-none outline-none w-full font-semibold bg-transparent text-garuda-grey cursor-not-allowed">
                        </div>
                    </label>
                    {{-- Phone --}}
                    <label class="flex flex-col gap-[10px]">
                        <p class="font-semibold">Phone No.</p>
                        <div class="flex items-center rounded-full border border-garuda-black py-3 px-5 gap-[10px] bg-gray-50">
                            <img src="{{ asset('assets/images/icons/call-black.svg') }}" class="w-5 flex shrink-0" alt="icon">
                            <input type="tel" readonly
                                value="{{ $transaction['phone'] ?? '' }}"
                                class="appearance-none outline-none w-full font-semibold bg-transparent text-garuda-grey cursor-not-allowed">
                        </div>
                    </label>
                </div>
            </div>

            {{-- Passenger Details (readonly, dari session) --}}
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

                        {{-- Complete Name --}}
                        <label class="flex flex-col gap-[10px]">
                            <p class="font-semibold">Complete Name</p>
                            <div class="flex items-center rounded-full border border-garuda-black py-3 px-5 gap-[10px] bg-gray-50">
                                <img src="{{ asset('assets/images/icons/profile-black.svg') }}" class="w-5 flex shrink-0" alt="icon">
                                <input type="text" readonly
                                    value="{{ $passenger['name'] ?? '' }}"
                                    class="appearance-none outline-none w-full font-semibold bg-transparent text-garuda-grey cursor-not-allowed">
                            </div>
                        </label>

                        {{-- Date of Birth --}}
                        <div class="flex flex-col gap-[10px]">
                            <p class="font-semibold">Date of Birth</p>
                            <div class="flex items-center gap-[10px]">
                                @php
                                    $dob = isset($passenger['date_of_birth']) ? \Carbon\Carbon::parse($passenger['date_of_birth']) : null;
                                @endphp
                                {{-- Day --}}
                                <label class="relative flex items-center w-full rounded-full overflow-hidden border border-garuda-black gap-[10px]">
                                    <img src="{{ asset('assets/images/icons/note-add-black.svg') }}"
                                        class="absolute transform -translate-y-1/2 top-1/2 left-5 w-5 shrink-0" alt="icon">
                                    <select class="date-select day-select appearance-none w-full outline-none pl-[50px] py-3 px-5 font-semibold bg-gray-50 pointer-events-none" disabled>
                                        <option selected>{{ $dob ? $dob->format('d') : 'DD' }}</option>
                                    </select>
                                </label>
                                {{-- Month --}}
                                <label class="relative flex items-center w-full rounded-full overflow-hidden border border-garuda-black gap-[10px]">
                                    <img src="{{ asset('assets/images/icons/note-add-black.svg') }}"
                                        class="absolute transform -translate-y-1/2 top-1/2 left-5 w-5 shrink-0" alt="icon">
                                    <select class="date-select month-select appearance-none w-full outline-none pl-[50px] py-3 px-5 font-semibold bg-gray-50 pointer-events-none" disabled>
                                        <option selected>{{ $dob ? $dob->format('m') : 'MM' }}</option>
                                    </select>
                                </label>
                                {{-- Year --}}
                                <label class="relative flex items-center w-full rounded-full overflow-hidden border border-garuda-black gap-[10px]">
                                    <img src="{{ asset('assets/images/icons/note-add-black.svg') }}"
                                        class="absolute transform -translate-y-1/2 top-1/2 left-5 w-5 shrink-0" alt="icon">
                                    <select class="date-select year-select appearance-none w-full outline-none pl-[50px] py-3 px-5 font-semibold bg-gray-50 pointer-events-none" disabled>
                                        <option selected>{{ $dob ? $dob->format('Y') : 'YYYY' }}</option>
                                    </select>
                                </label>
                            </div>
                        </div>

                        {{-- Nationality --}}
                        <label class="flex flex-col gap-[10px]">
                            <p class="font-semibold">Nationality</p>
                            <div class="relative flex items-center w-full rounded-full overflow-hidden border border-garuda-black gap-[10px] bg-gray-50">
                                <img src="{{ asset('assets/images/icons/global-black.svg') }}"
                                    class="absolute transform -translate-y-1/2 top-1/2 left-5 w-5 shrink-0" alt="icon">
                                <select class="appearance-none w-full outline-none pl-[50px] py-3 px-5 font-semibold bg-transparent text-garuda-grey pointer-events-none" disabled>
                                    <option selected>{{ $passenger['nationality'] ?? '-' }}</option>
                                </select>
                            </div>
                        </label>

                    </div>
                </div>
                @endforeach
            @endif

           @livewire('check-promo-code')

            {{-- Payment Method --}}
            <div id="Payment-Method" class="flex flex-col rounded-[20px] p-5 gap-5 bg-white overflow-hidden">
                <h2 class="font-bold text-xl leading-[30px]">Payment Method</h2>
                @error('payment_method')
                    <p class="text-sm text-red-500">{{ $message }}</p>
                @enderror
                <div class="flex flex-col gap-[10px]">
                    <p class="font-semibold">Choose Payment</p>
                    <div class="flex items-center flex-nowrap gap-[10px]">
                        <label class="group relative flex items-center w-full rounded-full py-3 px-5 bg-garuda-bg-dark-grey gap-[10px] has-[:checked]:bg-garuda-orange transition-all duration-300 cursor-pointer">
                            <img src="{{ asset('assets/images/icons/note-add-black.svg') }}"
                                class="w-5 flex shrink-0 group-has-[:checked]:invert transition-all duration-300" alt="icon">
                            <span class="font-semibold group-has-[:checked]:text-white">Midtrans Gateway</span>
                            <input type="radio" name="payment_method" value="midtrans"
                                class="absolute opacity-0 left-1/2" required>
                        </label>
                        <label class="group relative flex items-center w-full rounded-full py-3 px-5 bg-garuda-bg-dark-grey gap-[10px] has-[:checked]:bg-garuda-orange transition-all duration-300 cursor-pointer">
                            <img src="{{ asset('assets/images/icons/note-add-black.svg') }}"
                                class="w-5 flex shrink-0 group-has-[:checked]:invert transition-all duration-300" alt="icon">
                            <span class="font-semibold group-has-[:checked]:text-white">Transfer to Bank</span>
                            <input type="radio" name="payment_method" value="bank_transfer"
                                class="absolute opacity-0 left-1/2" required>
                        </label>
                    </div>
                </div>
            </div>

            <button type="submit"
                class="w-full rounded-full py-3 px-5 text-center bg-garuda-blue hover:shadow-[0px_14px_30px_0px_#0068FF66] transition-all duration-300">
                <span class="font-semibold text-white">Continue to Payment</span>
            </button>
        </form>
    </div>
</main>
@endsection

@section('scripts')
<script>
    function checkPromo() {
        const code = document.getElementById('promo_code').value.trim();
        const msg  = document.getElementById('promo-msg');
        if (!code) {
            msg.textContent = 'Masukkan kode promo terlebih dahulu.';
            msg.className   = 'text-sm font-semibold text-garuda-red';
            msg.classList.remove('hidden');
            return;
        }
        fetch(`/api/promo/check?code=${encodeURIComponent(code)}`)
            .then(r => r.json())
            .then(data => {
                if (data.valid) {
                    msg.textContent = `Kode promo "${code}" tersedia! Diskon: ${data.discount_label}`;
                    msg.className   = 'text-sm font-semibold text-garuda-green';
                } else {
                    msg.textContent = 'Kode promo tidak valid atau sudah kadaluarsa.';
                    msg.className   = 'text-sm font-semibold text-garuda-red';
                }
                msg.classList.remove('hidden');
            })
            .catch(() => {
                msg.textContent = 'Gagal memeriksa kode promo. Coba lagi.';
                msg.className   = 'text-sm font-semibold text-garuda-red';
                msg.classList.remove('hidden');
            });
    }
</script>
@endsection
