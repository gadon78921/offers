<?php

declare(strict_types=1);

namespace App\Esb\In\RetailOrderCityPrices\Infrastructure;

use App\Esb\In\RetailOrderCityPrices\Messages\OrderPricesData;
use FtpClient\FtpClient;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @codeCoverageIgnore
 */
final class OrderPricesFtpAccessObject
{
    public function __construct(
        private readonly FtpClient $ftp,
        private readonly Filesystem $filesystem,
        private readonly string $ftpLogin,
        private readonly string $ftpPassword,
        private readonly string $pathToLocalFile,
    ) {}

    public function download(OrderPricesData $orderPricesData): string
    {
        $this->connectToFtp($orderPricesData->host, $orderPricesData->port);

        $localFile = $this->pathToLocalFile . $orderPricesData->file;
        $this->filesystem->dumpFile($localFile, $this->ftp->getContent($orderPricesData->fullFileName()));

        return $localFile;
    }

    private function connectToFtp(string $host, int $port = 21): void
    {
        $this->ftp->connect($host, false, $port);
        $this->ftp->login($this->ftpLogin, $this->ftpPassword);
        $this->ftp->pasv(true);
    }
}
