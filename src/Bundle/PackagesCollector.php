<?php
/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */
namespace Vaimo\ComposerRepositoryBundle\Bundle;

use Vaimo\ComposerRepositoryBundle\Composer\Config as ComposerConfig;

class PackagesCollector
{
    /**
     * @var \Composer\Composer
     */
    private $composer;

    /**
     * @var \Vaimo\ComposerRepositoryBundle\FileSystem\ChecksumCalculator
     */
    private $checksumCalculator;

    /**
     * @var \Vaimo\ComposerRepositoryBundle\Generators\PackageConfigGenerator
     */
    private $packageConfigGenerator;

    public function __construct(
        \Composer\Composer $composer
    ) {
        $this->composer = $composer;
        $this->checksumCalculator = new \Vaimo\ComposerRepositoryBundle\FileSystem\ChecksumCalculator();
        $this->packageConfigGenerator = new \Vaimo\ComposerRepositoryBundle\Generators\PackageConfigGenerator();
    }

    public function collectBundlePackageDefinitions(\Composer\Package\PackageInterface $package)
    {
        $config = $package->getExtra();

        $definitions = array();

        $paths = $this->getPackageDefinitionPaths($package);

        foreach ($paths as $name => $path) {
            $packageDefinition = $this->packageConfigGenerator->generate(
                $name,
                $path,
                array_replace(array(
                    'description' => sprintf('Generated package from bundle: %s', $config['name'])
                ), $config['package'])
            );

            $packageName = $packageDefinition['name'];
            $packagePath = dirname($path);

            $isLocalPackage = isset($config['local']) || $config['local'];

            $installMode = 'symlink';

            if (isset($config['mode'])) {
                $installMode = $config['mode'];
            }

            $isLinkedPackage = ($isLocalPackage || isset($config['target'])) && $installMode !== 'mirror';
            
            $moduleChecksum = $this->checksumCalculator->calculate($packagePath, !$isLinkedPackage);
            
            $definitions[$packageName] = array(
                'owner' => $config['name'],
                'path' => $packagePath,
                'md5' => md5($config['md5'] . ':' . $moduleChecksum),
                'config' => $packageDefinition
            );
        }

        return $definitions;
    }

    private function getPackageDefinitionPaths(\Composer\Package\PackageInterface $package)
    {
        $config = $package->getExtra();

        $targetDir = trim($package->getTargetDir(), chr(32));

        if ($config['local']) {
            $targetDir = rtrim(getcwd() . DIRECTORY_SEPARATOR . $targetDir, DIRECTORY_SEPARATOR);
        }

        $paths = array();

        foreach ($config['paths'] as $basePath) {
            $vendorName = strtok($config['name'], DIRECTORY_SEPARATOR);

            $bundleRoot = $targetDir . DIRECTORY_SEPARATOR . $basePath;

            $directoryIterator = new \DirectoryIterator($bundleRoot);

            foreach ($directoryIterator as $fileInfo) {
                if (!$fileInfo->isDir() || $fileInfo->isDot()) {
                    continue;
                }

                $packageName = $fileInfo->getFilename();
                $packagePath = $fileInfo->getPathname();

                $paths[$vendorName . DIRECTORY_SEPARATOR . $packageName] = $packagePath
                    . DIRECTORY_SEPARATOR
                    . ComposerConfig::PACKAGE_FILE;
            }
        }

        return $paths;
    }
}
