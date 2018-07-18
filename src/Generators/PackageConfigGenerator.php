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

        $packageDefinitionInitial = array();

        if (file_exists($packageDefinitionPath)) {
            $packageJson = file_get_contents($packageDefinitionPath);
            $packageDefinition = json_decode($packageJson, true);

            $packageDefinitionInitial = $packageDefinition;
        } else {
            $packageDefinition = array_replace(array('name' => $name), $defaults);
        }

        if (!isset($packageDefinition[ComposerConfig::AUTOLOAD][ComposerConfig::PSR4_CONFIG])) {
            $files = $this->filesCollector->collectFiles($packagePath, 'php');

            if ($namespace = $this->namespaceResolver->resolveSharedNamespaceForFiles($files)) {
                $packageDefinition = array_replace_recursive(array(
                    ComposerConfig::AUTOLOAD => array(
                        ComposerConfig::PSR4_CONFIG => array(
                            $namespace => ''
                        )
                    )
                ), $packageDefinition);
            }
        }

        if ($this->arrayDiffAssocRecursive($packageDefinition, $packageDefinitionInitial)) {
            $packageJson = json_encode($packageDefinition, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

            file_put_contents($packageDefinitionPath, $packageJson);
        }

        return $packageDefinition;
    }

    private function arrayDiffAssocRecursive($array1, $array2)
    {
        foreach ($array1 as $key => $value) {
            if (is_array($value)) {
                if (!isset($array2[$key])) {
                    $difference[$key] = $value;
                } elseif (!is_array($array2[$key])) {
                    $difference[$key] = $value;
                } else {
                    $new_diff = $this->arrayDiffAssocRecursive($value, $array2[$key]);

                    if ($new_diff != false) {
                        $difference[$key] = $new_diff;
                    }
                }
            } elseif (!isset($array2[$key]) || (is_object($array2[$key]) xor is_object($value)) || $array2[$key] != $value) {
                $difference[$key] = $value;
            }
        }

        return !isset($difference) ? 0 : $difference;
    }
}
