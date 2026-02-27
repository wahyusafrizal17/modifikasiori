<?php

namespace App\Imports;

use App\Models\JasaServis;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class JasaServisImport implements ToModel, WithHeadingRow
{
    protected int $warehouseId;

    public function __construct()
    {
        $this->warehouseId = auth()->user()->warehouse_id;
    }

    public function model(array $row): ?JasaServis
    {
        $nama = trim($row['nama_jasa_servis'] ?? $row['nama'] ?? '');
        $biaya = (float) ($row['biaya'] ?? $row['biaya_rp'] ?? 0);

        if (empty($nama)) {
            return null;
        }

        return new JasaServis([
            'nama' => $nama,
            'biaya' => $biaya,
            'warehouse_id' => $this->warehouseId,
        ]);
    }
}
