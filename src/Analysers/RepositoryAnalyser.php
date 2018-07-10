<?php
/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */
namespace Vaimo\ComposerRepositoryBundle\Analysers;

use Composer\Repository\RepositoryInterface;
use Vaimo\ComposerRepositoryBundle\Composer\Config as ComposerConfig;

class RepositoryAnalyser
{
    /**
     * @param RepositoryInterface $repository
     * @param string $namespace
     * @return string
     * @throws \Exception
     */
    public function getPackageForNamespace(RepositoryInterface $repository, $namespace)
    {
        foreach ($repository->getCanonicalPackages() as $package) {
            if ($package->getType() !== ComposerConfig::COMPOSER_PLUGIN_TYPE) {
                continue;
            }

            $autoload = $package->getAutoload();

            if (!isset($autoload[ComposerConfig::PSR4_CONFIG])) {
                continue;
            }

            $matches = array_filter(
                array_keys($autoload[ComposerConfig::PSR4_CONFIG]),
                function ($item) use ($namespace) {
                    return strpos($namespace, rtrim($item, '\\')) === 0;
                }
            );

            if (!$matches) {
                continue;
            }

            return $package;
        }

        throw new \Exception('Failed to detect the plugin package');
    }
}
