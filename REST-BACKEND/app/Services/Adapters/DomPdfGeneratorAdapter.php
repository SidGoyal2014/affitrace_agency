<?php

namespace App\Services\Adapters;

use App\Services\Contracts\PdfGeneratorInterface;
use Barryvdh\DomPDF\Facade\Pdf;

class DomPdfGeneratorAdapter implements PdfGeneratorInterface
{
    public function generate(string $html, string $outputPath): void
    {
        $pdf = Pdf::loadHTML($html);
        $pdf->save($outputPath);
    }
}
