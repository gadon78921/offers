<?php

declare(strict_types=1);

namespace App\Esb\In\RetailSupplierPriorities\Infrastructure;

use App\Esb\In\RetailSupplierPriorities\Messages\SupplierPrioritiesData;
use FtpClient\FtpClient;
use Symfony\Component\Filesystem\Filesystem;

final class SupplierPrioritiesDataFtpAccessObject
{
    public function __construct(
        private readonly FtpClient $ftp,
        private readonly Filesystem $filesystem,
        private readonly string $ftpLogin,
        private readonly string $ftpPassword,
        private readonly string $pathToLocalFile,
    ) {}

    public function download(SupplierPrioritiesData $supplierPricesData): string
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
