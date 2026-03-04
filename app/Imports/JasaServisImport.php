<?php

namespace App\Imports;

use App\Models\JasaServis;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class JasaServisImport implements ToModel, WithHeadingRow
{
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
        ]);
    }
}
