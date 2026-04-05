@extends('layouts.admin')

@section('title', 'QR Code - ' . $table->table_number)

@section('content')
<div class="max-w-md mx-auto py-12">
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden text-center relative print:shadow-none print:border-2 print:border-black">
        <!-- Header -->
        <div class="bg-navy-900 text-white p-6 relative overflow-hidden print:bg-black">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full blur-2xl -mr-10 -mt-10"></div>
            <h3 class="text-2xl font-bold mb-1">SCAN UNTUK MEMESAN</h3>
            <p class="text-orange-200 text-sm font-medium tracking-widest uppercase">Kantin Industri Batang</p>
        </div>

        <!-- Body -->
        <div class="p-8 flex flex-col items-center">
            
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6 print:border-black print:shadow-none flex justify-center">
                <!-- JS QrCode Generator -->
                <div id="qrcode"></div>
            </div>

            <div class="space-y-1">
                <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Lokasi Anda</p>
                <div class="text-3xl font-bold text-navy-900 print:text-black">
                    Meja <span class="text-orange-600 print:text-black">{{ $table->table_number }}</span>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-dashed border-gray-200 w-full">
                <p class="text-sm text-gray-500">
                    Buka kamera HP Anda dan scan QR Code di atas untuk melihat menu dan memesan makanan.
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 p-4 print:hidden">
            <button onclick="window.print()" class="w-full bg-navy-900 text-white font-bold py-3 rounded-xl hover:bg-navy-800 transition shadow-lg shadow-navy-900/20">
                <i class="fas fa-print mr-2"></i> Cetak QR Code
            </button>
        </div>
    </div>
</div>

<!-- QR Code JS Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    new QRCode(document.getElementById("qrcode"), {
        text: "{!! $qrUrl !!}",
        width: 250,
        height: 250,
        colorDark : "#000000",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
    });
</script>

<style media="print">
    @page { margin: 0; size: auto; }
    body * { visibility: hidden; }
    .max-w-md, .max-w-md * { visibility: visible; }
    .max-w-md { position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); width: 100%; }
    .print\:hidden { display: none !important; }
    .print\:border-black { border-color: black !important; }
    .print\:shadow-none { box-shadow: none !important; }
    .print\:text-black { color: black !important; }
    .print\:bg-black { background-color: black !important; }
    #qrcode img { display: block; margin: 0 auto; }
</style>
@endsection
