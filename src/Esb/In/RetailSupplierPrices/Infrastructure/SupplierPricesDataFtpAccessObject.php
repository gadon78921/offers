<?php

declare(strict_types=1);

namespace App\Esb\In\RetailSupplierPrices\Infrastructure;

use App\Esb\In\RetailSupplierPrices\Messages\SupplierPricesData;
use FtpClient\FtpClient;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @codeCoverageIgnore
 */
final class SupplierPricesDataFtpAccessObject
{
    public function __construct(
        private readonly FtpClient $ftp,
        private readonly Filesystem $filesystem,
        private readonly string $ftpLogin,
        private readonly string $ftpPassword,
        private readonly string $pathToLocalFile,
    ) {}

    public function download(SupplierPricesData $supplierPricesData): string
    {
        $this->connectToFtp($supplierPricesData->host, $supplierPricesData->port);

        $localFile = $this->pathToLocalFile . $supplierPricesData->file;
        $this->filesystem->dumpFile($localFile, $this->ftp->getContent($supplierPricesData->fullFileName()));

        return $localFile;
    }

    private function connectToFtp(string $host, int $port = 21): void
    {
        $this->ftp->connect($host, false, $port);
        $this->ftp->login($this->ftpLogin, $this->ftpPassword);
        $this->ftp->pasv(true);
    }
}
