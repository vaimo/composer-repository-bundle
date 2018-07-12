<?php
/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */
namespace Vaimo\ComposerRepositoryBundle\Generators;

use Vaimo\ComposerRepositoryBundle\Composer\Config as ComposerConfig;

class PackageConfigGenerator
{
    /**
     * @var \Vaimo\ComposerRepositoryBundle\FileSystem\FilesCollector
     */
    private $filesCollector;

    /**
     * @var \Vaimo\ComposerRepositoryBundle\Resolvers\NamespaceResolver
     */
    private $namespaceResolver;

    public function __construct()
    {
        $this->filesCollector = new \Vaimo\ComposerRepositoryBundle\FileSystem\FilesCollector();
        $this->namespaceResolver = new \Vaimo\ComposerRepositoryBundle\Resolvers\NamespaceResolver();
    }

    public function generate($name, $packageDefinitionPath, array $defaults)
    {
        $packagePath = dirname($packageDefinitionPath);

        if (file_exists($packageDefinitionPath)) {
            $packageJson = file_get_contents($packageDefinitionPath);
            $packageDefinition = json_decode($packageJson, true);
        } else {
            $packageDefinition = array_replace(array('name' => $name), $defaults);
        }

        $files = $this->filesCollector->collectFiles($packagePath, 'php');

        $namespace = $this->namespaceResolver->resolveSharedNamespaceForFiles($files);

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
}
