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

        $this->bundlePackageDefCollector = new \Vaimo\ComposerRepositoryBundle\Bundle\PackagesCollector(
            $this->composer
        );
    }

    public function execute(array $bundles, $isVerbose)
    {
        $output = new \Vaimo\ComposerRepositoryBundle\Console\Output($this->io, $isVerbose);

        $output->info('Configuring packages');

        $bundlePackageQueue = array();

        foreach ($bundles as $bundleName => $bundle) {
            $packages = $this->bundlePackageDefCollector->collectBundlePackageDefinitions($bundle);

            $config = $bundle->getExtra();

            $isLocalPackage = isset($config['local']) || $config['local'];

            $updates = array_fill_keys(array_keys($packages), array(
                'symlink' => $isLocalPackage || isset($config['target'])
            ));

            $bundlePackageQueue = array_replace(
                array_replace_recursive($packages, $updates),
                $bundlePackageQueue
            );
        }

        $output->info('Registering package endpoints');

        $repositoryManager = $this->composer->getRepositoryManager();

        $rootPackage = $this->composer->getPackage();

        $rootRequires = array_replace(
            $rootPackage->getRequires(),
            $rootPackage->getDevRequires()
        );

        $names = array_keys($bundlePackageQueue);

        foreach ($bundlePackageQueue as $name => $config) {
            $owner = $config['owner'];
            $bundle = $bundles[$owner];

            $targetDir = trim($bundle->getTargetDir(), chr(32));

            $output->raw('  - Including <info>%s</info> (<comment>%s</comment>)', $name, $config['md5']);
            $output->raw('    ~ Bundle: <comment>%s</comment>', $config['owner']);

            $repository = $repositoryManager->createRepository('path', array(
                'url' => $config['path'],
                'options' => array(
                    'symlink' => $config['symlink'],
                    'bundle-root' => $targetDir,
                    'md5' => $config['md5']
                )
            ));

            $repositoryManager->addRepository($repository);

            /** @var \Composer\Package\Package[] $packages */
            $packages = $repository->getPackages();

            foreach ($packages as $package) {
                $packageName = $package->getName();

                if (isset($rootRequires[$packageName])) {
                    /** @var \Composer\Package\Link $rootRequire */
                    $rootRequire = $rootRequires[$packageName];

                    /** @var \Composer\Semver\Constraint\Constraint $constraint */
                    $constraint = $rootRequire->getConstraint();

                    $version = ltrim((string)$constraint, ' =<>');
                    $prettyVersion = $constraint->getPrettyString();

                    $package->replaceVersion($version, $prettyVersion);
                } else {
                    $package->replaceVersion('9999999-dev', 'dev-default');
                }
            }

            if ($name !== end($names)) {
                $output->nl();
            }
        }
    }
}
