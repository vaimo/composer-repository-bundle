<?php
/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */
namespace Vaimo\ComposerRepositoryBundle\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Vaimo\ComposerRepositoryBundle\Composer\Config as ComposerConfig;

class ListCommand extends \Composer\Command\BaseCommand
{
    protected function configure()
    {
        $this->setName('bundle:list');

        $this->setDescription('List all available packages from bundles');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $composer = $this->getComposer();
        $io = $this->getIO();

        $bundlesRepository = new \Vaimo\ComposerRepositoryBundle\Repositories\BundlesRepository(
            $composer,
            $io
        );

        $packages = $bundlesRepository->getPackages();

        $bundlePackageDefCollector = new \Vaimo\ComposerRepositoryBundle\Bundle\PackagesCollector(
            $composer
        );

        $repository = $composer->getLocker()->getLockedRepository();

        foreach ($packages as $bundleName => $bundle) {
            $io->write(sprintf('<info>%s</info>', $bundleName));

            $packagesDefinitions = $bundlePackageDefCollector->collectBundlePackageDefinitions($bundle);

            foreach ($packagesDefinitions as $packageDefinition) {
                $packageName = $packageDefinition['config']['name'];
                $packageChecksum = $packageDefinition['md5'];

                $io->write(
                    sprintf('- <info>%s</info> (<comment>%s</comment>)', $packageName, $packageChecksum)
                );

                $package = $repository->findPackage($packageName, ComposerConfig::CONSTRAINT_ANY);

                if ($package) {
                    $io->write(
                        sprintf('  ~ Locked as <comment>%s</comment>', $package->getPrettyVersion())
                    );
                }
            }
        }
    }
}
