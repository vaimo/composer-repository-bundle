<?php
/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */
namespace Vaimo\ComposerRepositoryBundle\Managers;

use Symfony\Component\Console\Output\OutputInterface;
use Vaimo\ComposerRepositoryBundle\BootstrapSteps as Steps;
use Vaimo\ComposerRepositoryBundle\Repositories\BundlesRepository;
use Vaimo\ComposerRepositoryBundle\Composer\Utils\OutputUtils;

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

    /**
     * @throws \Exception
     */
    public function bootstrap()
    {
        if (!$bundles = $this->bundlesRepository->getPackages()) {
            return;
        }

        foreach ($this->bootstrapSteps as $step) {
            $step->execute($bundles);

            $this->io->write('');
        }
    }

    public function processPackages(array $packages)
    {
        $repository = $this->composer->getRepositoryManager()->getLocalRepository();
        $installationManager = $this->composer->getInstallationManager();

        foreach ($packages as $name) {
            $package = $repository->findPackage($name, '*');

            if (!$package) {
                continue;
            }

            $options = $package->getTransportOptions();

            if (!isset($options['bundle-root'])) {
                continue;
            }

            $operation = new \Composer\DependencyResolver\Operation\UninstallOperation($package);

            $verbosityLevel = OutputUtils::resetVerbosity($this->io, OutputInterface::VERBOSITY_QUIET);

            try {
                $installationManager->uninstall($repository, $operation);
            } finally {
                OutputUtils::resetVerbosity($this->io, $verbosityLevel);
            }
        }
    }
}