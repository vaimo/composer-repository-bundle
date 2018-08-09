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
     * @throws \Exception
     */
    public function download(\Composer\Package\PackageInterface $package)
    {
        $config = $package->getExtra();

        $source = getcwd() . DIRECTORY_SEPARATOR . $package->getSourceUrl();
        $targetDir = trim($package->getTargetDir(), chr(32));

        if (!$package->getDistType() && !$package->getSourceType()) {
            return;
        }

        try {
            $downloader = $this->downloadManager->getDownloaderForInstalledPackage($package);
        } catch (\InvalidArgumentException $e) {
            if (is_dir($source)) {
                return;
            }

            throw $e;
        }

        try {
            if (file_exists($targetDir)) {
                return;
            }

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
