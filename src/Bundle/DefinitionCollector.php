<?php
/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */
namespace Vaimo\ComposerRepositoryBundle\Bundle;

class DefinitionCollector
{
    public function collectBundleDefinitions(array $packages)
    {
        $result = array();

        foreach ($packages as $package) {
            $extra = $package->getExtra();

            if (!isset($extra['bundles']) || !is_array($extra['bundles'])) {
                continue;
            }

            $template = isset($extra['bundles-package']) ? $extra['bundles-package'] : array();

            $bundles = array_map(function (array $item) use ($template) {
                return array_replace(array(
                    'package' => $template
                ), $item);
            }, $this->normalize($extra['bundles']));

            $result = array_replace($result, $bundles);
        }

        return $result;
    }

    private function normalize(array $bundles)
    {
        return array_map(function ($config) {
            return !is_array($config) ? array('source' => $config) : $config;
        }, $bundles);
    }
}
