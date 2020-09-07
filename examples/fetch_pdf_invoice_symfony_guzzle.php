<?php

/*
 * In InvoiceDownloader.php
 */

use GuzzleHttp\Client;
use function GuzzleHttp\Psr7\stream_for;
// Only uncomment for Symfony project
// use Symfony\Component\HttpFoundation\BinaryFileResponse;
// use Symfony\Component\HttpFoundation\File\File;
// use Symfony\Component\HttpFoundation\ResponseHeaderBag;

final class InvoiceDownloader
{
    private $invoiceUrl;
    private $invoiceTemplate;
    private $invoiceTempdir;

    /**
     * InvoiceDownloader constructor.
     * @param string $invoiceUrl        Url to be used in sprintf() (ex: https://pdf.hiboutik.net/pdf/?account=%s)
     * @param string $invoiceTemplate   Invoice template name configured in Hiboutik
     * @param string $invoiceTempdir    Temporary **readable & writable** directory path to store downloaded invoice (ex: /var/cache or %kernel.cache_dir% parameter under symfony)
     */
    public function __construct(string $invoiceUrl, string $invoiceTemplate, string $invoiceTempdir)
    {
        $this->invoiceUrl = $invoiceUrl;
        $this->invoiceTemplate = $invoiceTemplate;
        $this->invoiceTempdir = $invoiceTempdir;
    }

    /**
     * Download the invoice in a temporary folder
     * @param string $account   Your Hiboutik account identifier
     * @param string $token     The Hiboutik application token
     * @param int $hiboutikId   The Hiboutik sale identifier
     * @return string           The invoice local file path
     */
    public function download(string $account, string $token, int $hiboutikId): string
    {
        $client = new Client([
            'base_uri' => sprintf(
                $this->invoiceUrl,
                $account
            ),
            'timeout' => 20
        ]);
        $tempFile = sprintf('%s/invoice-%d.pdf', $this->invoiceTempdir, $hiboutikId);
        $resource = fopen($tempFile, 'wb');
        $stream = stream_for($resource);
        $client->request('POST', '', [
            'save_to' => $stream,
            'form_params' => [
                'template' => $this->invoiceTemplate,
                'token'    => $token,
                'sale_no'  => $hiboutikId,
            ]
        ]);

        return $tempFile;
    }

    // public function downloadForSymfony(string $account, string $token, int $hiboutikId): BinaryFileResponse
    // {
    //     $invoice = new File($this->download($account, $token, $hiboutikId));
    //     $response = new BinaryFileResponse(
    //         $invoice,
    //         200,
    //         [ 'Content-Type' => 'application/pdf' ]
    //     );
    //     $response
    //         // Delete the file from the local folder when the file is downloaded by the user
    //         ->deleteFileAfterSend(true)
    //         ->setContentDisposition(
    //             ResponseHeaderBag::DISPOSITION_ATTACHMENT,
    //             'invoice.pdf'
    //         );
    // 
    //     return $response;
    // }
}


/*
 * In downloadInvoiceScript.php
 */
$downloader = new \InvoiceDownloader('moncompte', 'ticket', '/var/cache');
$invoicePath = $downloader->download('moncompte', 'IqKSpo8B2etlO6xxxxxxxxxxxxxxc4JsiHgb', 243549);
// Now the file is stored in /var/cache/invoice-243549.pdf
$response = file_get_contents($invoicePath);
// Stream file
header("Content-Description: File Transfer");
header("Content-Type: application/octet-stream");
header("Content-Disposition:attachment;filename=\"facture_$sale_no.pdf\"");
header("Expires: 0");
header("Cache-Control: must-revalidate");
header("Pragma: public");
header("Content-Length: '.strlen($response)");
print $response;
