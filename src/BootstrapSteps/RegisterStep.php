<?php
/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */
namespace Vaimo\ComposerRepositoryBundle\BootstrapSteps;

class RegisterStep
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
     * @var \Vaimo\ComposerRepositoryBundle\Bundle\PackagesCollector
     */
    private $bundlePackageDefCollector;

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

        $this->bundlePackageDefCollector = new \Vaimo\ComposerRepositoryBundle\Bundle\PackagesCollector();
    }

    public function execute(array $bundles)
    {
        $this->io->write('<info>Configuring bundle packages</info>');

        $bundlePackageQueue = array();

        foreach ($bundles as $bundleName => $bundle) {
            $packages = $this->bundlePackageDefCollector->collectBundlePackageDefinitions($bundle);

            $config = $bundle->getExtra();

            $updates = array_fill_keys(array_keys($packages), array(
                'symlink' => isset($config['target'])
            ));

            $bundlePackageQueue = array_replace(
                array_replace_recursive($packages, $updates),
                $bundlePackageQueue
            );
        }

        $this->io->write('<info>Registering bundle package endpoints</info>');

        $repositoryManager = $this->composer->getRepositoryManager();

        foreach ($bundlePackageQueue as $name => $config) {
            $owner = $config['owner'];

            $bundle = $bundles[$owner];

            $targetDir = trim($bundle->getTargetDir(), chr(32));

            $repository = $repositoryManager->createRepository('path', array(
                'url' => $config['path'],
                'options' => array(
                    'symlink' => $config['symlink'],
                    'bundle-root' => $targetDir,
                    'bundle-md5' => $config['md5']
                )
            ));

            $this->io->write(
                sprintf('  - Including <info>%s</info> (<comment>%s</comment>)', $name, $config['owner'])
            );

            $repositoryManager->addRepository($repository);
        }
    }
}
