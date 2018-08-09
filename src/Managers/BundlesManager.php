<?php
/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */
namespace Vaimo\ComposerRepositoryBundle\Managers;

use Vaimo\ComposerRepositoryBundle\BootstrapSteps as Steps;
use Vaimo\ComposerRepositoryBundle\Repositories\BundlesRepository;

class BundlesManager
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
     * @var \Vaimo\ComposerRepositoryBundle\Repositories\BundlesRepository
     */
    private $bundlesRepository;

    /**
     * @var \Vaimo\ComposerRepositoryBundle\Interfaces\BootstrapStepInterface[]
     */
    private $bootstrapSteps;

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

        $this->bootstrapSteps = array(
            new Steps\DownloadStep($this->composer, $this->io),
            new Steps\RegisterStep($this->composer, $this->io)
        );

        $this->bundlesRepository = new BundlesRepository($this->composer, $this->io);
    }

    public function bootstrap()
    {
        if (!$bundles = $this->bundlesRepository->getPackages()) {
            return;
        }

        foreach ($this->bootstrapSteps as $step) {
            $step->execute($bundles);

            if ($this->io->isVerbose()) {
                $this->io->write('');
            }
        }
    }
}