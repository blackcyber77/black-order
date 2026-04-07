<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $order->order_number }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
            background-color: #fff;
            width: 100%;
        }
        .receipt-container {
            width: 58mm; /* Auto adapt to thermal 58mm, change to 80mm if needed */
            max-width: 100%;
            margin: 0 auto;
            padding: 10px;
            box-sizing: border-box;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .border-top { border-top: 1px dashed #000; margin-top: 5px; padding-top: 5px; }
        .border-bottom { border-bottom: 1px dashed #000; margin-bottom: 5px; padding-bottom: 5px; }
        .mb-1 { margin-bottom: 5px; }
        .mb-2 { margin-bottom: 10px; }
        .mt-2 { margin-top: 10px; }
        
        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; }
        .w-full { width: 100%; }
        
        .item-name { padding-right: 5px; }
        .item-qty { padding-right: 5px; white-space: nowrap; }
        .item-price { text-align: right; white-space: nowrap; }
        
        @media print {
            .no-print { display: none; }
            body { width: 58mm; }
        }
    </style>
</head>
<body onload="window.print()">

<div class="receipt-container">
    
    <!-- Header -->
    <div class="text-center mb-2 font-bold">
        <h2 style="margin: 0; font-size: 16px;">KANTIN KITA</h2>
        <p style="margin: 2px 0 0; font-size: 10px;">Kawasan Industri Terpadu Batang</p>
    </div>
    
    <div class="border-bottom text-center mb-2">
        <p style="margin: 0;">No: {{ $order->order_number }}</p>
        <p style="margin: 0;">{{ $order->created_at->format('d/m/Y H:i') }}</p>
    </div>

    <!-- Customer Info -->
    <div class="mb-2">
        <p style="margin: 0;">Kasir : {{ $order->cashier?->name ?? 'Admin' }}</p>
        <p style="margin: 0;">Pelanggan : {{ $order->customer_name }}</p>
        <p style="margin: 0;">Lokasi : {{ $order->table_number ? 'Meja ' . $order->table_number : 'Walk-In / Takeaway' }}</p>
    </div>

    <div class="border-top mb-1"></div>

    <!-- Items -->
    <table class="mb-1 border-bottom">
        @foreach($order->items as $item)
        <tr>
            <td colspan="3" class="font-bold">{{ $item->menuItem->name ?? 'Item Terhapus' }}</td>
        </tr>
        <tr>
            <td class="item-qty">{{ $item->quantity }}x</td>
            <td class="item-name">{{ number_format($item->price, 0, ',', '.') }}</td>
            <td class="item-price">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </table>

    <!-- Totals -->
    <table>
        <tr>
            <td>Subtotal</td>
            <td class="text-right">{{ number_format($order->subtotal, 0, ',', '.') }}</td>
        </tr>
        @if($order->service_fee > 0)
        <tr>
            <td>Layanan</td>
            <td class="text-right">{{ number_format($order->service_fee, 0, ',', '.') }}</td>
        </tr>
        @endif
        @if($order->delivery_fee > 0)
        <tr>
            <td>Ongkir</td>
            <td class="text-right">{{ number_format($order->delivery_fee, 0, ',', '.') }}</td>
        </tr>
        @endif
        <tr class="font-bold border-top">
            <td style="padding-top: 5px;">TOTAL</td>
            <td class="text-right" style="padding-top: 5px;">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="mt-2 text-center" colspan="2">
                <br>
                Metode: {{ $order->payment_method_label }} ({{ $order->payment_status === 'paid' || $order->payment_status === 'verified' ? 'LUNAS' : 'BELUM LUNAS' }})
            </td>
        </tr>
    </table>
    
    <div class="border-top mt-2"></div>
    
    <!-- Footer -->
    <div class="text-center mt-2" style="font-size: 10px;">
        <p style="margin: 0;">Terima kasih atas kunjungan Anda!</p>
        <p style="margin: 0;">Simpan struk ini sebagai bukti pembayaran yang sah.</p>
    </div>

    <!-- Action Buttons for normal view -->
    <div class="no-print text-center" style="margin-top: 20px;">
        <button onclick="window.print()" style="padding: 8px 16px; background-color: #f97316; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; margin-bottom: 10px;">
            Cetak Struk
        </button>
        <br>
        <a href="{{ route('admin.pos.index') }}" style="color: #64748b; text-decoration: none;">Kembali ke POS</a>
    </div>

</div>

</body>
</html>
