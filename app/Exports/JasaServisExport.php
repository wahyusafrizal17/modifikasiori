<?php

namespace App\Exports;

use App\Models\JasaServis;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class JasaServisExport implements FromQuery, WithHeadings, WithMapping
{
    public function query()
    {
        return JasaServis::forUser()->orderBy('nama');
    }

    public function headings(): array
    {
        return [
            'Nama Jasa Servis',
            'Biaya',
        ];
    }

    public function map($item): array
    {
        return [
            $item->nama,
            $item->biaya,
        ];
    }
}
