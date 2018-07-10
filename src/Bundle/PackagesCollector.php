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
     * @var \Vaimo\ComposerRepositoryBundle\Analysers\PhpFileAnalyser
     */
    private $phpFileAnalyser;

    /**
     * @var \Vaimo\ComposerRepositoryBundle\FileSystem\FilesCollector
     */
    private $filesCollector;

    public function __construct()
    {
        $this->phpFileAnalyser = new \Vaimo\ComposerRepositoryBundle\Analysers\PhpFileAnalyser();
        $this->filesCollector = new \Vaimo\ComposerRepositoryBundle\FileSystem\FilesCollector();
    }

    public function collectBundlePackageDefinitions($package)
    {
        $config = $package->getExtra();

        $definitions = array();

        $paths = $this->getPackageDefinitionPaths($package);

        foreach ($paths as $name => $path) {
            $packageDefinition = $this->getPackageConfig(
                $name,
                $path,
                array_replace(array(
                    'description' => sprintf('Generated package from bundle: %s', $config['name'])
                ), $config['package'])
            );

            if (isset($config['package']['version']) && !isset($packageDefinition['version'])) {
                $packageDefinition['version'] = $config['package']['version'];
            }

            $packageJson = json_encode($packageDefinition, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

            file_put_contents($path, $packageJson);

            $packageName = $packageDefinition['name'];

            $definitions[$packageName] = array(
                'owner' => $config['name'],
                'path' => dirname($path),
                'config' => $packageDefinition
            );
        }

        return $definitions;
    }

    private function getPackageDefinitionPaths($package)
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

    private function getPackageConfig($name, $packageDefinitionPath, array $defaults)
    {
        $packagePath = dirname($packageDefinitionPath);

        if (file_exists($packageDefinitionPath)) {
            $packageJson = file_get_contents($packageDefinitionPath);
            $packageDefinition = json_decode($packageJson, true);
        } else {
            $packageDefinition = array_replace(array('name' => $name), $defaults);
        }

        $files = $this->filesCollector->collectFilesWithExtension($packagePath, 'php');

        $namespace = $this->resolveSharedNamespace($files);

        if ($namespace) {
            $packageDefinition = array_replace_recursive(array(
                ComposerConfig::AUTOLOAD => array(
                    ComposerConfig::PSR4_CONFIG => array(
                        $namespace => ''
                    )
                )
            ), $packageDefinition);
        }

        return $packageDefinition;
    }

    private function resolveSharedNamespace(array $files)
    {
        $classes = array();

        foreach ($files as $file) {
            $classes = array_merge(
                $classes,
                array_filter($this->phpFileAnalyser->collectPhpClasses($file))
            );
        }

        $classParts = array_filter(explode('\\', reset($classes)));

        $namespace = '';

        foreach ($classParts as $item) {
            $lookup = $namespace . $item;

            $result = preg_grep('#^' . preg_quote($lookup, '#') . '#', $classes);

            if ($result !== $classes) {
                break;
            }

            $namespace = $lookup . '\\';
        }

        return $namespace;
    }
}
