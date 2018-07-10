<?php
/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */
namespace Vaimo\ComposerRepositoryBundle\Bundle;

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
     * @param \Composer\Repository\RepositoryManager $repositoryManager
     * @param \Composer\Installer\InstallationManager $installationManager
     */
    public function __construct(
        \Composer\Repository\RepositoryManager $repositoryManager,
        \Composer\Installer\InstallationManager $installationManager
    ) {
        $this->repositoryManager = $repositoryManager;
        $this->installationManager = $installationManager;
    }

    /**
     * @param string $packageName
     * @return array
     * @throws \Exception
     */
    public function getPackagePaths($packageName)
    {
        $packageRepository = $this->repositoryManager->getLocalRepository();

        if (!$matches = $packageRepository->findPackages($packageName)) {
            return array();
        }

        $package = reset($matches);

        $transportOptions = $package->getTransportOptions();

        return array(
            'owner' => $transportOptions['bundle-root'],
            'origin' => $package->getDistUrl(),
            'name' => $this->installationManager->getInstallPath($package)
        );
    }
}
