<?php
/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */
namespace Vaimo\ComposerRepositoryBundle\BootstrapSteps;

class RegisterStep implements \Vaimo\ComposerRepositoryBundle\Interfaces\BootstrapStepInterface
{
    /**
     * @var \Composer\Composer
     */
    private $composer;
    
    /**
     * @var \Vaimo\ComposerRepositoryBundle\Bundle\PackagesCollector
     */
    private $bundlePackageDefCollector;

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

        $this->bundlePackageDefCollector = new \Vaimo\ComposerRepositoryBundle\Bundle\PackagesCollector(
            $this->composer
        );

        $this->output = new \Vaimo\ComposerRepositoryBundle\Console\Logger($io);
    }

    public function execute(array $bundles)
    {
        $this->output->info('Configuring packages');

        $bundlePackageQueue = array();
        $rootDir = getcwd();

        foreach ($bundles as $bundleName => $bundle) {
            $packages = $this->bundlePackageDefCollector->collectBundlePackageDefinitions($bundle);

            $config = $bundle->getExtra();

            $isLocalPackage = isset($config['local']) || $config['local'];

            $installMode = 'symlink';
            
            if (isset($config['mode'])) {
                $installMode = $config['mode'];
            }
            
            $updates = array_fill_keys(
                array_keys($packages), 
                array(
                    'symlink' => ($isLocalPackage || isset($config['target'])) && $installMode !== 'mirror'
                )
            );

            $bundlePackageQueue = array_replace(
                array_replace_recursive($packages, $updates),
                $bundlePackageQueue
            );
        }

        $this->output->info('Registering package endpoints');

        $rootPackage = $this->composer->getPackage();
        $repositoryManager = $this->composer->getRepositoryManager();

        $rootRequires = array_replace(
            $rootPackage->getRequires(),
            $rootPackage->getDevRequires()
        );

        $names = array_keys($bundlePackageQueue);

        $repositories = $repositoryManager->getRepositories();

        $repositoryMap = array_combine(
            array_map(function ($item) {
                $config = $item->getRepoConfig();
                return $config['url'] ?? '';
            }, $repositories),
            $repositories
        );

        foreach ($bundlePackageQueue as $name => $config) {
            $owner = $config['owner'];
            $bundle = $bundles[$owner];

            $targetDir = trim($bundle->getTargetDir(), chr(32));

            $this->output->raw('  - Including <info>%s</info> (<comment>%s</comment>)', $name, $config['md5']);
            $this->output->raw('    ~ Bundle: <comment>%s</comment>', $config['owner']);

            if (isset($repositoryMap[$config['path']])) {
                continue;
            }

            $repository = $repositoryManager->createRepository('path', array(
                'url' => strpos($config['path'], $rootDir) === 0 
                    ? ltrim(substr($config['path'], strlen($rootDir)), DIRECTORY_SEPARATOR) 
                    : $config['path'],
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

                    $version = ltrim((string)$constraint, ' =<>~^');
                    $prettyVersion = $constraint->getPrettyString();

                    $package->replaceVersion($version, $prettyVersion);
                } else {
                    $bundleConstraint = 'dev-' . $owner;

                    $package->replaceVersion($bundleConstraint, $bundleConstraint);
                }
            }

            if ($name !== end($names)) {
                $this->output->nl();
            }
        }
    }
}
