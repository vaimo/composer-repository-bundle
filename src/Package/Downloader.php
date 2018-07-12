<?php
/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */
namespace Vaimo\ComposerRepositoryBundle\Package;

class Downloader
{
    /**
     * @var \Composer\Downloader\DownloadManager
     */
    private $downloadManager;

    /**
     * @param \Composer\Downloader\DownloadManager $downloadManager
     */
    public function __construct(
        \Composer\Downloader\DownloadManager $downloadManager
    ) {
        $this->downloadManager = $downloadManager;
    }

    /**
     * @param \Composer\Package\PackageInterface $package
     * @param bool $reDownload
     * @throws \Exception
     * @return bool
     */
    public function download(\Composer\Package\PackageInterface $package, $reDownload = false)
    {
        $config = $package->getExtra();

        $downloader = $this->downloadManager->getDownloaderForInstalledPackage($package);
        $targetDir = trim($package->getTargetDir(), chr(32));

        try {
            if (!$reDownload && file_exists($targetDir)) {
                return false;
            }

            $downloader->download($package, $targetDir);

            return true;
        } catch (\Composer\Downloader\TransportException $e) {
            $message = sprintf(
                'Transport failure %s while downloading from %s: %s',
                $e->getStatusCode(),
                $config['url'],
                $e->getMessage()
            );

            throw new \Exception($message, 0, $e);
        } catch (\Exception $e) {
            $message = sprintf(
                'Unexpected error while downloading from %s: %s',
                $config['url'],
                $e->getMessage()
            );

            throw new \Exception($message, 0, $e);
        }
    }
}
