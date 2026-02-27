<?php

namespace App\Console\Commands;

use App\Exports\JasaServisTemplateExport;
use App\Exports\ProductsTemplateExport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class GenerateTemplateFiles extends Command
{
    protected $signature = 'templates:generate';
    protected $description = 'Generate Excel template files for Products and Jasa Servis';

    public function handle(): int
    {
        $this->info('Generating template files...');

        Excel::store(new ProductsTemplateExport, 'templates/template-products.xlsx');
        $this->line('  ✓ template-products.xlsx');

        Excel::store(new JasaServisTemplateExport, 'templates/template-jasa-servis.xlsx');
        $this->line('  ✓ template-jasa-servis.xlsx');

        $publicDir = public_path('templates');
        if (!is_dir($publicDir)) {
            File::makeDirectory($publicDir, 0755, true);
        }
        foreach (['templates/template-products.xlsx', 'templates/template-jasa-servis.xlsx'] as $path) {
            if (Storage::exists($path)) {
                File::copy(Storage::path($path), $publicDir . '/' . basename($path));
            }
        }

        $this->newLine();
        $this->info('Template files saved to:');
        $this->line('  - storage/app/ (via Excel::store)');
        $this->line('  - public/templates/ (template-products.xlsx, template-jasa-servis.xlsx)');

        return self::SUCCESS;
    }
}
