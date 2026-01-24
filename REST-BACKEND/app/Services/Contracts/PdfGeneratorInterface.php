<?php

namespace App\Services\Contracts;

interface PdfGeneratorInterface
{
    /**
     * Generate a PDF from HTML content and save it to the specified path.
     *
     * @param string $html The HTML content to convert.
     * @param string $outputPath The absolute path where the PDF should be saved.
     * @return void
     */
    public function generate(string $html, string $outputPath): void;
}
