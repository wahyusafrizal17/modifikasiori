<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->kode_invoice }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 12px; color: #333; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid #e53e3e; padding-bottom: 20px; margin-bottom: 20px; }
        .header h1 { font-size: 28px; color: #e53e3e; font-weight: 800; }
        .header .meta { text-align: right; font-size: 11px; color: #666; line-height: 1.6; }
        .header .meta strong { color: #333; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px; }
        .info-box h3 { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #999; margin-bottom: 8px; }
        .info-box p { line-height: 1.7; }
        .info-box p strong { color: #111; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #f7f7f7; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; color: #666; padding: 10px 12px; text-align: left; border-bottom: 2px solid #e5e5e5; }
        td { padding: 10px 12px; border-bottom: 1px solid #eee; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .summary { margin-left: auto; width: 300px; }
        .summary-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 12px; }
        .summary-row.total { border-top: 2px solid #333; padding-top: 12px; margin-top: 6px; font-size: 16px; font-weight: 800; color: #e53e3e; }
        .footer { margin-top: 40px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 15px; }
        @media print { body { padding: 0; } .no-print { display: none; } @page { margin: 15mm; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="no-print" style="text-align: center; margin-bottom: 20px;">
            <button onclick="window.print()" style="padding: 10px 30px; background: #e53e3e; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px;">Cetak Invoice</button>
        </div>

        @php $order = $invoice->serviceOrder; @endphp

        <div class="header">
            <div>
                <h1>INVOICE</h1>
                <p style="color: #666; margin-top: 4px;">ModifikasiOri Bengkel</p>
            </div>
            <div class="meta">
                <p><strong>{{ $invoice->kode_invoice }}</strong></p>
                <p>Tanggal: {{ $invoice->tanggal->format('d M Y') }}</p>
                <p>Work Order: {{ $order->kode_servis }}</p>
                <p>Pembayaran: {{ ucfirst($invoice->metode_pembayaran) }}</p>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-box">
                <h3>Pelanggan</h3>
                <p>
                    <strong>{{ $order->pelanggan->nama }}</strong><br>
                    {{ $order->pelanggan->no_hp ?? '' }}<br>
                    {{ $order->pelanggan->alamat ?? '' }}
                    @if($order->pelanggan->kota)<br>{{ $order->pelanggan->kota->nama }}@endif
                </p>
            </div>
            <div class="info-box">
                <h3>Kendaraan</h3>
                <p>
                    <strong>{{ $order->kendaraan->nomor_polisi }}</strong><br>
                    {{ $order->kendaraan->merk }} {{ $order->kendaraan->tipe }}<br>
                    Tahun {{ $order->kendaraan->tahun ?? '-' }}<br>
                    Mekanik: {{ $order->mekanik->nama ?? '-' }}
                </p>
            </div>
        </div>

        @if($order->jasaServis->count())
        <table>
            <thead><tr><th>Jasa Servis</th><th class="text-right">Biaya</th></tr></thead>
            <tbody>
                @foreach($order->jasaServis as $j)
                <tr><td>{{ $j->nama }}</td><td class="text-right">Rp {{ number_format($j->pivot->biaya, 0, ',', '.') }}</td></tr>
                @endforeach
            </tbody>
        </table>
        @endif

        @if($order->products->count())
        <table>
            <thead><tr><th>Sparepart</th><th class="text-center">Qty</th><th class="text-right">Harga</th><th class="text-right">Subtotal</th></tr></thead>
            <tbody>
                @foreach($order->products as $p)
                <tr>
                    <td>{{ $p->nama_produk }}</td>
                    <td class="text-center">{{ $p->pivot->qty }}</td>
                    <td class="text-right">Rp {{ number_format($p->pivot->harga, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($p->pivot->qty * $p->pivot->harga, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <div class="summary">
            <div class="summary-row"><span>Total Jasa</span><span>Rp {{ number_format($invoice->total_jasa, 0, ',', '.') }}</span></div>
            <div class="summary-row"><span>Total Sparepart</span><span>Rp {{ number_format($invoice->total_sparepart, 0, ',', '.') }}</span></div>
            @if($invoice->diskon > 0)
            <div class="summary-row"><span>Diskon</span><span>- Rp {{ number_format($invoice->diskon, 0, ',', '.') }}</span></div>
            @endif
            <div class="summary-row total"><span>Grand Total</span><span>Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</span></div>
        </div>

        @if($invoice->catatan)
        <div style="margin-top: 25px; padding: 12px; background: #f9f9f9; border-radius: 6px;">
            <p style="font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #999; margin-bottom: 4px;">Catatan</p>
            <p>{{ $invoice->catatan }}</p>
        </div>
        @endif

        <div class="footer">
            <p>Terima kasih telah mempercayakan kendaraan Anda kepada kami.</p>
            <p style="margin-top: 4px;">ModifikasiOri Bengkel &mdash; {{ now()->format('Y') }}</p>
        </div>
    </div>
</body>
</html>
