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
     * @var \Vaimo\ComposerRepositoryBundle\FileSystem\ChecksumCalculator
     */
    private $checksumCalculator;

    /**
     * @var \Vaimo\ComposerRepositoryBundle\Generators\PackageConfigGenerator
     */
    private $packageConfigGenerator;

    public function __construct()
    {
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

            $packageJson = json_encode($packageDefinition, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

            file_put_contents($path, $packageJson);

            $packageName = $packageDefinition['name'];

            $packagePath = dirname($path);

            $definitions[$packageName] = array(
                'owner' => $config['name'],
                'path' => $packagePath,
                'md5' => md5($config['md5'] . ':' . $this->checksumCalculator->calculate($packagePath)),
                'config' => $packageDefinition
            );
        }

        return $definitions;
    }

    private function getPackageDefinitionPaths(\Composer\Package\PackageInterface $package)
    {
        $config = $package->getExtra();
        $targetDir = trim($package->getTargetDir(), chr(32));

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
