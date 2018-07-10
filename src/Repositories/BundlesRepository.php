<?php
/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */
namespace Vaimo\ComposerRepositoryBundle\Repositories;

use Vaimo\ComposerRepositoryBundle\Composer\Config as ComposerConfig;

class BundlesRepository
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
     * @var \Vaimo\ComposerRepositoryBundle\Factories\PackageFactory
     */
    private $packageFactory;

    /**
     * @var \Vaimo\ComposerRepositoryBundle\Factories\CacheFactory
     */
    private $cacheFactory;

    /**
     * @var \Vaimo\ComposerRepositoryBundle\Bundle\DefinitionCollector
     */
    private $definitionCollector;

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

        $this->packageFactory = new \Vaimo\ComposerRepositoryBundle\Factories\PackageFactory();
        $this->definitionCollector = new \Vaimo\ComposerRepositoryBundle\Bundle\DefinitionCollector();
        $this->cacheFactory = new \Vaimo\ComposerRepositoryBundle\Factories\CacheFactory();
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getPackages()
    {
        $repository = $this->composer->getRepositoryManager()->getLocalRepository();

        $packages = array_merge(
            array($this->composer->getPackage()),
            $repository->getPackages()
        );

        $bundles = $this->definitionCollector->collectBundleDefinitions($packages);

        $rootDir = dirname($this->composer->getConfig()->getConfigSource()->getName());

        $bundlePackages = array();

        $cache = $this->cacheFactory->create(
            $repository,
            $this->io,
            $this->composer->getConfig()->get(ComposerConfig::CACHE_DIR)
        );

        foreach ($bundles as $bundleName => $bundle) {
            $bundleUrl = $bundle['url'];

            $targetDir = rtrim($cache->getRoot(), DIRECTORY_SEPARATOR)
                . DIRECTORY_SEPARATOR
                . md5($bundleUrl);

            $package = $this->packageFactory->create(
                $bundleName,
                $bundleUrl,
                isset($bundle['target']) ? $rootDir . DIRECTORY_SEPARATOR . $bundle['target'] : $targetDir,
                isset($bundle['reference']) ? $bundle['reference'] : null
            );

            $bundle['name'] = $bundleName;

            $package->setExtra($bundle);

            $bundlePackages[$bundleName] = $package;
        }

        return $bundlePackages;
    }
}
