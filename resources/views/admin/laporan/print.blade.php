<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan {{ $from }} - {{ $to }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 11px; color: #333; padding: 20px; }
        .container { max-width: 1100px; margin: 0 auto; }
        .header { text-align: center; border-bottom: 3px solid #e53e3e; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { font-size: 22px; color: #e53e3e; font-weight: 800; }
        .header p { color: #666; margin-top: 4px; font-size: 12px; }
        .summary { display: flex; gap: 15px; margin-bottom: 20px; }
        .summary-card { flex: 1; border: 1px solid #eee; border-radius: 8px; padding: 12px; text-align: center; }
        .summary-card .label { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: #999; }
        .summary-card .value { font-size: 16px; font-weight: 800; color: #111; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th { background: #f7f7f7; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; color: #666; padding: 8px 6px; text-align: left; border-bottom: 2px solid #e5e5e5; }
        td { padding: 7px 6px; border-bottom: 1px solid #eee; font-size: 10px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { background: #f7f7f7; font-weight: 800; }
        .total-row td { border-top: 2px solid #333; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 12px; }
        .no-print { text-align: center; margin-bottom: 20px; }
        @media print { .no-print { display: none; } @page { margin: 10mm; size: landscape; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="no-print">
            <button onclick="window.print()" style="padding: 10px 30px; background: #e53e3e; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px;">Cetak Laporan</button>
        </div>

        <div class="header">
            <h1>LAPORAN TRANSAKSI</h1>
            <p>ModifikasiOri Bengkel</p>
            <p style="margin-top: 8px; font-weight: 600; color: #333;">Periode: {{ \Carbon\Carbon::parse($from)->format('d M Y') }} — {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</p>
        </div>

        <div class="summary">
            <div class="summary-card">
                <div class="label">Jumlah Invoice</div>
                <div class="value">{{ $invoices->count() }}</div>
            </div>
            <div class="summary-card">
                <div class="label">Total Omset</div>
                <div class="value" style="color: #e53e3e;">Rp {{ number_format($totalOmset, 0, ',', '.') }}</div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Kode Servis</th>
                    <th>No. Invoice</th>
                    <th>Tanggal</th>
                    <th>Pelanggan</th>
                    <th>Mekanik</th>
                    <th>No. HP</th>
                    <th>No. Polisi</th>
                    <th>Kendaraan</th>
                    <th>Tahun</th>
                    <th class="text-center">Metode</th>
                    <th class="text-right">Grand Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $i => $inv)
                @php $order = $inv->serviceOrder; @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $order->kode_servis ?? '-' }}</td>
                    <td style="font-weight: 600;">{{ $inv->kode_invoice }}</td>
                    <td>{{ $inv->tanggal->format('d/m/Y') }}</td>
                    <td>{{ $order->pelanggan->nama ?? '-' }}</td>
                    <td>{{ $order->mekanik->nama ?? '-' }}</td>
                    <td>{{ $order->pelanggan->no_hp ?? '-' }}</td>
                    <td style="font-weight: 600;">{{ $order->kendaraan->nomor_polisi ?? '-' }}</td>
                    <td>{{ $order->kendaraan->merk ?? '' }} {{ $order->kendaraan->tipe ?? '' }}</td>
                    <td>{{ $order->kendaraan->tahun ?? '-' }}</td>
                    <td class="text-center">{{ ucfirst($inv->metode_pembayaran) }}</td>
                    <td class="text-right" style="font-weight: 600;">Rp {{ number_format($inv->grand_total, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="11" class="text-right">Total Omset</td>
                    <td class="text-right" style="color: #e53e3e; font-size: 12px;">Rp {{ number_format($totalOmset, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="footer">
            <p>Dicetak pada {{ now()->format('d M Y H:i') }} — ModifikasiOri Bengkel</p>
        </div>
    </div>
</body>
</html>
