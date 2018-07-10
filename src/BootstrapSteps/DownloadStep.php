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

    public function execute(array $bundles)
    {
        $this->io->write('<info>Downloading bundles</info>');

        $downloader = new \Vaimo\ComposerRepositoryBundle\Package\Downloader(
            $this->composer->getDownloadManager()
        );

        try {
            array_walk($bundles, array($downloader, 'download'));
        } catch (\Exception $e) {
            $this->io->error(sprintf(PHP_EOL . '<error>%s</error>', $e->getMessage()));
        }
    }
}
