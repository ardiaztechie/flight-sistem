@extends('layouts.app')

@section('include')
<div id="Background" class="absolute top-0 w-full" style="height: 500px; background: linear-gradient(180deg, #0047CC 0%, #85C8FF 100%);"></div>
@endsection

@section('content')
<main class="relative flex flex-col items-center w-full max-w-5xl px-4 md:px-12 mx-auto mt-12 mb-20" style="max-width: 1024px; padding-left: 20px; padding-right: 20px; margin-top: 50px;">

    {{-- ⚠️ WARNING MENDESAK UNTUK DOWNLOAD/PRINT --}}
    <div class="w-full mb-8 p-6 rounded-3xl shadow-lg relative overflow-hidden print-hide" style="background: linear-gradient(90deg, #dc2626, #f97316); color: white; border: 2px solid rgba(255,255,255,0.4);">
        <div class="flex flex-col md:flex-row items-center gap-6 text-center md:text-left" style="display: flex; align-items: center; gap: 24px;">
            <div class="rounded-full bg-white flex items-center justify-center shrink-0 shadow-inner" style="width: 70px; height: 70px;">
                <svg style="width: 40px; height: 40px; color: #dc2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div style="flex: 1;">
                <h2 class="font-black text-2xl uppercase tracking-wide" style="font-size: 24px; font-weight: 900; margin-bottom: 5px;">Penting! Simpan Tiket Anda</h2>
                <p class="font-medium opacity-90 leading-snug" style="font-size: 16px;">
                    Harap segera <strong>SCREENSHOT / DOWNLOAD GAMBAR</strong>, atau <strong>CETAK SEBAGAI PDF</strong> halaman tiket ini agar tidak hilang. Ini adalah bukti sah keberangkatan Anda.
                </p>
                <div class="mt-4 flex gap-3" style="margin-top: 15px;">
                    <button onclick="window.print()" class="font-bold rounded-full text-sm shadow-lg flex items-center gap-2" style="background-color: white; color: #dc2626; padding: 12px 24px; border: none; cursor: pointer;">
                        Cetak PDF / Print Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ Alert Pembayaran Berhasil --}}
    <div class="w-full mb-8 flex items-center gap-4 p-5 rounded-2xl text-white shadow-lg print-hide" style="background-color: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.4);">
        <div class="flex items-center justify-center rounded-full shrink-0 shadow-md" style="width: 45px; height: 45px; background-color: #4ade80;">
            <svg style="width: 25px; height: 25px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <div>
            <p class="font-bold text-lg">Pembayaran Berhasil! 🎉</p>
            <p class="text-sm" style="color: #eff6ff;">Tiket Anda telah dikonfirmasi. Kode booking: <strong>{{ $transaction->code }}</strong></p>
        </div>
    </div>

    {{-- ══════════════════ TICKET CARD (RESPONSIVE) ══════════════════ --}}
    <div id="ticket-card" class="w-full flex flex-col md:flex-row bg-white rounded-3xl relative overflow-hidden" style="border-radius: 28px; box-shadow: 0px 20px 60px 0px rgba(0,0,0,0.15); display: flex;">

        {{-- LEFT SIDE (Main Info) --}}
        <div style="flex: 7; position: relative; display: flex; flex-direction: column;">
            
            {{-- Header Airline --}}
            <div class="p-8 text-white relative overflow-hidden print-bg-blue" style="background: linear-gradient(135deg, #0047CC 0%, #0068FF 100%); padding: 30px;">
                <div class="flex justify-between items-center gap-4" style="display: flex; justify-content: space-between; align-items: center;">
                    <div class="flex items-center gap-4" style="display: flex; align-items: center; gap: 15px;">
                        <div class="rounded-2xl bg-white flex items-center justify-center p-2 shadow-md" style="width: 60px; height: 60px; background: white; border-radius: 15px;">
                            <img src="{{ asset('storage/' . $transaction->flight->airline->logo) }}"
                                class="w-full h-full object-contain" style="max-width: 100%; max-height: 100%;"
                                alt="{{ $transaction->flight->airline->name }}"
                                onerror="this.style.display='none';">
                        </div>
                        <div>
                            <p class="font-black text-2xl uppercase" style="font-weight: 900; font-size: 24px; margin: 0;">{{ $transaction->flight->airline->name }}</p>
                            <p class="font-medium" style="font-size: 14px; color: #dbeafe; margin: 0;">Flight {{ $transaction->flight->flight_number }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="font-bold uppercase" style="display: inline-block; padding: 4px 12px; background-color: rgba(34, 197, 94, 0.2); color: #86efac; border: 1px solid rgba(74, 222, 128, 0.3); border-radius: 50px; font-size: 12px; letter-spacing: 1px;">
                            PAID
                        </span>
                        <p style="font-size: 10px; color: #bfdbfe; margin-top: 5px; text-transform: uppercase; letter-spacing: 1px;">E-Ticket</p>
                    </div>
                </div>
            </div>

            {{-- Body Info --}}
            <div class="p-8 flex-1 bg-white relative" style="padding: 30px; background-color: white;">
                @php
                    $segments  = $transaction->flight->segments->sortBy('sequence');
                    $departure = $segments->first();
                    $arrival   = $segments->last();
                @endphp
                
                {{-- Route --}}
                <div class="flex items-center justify-between mb-8" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                    {{-- Dep --}}
                    <div style="width: 30%;">
                        <p class="font-black text-gray-900" style="font-size: 40px; font-weight: 900; margin: 0;">{{ $departure->airport->iata_code }}</p>
                        <p class="font-bold text-gray-700" style="font-weight: 700; margin-top: 5px; font-size: 14px;">{{ $departure->airport->city }}</p>
                        <p class="font-bold text-garuda-blue" style="color: #0068FF; font-weight: 700; margin-top: 15px; font-size: 16px;">{{ $departure->time->format('H:i') }}</p>
                        <p class="text-gray-500 font-semibold" style="font-size: 12px; color: #6b7280; font-weight: 600;">{{ $departure->time->format('d M Y') }}</p>
                    </div>
                    
                    {{-- Plane Icon & Line --}}
                    <div style="width: 40%; text-align: center; padding: 0 10px;">
                        <p class="font-bold uppercase" style="font-size: 10px; color: #9ca3af; letter-spacing: 1px; margin-bottom: 8px;">
                            {{ number_format($departure->time->diffInHours($arrival->time), 0) }}H {{ $departure->time->diff($arrival->time)->i }}M
                        </p>
                        <div style="display: flex; align-items: center; justify-content: center; width: 100%;">
                            <div style="height: 2px; flex: 1; background-color: #e5e7eb;"></div>
                            <svg style="width: 24px; height: 24px; color: #0068FF; margin: 0 10px; transform: rotate(45deg);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            <div style="height: 2px; flex: 1; background-color: #e5e7eb;"></div>
                        </div>
                        @if ($segments->count() > 2)
                            <p class="font-bold uppercase" style="font-size: 10px; color: #f97316; margin-top: 8px;">Transit {{ $segments->count() - 2 }}x</p>
                        @else
                            <p class="font-bold uppercase" style="font-size: 10px; color: #22c55e; margin-top: 8px;">Direct</p>
                        @endif
                    </div>
                    
                    {{-- Arr --}}
                    <div style="width: 30%; text-align: right;">
                        <p class="font-black text-gray-900" style="font-size: 40px; font-weight: 900; margin: 0;">{{ $arrival->airport->iata_code }}</p>
                        <p class="font-bold text-gray-700" style="font-weight: 700; margin-top: 5px; font-size: 14px;">{{ $arrival->airport->city }}</p>
                        <p class="font-bold text-garuda-blue" style="color: #0068FF; font-weight: 700; margin-top: 15px; font-size: 16px;">{{ $arrival->time->format('H:i') }}</p>
                        <p class="text-gray-500 font-semibold" style="font-size: 12px; color: #6b7280; font-weight: 600;">{{ $arrival->time->format('d M Y') }}</p>
                    </div>
                </div>

                {{-- Passengers --}}
                <div class="rounded-2xl border" style="background-color: #f8faff; padding: 20px; border: 1px solid #f3f4f6; border-radius: 16px;">
                    <p class="font-bold uppercase" style="font-size: 10px; color: #9ca3af; letter-spacing: 1px; margin-bottom: 15px; margin-top: 0;">Passenger Details</p>
                    
                    {{-- Gunakan flex column jika kurang lebar, atau biarkan --}}
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                        @foreach ($transaction->passengers as $idx => $pax)
                        <div class="rounded-xl shadow-sm" style="background: white; border: 1px solid #f9fafb; padding: 12px; display: flex; align-items: flex-start; gap: 12px; border-radius: 12px;">
                            <div class="rounded-full flex items-center justify-center font-bold" style="width: 25px; height: 25px; background-color: rgba(0, 104, 255, 0.1); color: #0068FF; font-size: 12px; flex-shrink: 0; border-radius: 50%;">
                                {{ $idx + 1 }}
                            </div>
                            <div style="flex: 1; overflow: hidden;">
                                <p class="font-bold text-gray-900" style="font-weight: 700; font-size: 14px; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $pax->name }}</p>
                                <p class="font-medium" style="font-size: 11px; color: #6b7280; margin: 2px 0 0 0;">
                                    {{ $pax->nationality }}
                                </p>
                            </div>
                            <div style="text-align: right;">
                                <p class="font-bold uppercase" style="font-size: 10px; color: #9ca3af; margin: 0;">Seat</p>
                                <p class="font-black text-garuda-blue" style="font-size: 14px; font-weight: 900; color: #0068FF; margin: 0;">{{ $pax->seat?->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Garis Pemisah (Border) --}}
        <div style="border-left: 2px dashed #d1d5db; display: flex; flex-direction: column; align-items: center; justify-content: center; position: relative;">
            <div style="width: 30px; height: 30px; background-color: #85C8FF; border-radius: 50%; position: absolute; top: -15px; left: -16px; box-shadow: inset 0 -3px 5px rgba(0,0,0,0.05);" class="print-hide"></div>
            <div style="width: 30px; height: 30px; background-color: #85C8FF; border-radius: 50%; position: absolute; bottom: -15px; left: -16px; box-shadow: inset 0 3px 5px rgba(0,0,0,0.05);" class="print-hide"></div>
        </div>

        {{-- RIGHT SIDE (QR & Meta) --}}
        <div style="flex: 3; background-color: #f8fafc; display: flex; flex-direction: column; position: relative;">
            
            <div style="padding: 30px; flex: 1; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center;">
                
                {{-- Info Grid --}}
                <div style="width: 100%; display: grid; grid-template-columns: 1fr 1fr; gap: 20px; text-align: left; margin-bottom: 30px;">
                    <div>
                        <p class="font-bold uppercase" style="font-size: 10px; color: #9ca3af; letter-spacing: 1px; margin: 0;">Class</p>
                        <p class="font-bold text-gray-900" style="font-size: 14px; font-weight: 700; margin: 5px 0 0 0;">{{ \Str::ucfirst($transaction->flightClass?->class_type ?? 'Economy') }}</p>
                    </div>
                    <div>
                        <p class="font-bold uppercase" style="font-size: 10px; color: #9ca3af; letter-spacing: 1px; margin: 0;">Pax</p>
                        <p class="font-bold text-gray-900" style="font-size: 14px; font-weight: 700; margin: 5px 0 0 0;">{{ $transaction->number_of_passengers }} Person</p>
                    </div>
                    <div style="grid-column: span 2; border-top: 1px solid #e5e7eb; padding-top: 15px;">
                        <p class="font-bold uppercase" style="font-size: 10px; color: #9ca3af; letter-spacing: 1px; margin: 0;">Total Payment</p>
                        <p class="font-bold text-garuda-blue" style="font-size: 16px; font-weight: 700; color: #0068FF; margin: 5px 0 0 0;">Rp {{ number_format($transaction->grandtotal, 0, ',', '.') }}</p>
                    </div>
                </div>

                {{-- QR Code --}}
                <div style="background-color: white; padding: 15px; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; display: inline-block; margin-bottom: 20px;">
                    {!! QrCode::size(140)->style('round')->color(0, 71, 204)->generate(
                        url('/check-booking') . '?booking_trx_id=' . $transaction->code . '&phone=' . $transaction->phone
                    ) !!}
                </div>
                
                <p class="font-bold uppercase" style="font-size: 10px; color: #9ca3af; letter-spacing: 1px; margin: 0 0 5px 0;">Booking Code</p>
                <p class="font-black text-gray-900" style="font-size: 30px; font-weight: 900; letter-spacing: 2px; margin: 0;">{{ $transaction->code }}</p>
                
            </div>
            
            {{-- Footer info stub --}}
            <div style="background-color: rgba(0, 104, 255, 0.05); padding: 15px; text-align: center; border-top: 1px solid rgba(0, 104, 255, 0.1);">
                <p class="font-medium" style="font-size: 11px; color: #6b7280; margin: 0;">
                    Tunjukkan QR code ini saat check-in bandara.
                </p>
            </div>
        </div>
    </div>

    {{-- Action buttons --}}
    <div class="flex gap-4 mt-8 justify-center print-hide" style="display: flex; gap: 15px; margin-top: 30px; justify-content: center;">        
        <a href="{{ route('home') }}"
            class="font-bold text-lg rounded-full transition-all duration-300"
            style="display: flex; align-items: center; justify-content: center; gap: 10px; padding: 15px 30px; background-color: white; color: #0068FF; border: 2px solid #0068FF; border-radius: 50px; text-decoration: none;">
            Selesai & Ke Beranda
        </a>
    </div>
</main>

<style>
@media print {
    /* Format kertas & hilangkan elemen yg tidak perlu */
    @page { size: auto; margin: 0mm; }
    body { background: white !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; margin: 0; padding: 20px;}
    
    #Background, nav, .print-hide { display: none !important; }
    
    main { padding: 0 !important; margin: 0 auto !important; max-width: 100% !important; align-items: flex-start !important;}
    
    #ticket-card { 
        box-shadow: none !important; 
        border: 2px solid #cbd5e1 !important; 
        filter: none !important; 
        page-break-inside: avoid;
    }
    
    .print-bg-blue { background-color: #0068FF !important; }
    .print-bg-blue * { color: white !important; }
}

@media (max-width: 768px) {
    #ticket-card {
        flex-direction: column !important;
    }
    #ticket-card > div:nth-child(2) {
        border-left: none !important;
        border-top: 2px dashed #d1d5db !important;
        width: 100% !important;
    }
    #ticket-card > div:nth-child(2) > div:first-child { top: -16px !important; left: -15px !important; }
    #ticket-card > div:nth-child(2) > div:last-child { top: -16px !important; right: -15px !important; left: auto !important; bottom: auto !important; }
}
</style>
@endsection
