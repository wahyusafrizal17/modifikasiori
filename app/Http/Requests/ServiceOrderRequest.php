<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pelanggan_id' => ['required', 'exists:pelanggans,id'],
            'kendaraan_id' => ['required', 'exists:kendaraans,id'],
            'mekanik_id' => ['nullable', 'exists:mekaniks,id'],
            'keluhan' => ['nullable', 'string'],
            'tanggal_masuk' => ['required', 'date'],
            'tanggal_selesai' => ['nullable', 'date'],
            'next_service_date' => ['nullable', 'date'],
            'jasa_items' => ['nullable', 'array'],
            'jasa_items.*.jasa_servis_id' => ['required', 'exists:jasa_servis,id'],
            'jasa_items.*.biaya' => ['required', 'numeric', 'min:0'],
            'product_items' => ['nullable', 'array'],
            'product_items.*.product_id' => ['required', 'exists:products,id'],
            'product_items.*.qty' => ['required', 'integer', 'min:1'],
            'product_items.*.harga' => ['required', 'numeric', 'min:0'],
        ];
    }
}
