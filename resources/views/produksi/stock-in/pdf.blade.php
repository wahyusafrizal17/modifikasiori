<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock IN - {{ $stockIn->kode }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800" rel="stylesheet" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', 'Helvetica', sans-serif; font-size: 13px; color: #1f2937; background: #f3f4f6; }

        .print-btn-wrapper { text-align: center; padding: 20px 0; }
        .print-btn { display: inline-flex; align-items: center; gap: 8px; background: #dc2626; color: #fff; border: none; padding: 10px 28px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; font-family: inherit; transition: background 0.2s; }
        .print-btn:hover { background: #b91c1c; }
        .print-btn svg { width: 18px; height: 18px; }

        .page { max-width: 800px; margin: 0 auto 40px; background: #fff; padding: 50px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }

        /* Header */
        .header { margin-bottom: 30px; overflow: hidden; }
        .header-left { float: left; }
        .header-right { float: right; text-align: right; }
        .doc-title { font-size: 28px; font-weight: 800; color: #dc2626; letter-spacing: -0.5px; }
        .doc-subtitle { font-size: 13px; color: #6b7280; margin-top: 2px; }
        .doc-kode { font-size: 14px; font-weight: 700; color: #1f2937; }
        .doc-meta { font-size: 11px; color: #6b7280; margin-top: 2px; line-height: 1.6; }

        /* Info Section */
        .info-section { border: 1px solid #e5e7eb; border-radius: 6px; overflow: hidden; margin-bottom: 25px; }
        .info-row { display: table; width: 100%; }
        .info-col { display: table-cell; padding: 18px 22px; width: 50%; vertical-align: top; }
        .info-col + .info-col { border-left: 1px solid #e5e7eb; }
        .info-col-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #9ca3af; margin-bottom: 8px; }
        .info-col-value { font-size: 13px; font-weight: 700; color: #1f2937; }
        .info-col-sub { font-size: 12px; color: #6b7280; margin-top: 2px; line-height: 1.5; }

        /* Rejection */
        .reject-section { border: 1px solid #fecaca; border-radius: 6px; background: #fef2f2; padding: 15px 22px; margin-bottom: 25px; }
        .reject-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #dc2626; margin-bottom: 4px; }
        .reject-value { font-size: 13px; font-weight: 600; color: #991b1b; }
        .reject-note { font-size: 12px; color: #991b1b; margin-top: 4px; }

        /* Table */
        table.items { width: 100%; border-collapse: collapse; }
        table.items th { background: #f9fafb; padding: 10px 15px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #9ca3af; text-align: left; border-bottom: 2px solid #e5e7eb; }
        table.items td { padding: 12px 15px; font-size: 12px; border-bottom: 1px solid #f3f4f6; }
        table.items tbody tr:last-child td { border-bottom: 2px solid #e5e7eb; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .fw-600 { font-weight: 600; }
        .fw-700 { font-weight: 700; }

        .badge { display: inline-block; padding: 3px 10px; border-radius: 4px; font-size: 10px; font-weight: 600; }
        .badge-blue { background: #dbeafe; color: #1e40af; }
        .badge-purple { background: #ede9fe; color: #6b21a8; }

        /* Summary */
        .summary { margin-top: 15px; overflow: hidden; }
        .summary-table { float: right; width: 280px; }
        .summary-table tr td { padding: 5px 0; font-size: 12px; }
        .summary-table .label { color: #6b7280; }
        .summary-table .value { text-align: right; font-weight: 600; color: #1f2937; }
        .summary-table .grand { border-top: 2px solid #e5e7eb; padding-top: 8px; margin-top: 4px; }
        .summary-table .grand .label { font-size: 14px; font-weight: 700; color: #dc2626; }
        .summary-table .grand .value { font-size: 14px; font-weight: 800; color: #dc2626; }

        /* Signatures */
        .signatures { margin-top: 50px; overflow: hidden; }
        .signatures table { width: 100%; }
        .signatures td { width: 33.33%; text-align: center; vertical-align: top; padding: 0 15px; }
        .sig-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #9ca3af; }
        .sig-line { border-bottom: 1px solid #374151; margin: 60px auto 6px; width: 75%; }
        .sig-name { font-size: 12px; font-weight: 600; color: #1f2937; }
        .sig-date { font-size: 10px; color: #9ca3af; }

        /* Footer */
        .footer { margin-top: 40px; text-align: center; padding-top: 20px; border-top: 1px solid #e5e7eb; }
        .footer p { font-size: 11px; color: #9ca3af; line-height: 1.6; }

        @media print {
            body { background: #fff; }
            .print-btn-wrapper { display: none; }
            .page { box-shadow: none; margin: 0; padding: 30px; max-width: 100%; }
        }
    </style>
</head>
<body>
    <div class="print-btn-wrapper">
        <button class="print-btn" onclick="window.print()">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Cetak Stock IN
        </button>
    </div>

    <div class="page">
        {{-- Header --}}
        <div class="header">
            <div class="header-left">
                <div class="doc-title">STOCK IN</div>
                <div class="doc-subtitle">ModifikasiOri Produksi</div>
            </div>
            <div class="header-right">
                <div class="doc-kode">{{ $stockIn->kode }}</div>
                <div class="doc-meta">
                    Tanggal: {{ $stockIn->created_at->format('d M Y') }}<br>
                    Status: {{ $stockIn->status === 'pending' ? 'Menunggu Verifikasi' : ($stockIn->status === 'approved' ? 'Disetujui' : 'Ditolak') }}
                </div>
            </div>
        </div>

        {{-- Info --}}
        <div class="info-section">
            <div class="info-row">
                <div class="info-col">
                    <div class="info-col-label">Diajukan Oleh</div>
                    <div class="info-col-value">{{ $stockIn->user->name }}</div>
                    <div class="info-col-sub">{{ $stockIn->created_at->format('d M Y H:i') }}</div>
                    @if($stockIn->catatan)
                    <div class="info-col-sub" style="margin-top: 6px;">Catatan: {{ $stockIn->catatan }}</div>
                    @endif
                </div>
                <div class="info-col">
                    <div class="info-col-label">Diverifikasi Oleh</div>
                    @if($stockIn->approver)
                        <div class="info-col-value">{{ $stockIn->approver->name }}</div>
                        <div class="info-col-sub">{{ $stockIn->approved_at?->format('d M Y H:i') }}</div>
                    @else
                        <div class="info-col-sub">Belum diverifikasi</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Rejection --}}
        @if($stockIn->rejected_reason)
        @php
            $parts = explode(': ', $stockIn->rejected_reason, 2);
            $rejectCategory = $parts[0];
            $rejectNote = $parts[1] ?? '-';
        @endphp
        <div class="reject-section">
            <div class="reject-label">Alasan Penolakan</div>
            <div class="reject-value">{{ $rejectCategory }}</div>
            <div class="reject-note">Keterangan: {{ $rejectNote }}</div>
        </div>
        @endif

        {{-- Items --}}
        <table class="items">
            <thead>
                <tr>
                    <th style="width: 35px;">No</th>
                    <th style="width: 85px;">Tipe</th>
                    <th style="width: 75px;">Kode</th>
                    <th>Nama Item</th>
                    <th class="text-right" style="width: 100px;">Harga</th>
                    <th class="text-center" style="width: 60px;">Jumlah</th>
                    <th class="text-right" style="width: 110px;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stockIn->items as $i => $item)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>
                        @if(str_contains($item->itemable_type, 'BahanBaku'))
                            <span class="badge badge-blue">Bahan Baku</span>
                        @else
                            <span class="badge badge-purple">Kemasan</span>
                        @endif
                    </td>
                    <td class="fw-600">{{ $item->itemable->kode ?? '-' }}</td>
                    <td>{{ $item->itemable->nama ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($item->itemable->harga ?? 0, 0, ',', '.') }}</td>
                    <td class="text-center fw-600">{{ $item->jumlah }}</td>
                    <td class="text-right fw-600">Rp {{ number_format(($item->itemable->harga ?? 0) * $item->jumlah, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Summary --}}
        <div class="summary">
            <table class="summary-table">
                <tr>
                    <td class="label">Total Bahan Baku</td>
                    <td class="value">{{ $stockIn->items->filter(fn($i) => str_contains($i->itemable_type, 'BahanBaku'))->count() }} item</td>
                </tr>
                <tr>
                    <td class="label">Total Kemasan</td>
                    <td class="value">{{ $stockIn->items->filter(fn($i) => str_contains($i->itemable_type, 'Kemasan'))->count() }} item</td>
                </tr>
                <tr>
                    <td class="label">Total Qty</td>
                    <td class="value">{{ $stockIn->items->sum('jumlah') }}</td>
                </tr>
                <tr class="grand">
                    <td class="label">Grand Total</td>
                    <td class="value">Rp {{ number_format($stockIn->items->sum(fn($i) => ($i->itemable->harga ?? 0) * $i->jumlah), 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        {{-- Signatures --}}
        <div class="signatures">
            <table>
                <tr>
                    <td><div class="sig-label">Diajukan oleh</div></td>
                    <td><div class="sig-label">Diketahui oleh</div></td>
                    <td><div class="sig-label">{{ $stockIn->status === 'rejected' ? 'Ditolak oleh' : 'Disetujui oleh' }}</div></td>
                </tr>
                <tr>
                    <td>
                        <div class="sig-line"></div>
                        <div class="sig-name">{{ $stockIn->user->name }}</div>
                        <div class="sig-date">{{ $stockIn->created_at->format('d M Y') }}</div>
                    </td>
                    <td>
                        <div class="sig-line"></div>
                        <div class="sig-name">........................</div>
                    </td>
                    <td>
                        <div class="sig-line"></div>
                        @if($stockIn->approver)
                            <div class="sig-name">{{ $stockIn->approver->name }}</div>
                            <div class="sig-date">{{ $stockIn->approved_at?->format('d M Y') }}</div>
                        @else
                            <div class="sig-name">........................</div>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <p>Terima kasih telah mempercayakan kebutuhan produksi kepada kami.<br>ModifikasiOri Produksi &mdash; {{ now()->year }}</p>
        </div>
    </div>
</body>
</html>
