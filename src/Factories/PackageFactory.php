<?php
/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */
namespace Vaimo\ComposerRepositoryBundle\Factories;

class PackageFactory
{
    public function create($name, $remoteFile, $targetDir, $reference = null)
    {
        $versionParser = new \Composer\Package\Version\VersionParser();

        $version = '1.0.0';

        $package = new \Composer\Package\Package(
            'bundle-' . $name,
            $versionParser->normalize($version),
            $version
        );

        $extension = pathinfo($remoteFile, PATHINFO_EXTENSION);

        $remoteFile = str_replace('{{reference}}', $reference, $remoteFile);

        switch ($extension) {
            case 'zip':
                $package->setDistType('zip');
                $package->setInstallationSource('dist');
                $package->setDistUrl($remoteFile);
                break;
            case 'gz':
            case 'tar':
                $package->setDistType('tar');
                $package->setInstallationSource('dist');
                $package->setDistUrl($remoteFile);
                break;
            default:
                $package->setSourceType($extension);
                $package->setInstallationSource('source');
                $package->setSourceUrl($remoteFile);

        }

        if ($reference) {
            if ($package->getDistType()) {
                $package->setDistReference($reference);
            } else {
                $package->setSourceReference($reference);
            }
        }

        $package->setTargetDir(chr(32) . $targetDir);

        return $package;
    }
}
