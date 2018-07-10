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
     * @param $package
     * @throws \Exception
     */
    public function download($package)
    {
        $downloader = $this->downloadManager->getDownloaderForInstalledPackage($package);

        $config = $package->getExtra();

        $targetDir = trim($package->getTargetDir(), chr(32));

        try {
            $downloader->download($package, $targetDir);
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
