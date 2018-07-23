<?php
/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */
namespace Vaimo\ComposerRepositoryBundle\Bundle;

use Vaimo\ComposerRepositoryBundle\Composer\Config as ComposerConfig;

class PathInfo
{
    /**
     * @var \Composer\Repository\RepositoryManager
     */
    private $repositoryManager;

    /**
     * @var \Composer\Installer\InstallationManager
     */
    private $installationManager;

    /**
     * @var string
     */
    private $rootDir;

    /**
     * @param \Composer\Repository\RepositoryManager $repositoryManager
     * @param \Composer\Installer\InstallationManager $installationManager
     * @param string $rootDir
     */
    public function __construct(
        \Composer\Repository\RepositoryManager $repositoryManager,
        \Composer\Installer\InstallationManager $installationManager,
        $rootDir
    ) {
        $this->repositoryManager = $repositoryManager;
        $this->installationManager = $installationManager;
        $this->rootDir = $rootDir;
    }

    /**
     * @param string $packageName
     * @return array
     * @throws \Exception
     */
    public function getPackagePaths($packageName)
    {
        $packageRepository = $this->repositoryManager->getLocalRepository();

        if (!$package = $packageRepository->findPackage($packageName, ComposerConfig::CONSTRAINT_ANY)) {
            return array();
        }

        $transportOptions = $package->getTransportOptions();

        $bundleRoot = $transportOptions['bundle-root'];

        if (!is_dir($bundleRoot) && is_dir($this->rootDir . DIRECTORY_SEPARATOR . $bundleRoot)) {
            $bundleRoot = $this->rootDir . DIRECTORY_SEPARATOR . $bundleRoot;
        }

        return array(
            'name' => $package->getName(),
            'owner' => is_dir($bundleRoot) ? realpath($bundleRoot) : $bundleRoot,
            'origin' => $package->getDistUrl(),
            'target' => $this->installationManager->getInstallPath($package)
        );
    }
}
