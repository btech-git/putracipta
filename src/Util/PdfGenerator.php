<?php

namespace App\Util;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfGenerator
{
    private string $chrootDir;
    private string $resourceFilename;

    public function __construct(string $chrootDir, string $resourceFilename)
    {
        $this->chrootDir = $chrootDir;
        $this->resourceFilename = $resourceFilename;
    }

    public function generate(string $htmlView, string $streamName): void
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($pdfOptions);
        $dompdf->getOptions()->setChroot($this->chrootDir);

        $html = preg_replace('/<link(.+)href=".+">/', '<link\1href="' . $this->chrootDir . $this->resourceFilename . '">', $htmlView);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dompdf->stream($streamName, [
            'Attachment' => false,
        ]);
    }
}
