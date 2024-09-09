<?php

declare(strict_types=1);

namespace App\Esb\In\RetailSupplierPriorities\Messages;

use App\Esb\In\RetailSupplierPriorities\Commands\ImportSupplierPriorities;
use App\Esb\In\RetailSupplierPriorities\Infrastructure\SupplierPrioritiesDataFtpAccessObject;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @codeCoverageIgnore
 */
final class RetailSupplierPrioritiesHandler
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
        private readonly SupplierPrioritiesDataFtpAccessObject $dao,
    ) {}

    public function handle(RetailSupplierPrioritiesMessage $message): void
    {
        $supplierPrice = $this->dao->download($message->supplierPrioritiesData);
        $supplierPrice = $this->extractGzFile($supplierPrice);

        if (filesize($supplierPrice) > 1) {
            $this->messageBus->dispatch(new ImportSupplierPriorities($message->regionCode, $supplierPrice));
        }
    }

    private function extractGzFile(string $gzFile): string
    {
        $extractedFileName = str_replace('.gz', '', $gzFile);
        $tmpFile           = gzopen($gzFile, 'rb');
        $csvFile           = fopen($extractedFileName, 'wb');

        while (!gzeof($tmpFile)) {
            fwrite($csvFile, gzread($tmpFile, 4096));
        }

        fclose($csvFile);
        gzclose($tmpFile);

        return $extractedFileName;
    }
}
