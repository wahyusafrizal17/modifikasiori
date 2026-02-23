<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\ServiceOrder;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['serviceOrder.pelanggan', 'serviceOrder.kendaraan']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn ($q) => $q->where('kode_invoice', 'like', "%{$s}%")
                ->orWhereHas('serviceOrder.pelanggan', fn ($q2) => $q2->where('nama', 'like', "%{$s}%")));
        }

        $invoices = $query->latest()->paginate(20)->withQueryString();

        return view('admin.invoices.index', compact('invoices'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_order_id' => 'required|exists:service_orders,id',
            'diskon' => 'nullable|numeric|min:0',
            'metode_pembayaran' => 'required|in:cash,transfer',
            'catatan' => 'nullable|string',
        ]);

        $order = ServiceOrder::with(['jasaServis', 'products'])->findOrFail($request->service_order_id);

        if ($order->invoice) {
            return redirect()->route('admin.invoices.show', $order->invoice)
                ->with('error', 'Invoice sudah ada untuk order ini.');
        }

        $totalJasa = $order->total_jasa;
        $totalSparepart = $order->total_sparepart;
        $diskon = (int) ($request->diskon ?? 0);
        $grandTotal = $totalJasa + $totalSparepart - $diskon;

        $invoice = Invoice::create([
            'kode_invoice' => Invoice::generateKode(),
            'service_order_id' => $order->id,
            'total_jasa' => $totalJasa,
            'total_sparepart' => $totalSparepart,
            'diskon' => $diskon,
            'grand_total' => max(0, $grandTotal),
            'metode_pembayaran' => $request->metode_pembayaran,
            'tanggal' => now()->toDateString(),
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('admin.invoices.show', $invoice)
            ->with('success', 'Invoice berhasil dibuat.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['serviceOrder.pelanggan.kota', 'serviceOrder.kendaraan', 'serviceOrder.mekanik', 'serviceOrder.jasaServis', 'serviceOrder.products']);

        return view('admin.invoices.show', compact('invoice'));
    }

    public function print(Invoice $invoice)
    {
        $invoice->load(['serviceOrder.pelanggan.kota', 'serviceOrder.kendaraan', 'serviceOrder.mekanik', 'serviceOrder.jasaServis', 'serviceOrder.products']);

        return view('admin.invoices.print', compact('invoice'));
    }
}
