<?php
/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */
namespace Vaimo\ComposerRepositoryBundle\BootstrapSteps;

class DownloadStep
{
    /**
     * @var \Composer\Composer
     */
    private $composer;

    /**
     * @var \Composer\IO\IOInterface
     */
    private $io;

    /**
     * @param \Composer\Composer $composer
     * @param \Composer\IO\IOInterface $io
     */
    public function __construct(
        \Composer\Composer $composer,
        \Composer\IO\IOInterface $io
    ) {
        $this->composer = $composer;
        $this->io = $io;
    }

    public function execute(array $bundles, $isVerbose)
    {
        $downloadManager = $this->composer->getDownloadManager();

        $this->io->write('<info>Configuring bundles</info>');

        $rootDir = dirname($this->composer->getConfig()->getConfigSource()->getName());

        $downloader = new \Vaimo\ComposerRepositoryBundle\Package\Downloader($downloadManager, $rootDir);

        try {
            array_map(array($downloader, 'download'), $bundles);
        } catch (\Exception $e) {
            $this->io->error(sprintf(PHP_EOL . '<error>%s</error>', $e->getMessage()));
        }
    }
}
