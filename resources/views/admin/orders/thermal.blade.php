<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota {{ $order->order_number }}</title>
    <style>
        body {
            font-family: "Courier New", Courier, monospace;
            margin: 0;
            padding: 0;
            color: #111;
            background: #fff;
            font-size: 12px;
        }
        .paper {
            width: 80mm;
            max-width: 80mm;
            margin: 0 auto;
            padding: 10px 8px 14px;
            box-sizing: border-box;
        }
        .center { text-align: center; }
        .mt-8 { margin-top: 8px; }
        .mt-12 { margin-top: 12px; }
        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }
        .row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 8px;
        }
        .muted { color: #444; font-size: 11px; }
        .strong { font-weight: 700; }
        .item-name {
            width: 65%;
            word-break: break-word;
        }
        .item-total {
            width: 35%;
            text-align: right;
        }
        @media print {
            @page { size: 80mm auto; margin: 0; }
            body { margin: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="paper">
        <div class="center strong">ORDER KITB</div>
        <div class="center muted">Kantin Industri Batang</div>
        <div class="divider"></div>

        <div class="row">
            <span>No</span>
            <span class="strong">{{ $order->order_number }}</span>
        </div>
        <div class="row">
            <span>Waktu</span>
            <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
        </div>
        <div class="row">
            <span>Meja</span>
            <span>{{ $order->table_number ?? '-' }}</span>
        </div>
        <div class="row">
            <span>Pelanggan</span>
            <span>{{ $order->customer_name }}</span>
        </div>
        <div class="divider"></div>

        @foreach($order->items as $item)
            <div class="row mt-8">
                <div class="item-name">
                    {{ $item->menuItem?->name ?? 'Menu' }} x{{ $item->quantity }}
                </div>
                <div class="item-total">
                    Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}
                </div>
            </div>
        @endforeach

        <div class="divider"></div>
        <div class="row">
            <span>Subtotal</span>
            <span>Rp {{ number_format((float) $order->subtotal, 0, ',', '.') }}</span>
        </div>
        <div class="row">
            <span>Layanan</span>
            <span>Rp {{ number_format((float) $order->service_fee, 0, ',', '.') }}</span>
        </div>
        <div class="row">
            <span>Pengiriman</span>
            <span>Rp {{ number_format((float) $order->delivery_fee, 0, ',', '.') }}</span>
        </div>
        <div class="row strong mt-8">
            <span>TOTAL</span>
            <span>{{ $order->formatted_total }}</span>
        </div>
        <div class="divider"></div>
        <div class="row">
            <span>Pembayaran</span>
            <span>{{ $order->payment_method_label }}</span>
        </div>
        <div class="row">
            <span>Status</span>
            <span>{{ $order->payment_status_label }}</span>
        </div>

        <div class="center mt-12 muted">
            Terima kasih
        </div>

        <div class="center mt-12 no-print">
            <button onclick="window.print()">Print Ulang</button>
        </div>
    </div>

    <script>
        const shouldAutoPrint = new URLSearchParams(window.location.search).get('autoprint') === '1';
        if (shouldAutoPrint) {
            window.addEventListener('load', () => {
                setTimeout(() => window.print(), 250);
            });
        }
    </script>
</body>
</html>
