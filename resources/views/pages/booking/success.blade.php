@extends('layouts.app')

@section('include')
<div id="Background" class="absolute top-0 w-full h-[400px] bg-[linear-gradient(180deg,#0047CC_0%,#85C8FF_100%)]"></div>
@endsection

@section('content')
<main class="relative flex flex-col items-center w-full max-w-[1280px] px-[75px] mx-auto mt-[50px] mb-[80px]">

    {{-- ✅ Alert Pembayaran Berhasil --}}
    <div id="success-alert"
        class="w-full max-w-[780px] mb-8 flex items-center gap-4 p-5 rounded-2xl bg-green-500 text-white shadow-[0px_8px_30px_0px_rgba(34,197,94,0.4)] animate-bounce-once">
        <div class="w-12 h-12 flex items-center justify-center rounded-full bg-white shrink-0">
            <svg class="w-7 h-7 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <div>
            <p class="font-bold text-lg">Pembayaran Berhasil! 🎉</p>
            <p class="text-sm opacity-90">Tiket Anda telah dikonfirmasi. Kode booking: <strong>{{ $transaction->code }}</strong></p>
        </div>
    </div>

    {{-- ══════════════════ TICKET CARD ══════════════════ --}}
    <div id="ticket-card"
        class="w-full max-w-[780px] bg-white rounded-[30px] shadow-[0px_20px_60px_0px_rgba(0,0,0,0.12)] overflow-hidden">

        {{-- Header Ticket --}}
        <div class="relative bg-[linear-gradient(135deg,#0047CC_0%,#0068FF_50%,#00B4D8_100%)] px-10 py-8 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-75 font-medium uppercase tracking-widest">Boarding Pass</p>
                    <h1 class="text-4xl font-black mt-1">{{ $transaction->code }}</h1>
                </div>
                <div class="text-right">
                    <p class="text-xs opacity-75 uppercase tracking-widest">Status</p>
                    <span class="inline-block mt-1 px-4 py-1.5 rounded-full bg-green-400 text-white text-sm font-bold">
                        ✓ PAID
                    </span>
                </div>
            </div>

            {{-- Airline --}}
            <div class="flex items-center gap-3 mt-6">
                <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center p-1 overflow-hidden">
                    <img src="{{ asset('storage/' . $transaction->flight->airline->logo) }}"
                        class="w-full h-full object-contain"
                        alt="{{ $transaction->flight->airline->name }}"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
                    <div class="hidden w-full h-full items-center justify-center bg-blue-100 rounded-full">
                        <span class="text-blue-600 font-bold text-xs">{{ $transaction->flight->airline->code }}</span>
                    </div>
                </div>
                <div>
                    <p class="font-bold text-lg">{{ $transaction->flight->airline->name }}</p>
                    <p class="text-sm opacity-75">Flight {{ $transaction->flight->flight_number }}</p>
                </div>
            </div>

            {{-- Dekorasi gelombang --}}
            <div class="absolute bottom-0 left-0 right-0">
                <svg viewBox="0 0 780 30" fill="white" preserveAspectRatio="none" class="w-full">
                    <path d="M0,20 Q195,0 390,15 Q585,30 780,10 L780,30 L0,30 Z"/>
                </svg>
            </div>
        </div>

        {{-- Route Info --}}
        <div class="px-10 pt-6 pb-4">
            @php
                $segments  = $transaction->flight->segments->sortBy('sequence');
                $departure = $segments->first();
                $arrival   = $segments->last();
            @endphp
            <div class="flex items-center justify-between">
                {{-- Departure --}}
                <div>
                    <p class="text-5xl font-black text-gray-900">{{ $departure->airport->iata_code }}</p>
                    <p class="font-semibold text-gray-700 mt-1">{{ $departure->airport->city }}</p>
                    <p class="text-sm text-gray-400">{{ $departure->airport->name }}</p>
                    <p class="text-lg font-bold text-garuda-blue mt-2">{{ $departure->time->format('H:i') }}</p>
                    <p class="text-sm text-gray-500">{{ $departure->time->format('d M Y') }}</p>
                </div>

                {{-- Flight Duration --}}
                <div class="flex flex-col items-center gap-2 flex-1 px-6">
                    <p class="text-xs text-gray-400 font-medium">
                        {{ number_format($departure->time->diffInHours($arrival->time), 0) }}h
                        {{ $departure->time->diff($arrival->time)->i }}m
                    </p>
                    <div class="relative w-full flex items-center">
                        <div class="flex-1 h-[2px] bg-gray-200"></div>
                        @if ($segments->count() > 2)
                            <div class="absolute left-1/2 transform -translate-x-1/2 w-3 h-3 rounded-full border-2 border-gray-400 bg-white"></div>
                        @endif
                        <svg class="w-5 h-5 text-garuda-blue ml-1" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M21 16v-2l-8-5V3.5c0-.83-.67-1.5-1.5-1.5S10 2.67 10 3.5V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5l8 2.5z"/>
                        </svg>
                    </div>
                    @if ($segments->count() > 2)
                        <p class="text-xs text-orange-500 font-semibold">Transit {{ $segments->count() - 2 }}x</p>
                    @else
                        <p class="text-xs text-green-500 font-semibold">Direct</p>
                    @endif
                </div>

                {{-- Arrival --}}
                <div class="text-right">
                    <p class="text-5xl font-black text-gray-900">{{ $arrival->airport->iata_code }}</p>
                    <p class="font-semibold text-gray-700 mt-1">{{ $arrival->airport->city }}</p>
                    <p class="text-sm text-gray-400">{{ $arrival->airport->name }}</p>
                    <p class="text-lg font-bold text-garuda-blue mt-2">{{ $arrival->time->format('H:i') }}</p>
                    <p class="text-sm text-gray-500">{{ $arrival->time->format('d M Y') }}</p>
                </div>
            </div>
        </div>

        {{-- Dotted separator --}}
        <div class="relative mx-6 my-4">
            <div class="border-t-2 border-dashed border-gray-200"></div>
            <div class="absolute -left-10 top-1/2 -translate-y-1/2 w-7 h-7 rounded-full bg-[#f3f6fd]"></div>
            <div class="absolute -right-10 top-1/2 -translate-y-1/2 w-7 h-7 rounded-full bg-[#f3f6fd]"></div>
        </div>

        {{-- Passenger & Class Info --}}
        <div class="px-10 pb-6">
            <div class="grid grid-cols-4 gap-4 mb-6">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide font-medium">Passenger(s)</p>
                    <p class="font-bold text-gray-900 mt-1">{{ $transaction->number_of_passengers }} Pax</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide font-medium">Class</p>
                    <p class="font-bold text-gray-900 mt-1">{{ \Str::ucfirst($transaction->flightClass?->class_type ?? 'Economy') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide font-medium">Payment</p>
                    <p class="font-bold text-gray-900 mt-1">{{ \Str::ucfirst($transaction->payment_method ?? 'Xendit') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide font-medium">Grand Total</p>
                    <p class="font-bold text-garuda-blue mt-1">Rp. {{ number_format($transaction->grandtotal, 0, ',', '.') }}</p>
                </div>
            </div>

            {{-- Passenger list --}}
            <div class="bg-[#f8faff] rounded-2xl p-5 mb-6">
                <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold mb-3">Passenger Details</p>
                <div class="flex flex-col gap-3">
                    @foreach ($transaction->passengers as $idx => $pax)
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-garuda-blue text-white flex items-center justify-center text-sm font-bold">
                                {{ $idx + 1 }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $pax->name }}</p>
                                <p class="text-xs text-gray-400">
                                    {{ \Carbon\Carbon::parse($pax->date_of_birth)->format('d M Y') }} · {{ $pax->nationality }}
                                </p>
                            </div>
                        </div>
                        <p class="text-sm font-semibold text-garuda-blue">{{ $pax->seat?->name ?? 'N/A' }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- QR Code + Booking Code --}}
            <div class="flex items-center justify-between gap-6 bg-[linear-gradient(135deg,#f0f7ff,#e8f4ff)] rounded-2xl p-5">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold mb-1">Booking Code</p>
                    <p class="text-2xl font-black text-garuda-blue tracking-widest">{{ $transaction->code }}</p>
                    <p class="text-xs text-gray-500 mt-2">Tunjukkan QR code ini kepada petugas bandara</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $transaction->email }}</p>
                </div>
                <div class="shrink-0 p-2 bg-white rounded-xl shadow-sm">
                    {!! QrCode::size(130)->style('round')->color(0, 71, 204)->generate(
                        url('/check-booking') . '?code=' . $transaction->code
                    ) !!}
                </div>
            </div>
        </div>

        {{-- Footer Ticket --}}
        <div class="bg-gray-50 px-10 py-5 flex items-center justify-between">
            <div class="flex items-center gap-2 text-gray-400 text-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Tiket ini sah sebagai bukti pembelian. Harap simpan dengan baik.</span>
            </div>
            <button onclick="window.print()"
                class="flex items-center gap-2 px-5 py-2.5 rounded-full bg-garuda-blue text-white text-sm font-semibold hover:shadow-[0px_8px_20px_0px_#0068FF55] transition-all duration-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print Ticket
            </button>
        </div>
    </div>

    {{-- Action buttons --}}
    <div class="flex gap-4 mt-8">
        <a href="{{ route('home') }}"
            class="flex items-center gap-2 px-6 py-3 rounded-full border-2 border-garuda-blue text-garuda-blue font-semibold hover:bg-garuda-blue hover:text-white transition-all duration-300">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Kembali ke Beranda
        </a>
        <a href="{{ route('booking.check') }}"
            class="flex items-center gap-2 px-6 py-3 rounded-full bg-garuda-blue text-white font-semibold hover:shadow-[0px_14px_30px_0px_#0068FF66] transition-all duration-300">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Cek Booking Lain
        </a>
    </div>
</main>

<style>
@media print {
    #Background, nav, .fixed, button:not(.print-hide), a { display: none !important; }
    #ticket-card { box-shadow: none !important; border: 1px solid #ddd; }
    main { padding: 0 !important; margin: 0 !important; }
    #success-alert { display: none !important; }
}
@keyframes bounce-once {
    0%, 100% { transform: translateY(0); }
    30% { transform: translateY(-8px); }
    60% { transform: translateY(-4px); }
}
.animate-bounce-once { animation: bounce-once 0.8s ease-out; }
</style>
@endsection
