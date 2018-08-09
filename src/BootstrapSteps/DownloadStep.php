<?php
/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */
namespace Vaimo\ComposerRepositoryBundle\BootstrapSteps;

class DownloadStep implements \Vaimo\ComposerRepositoryBundle\Interfaces\BootstrapStepInterface
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
     * @var \Vaimo\ComposerRepositoryBundle\Console\Logger
     */
    private $output;

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

        $this->output = new \Vaimo\ComposerRepositoryBundle\Console\Logger($this->io);
    }

    public function execute(array $bundles)
    {
        $downloadManager = $this->composer->getDownloadManager();

        $this->output->info('Configuring bundles');

        $downloader = new \Vaimo\ComposerRepositoryBundle\Package\Downloader($downloadManager);

        try {
            array_map(array($downloader, 'download'), $bundles);
        } catch (\Exception $e) {
            $this->io->error(
                sprintf(PHP_EOL . '<error>%s</error>', $e->getMessage())
            );
        }
    }
}
