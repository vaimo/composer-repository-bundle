<?php
/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */
namespace Vaimo\ComposerRepositoryBundle\Factories;

use Composer\Repository\RepositoryInterface;
use Composer\IO\IOInterface;

class CacheFactory
{
    /**
     * @var \Vaimo\ComposerRepositoryBundle\Analysers\RepositoryAnalyser
     */
    private $repositoryAnalyser;

    public function __construct()
    {
        $this->repositoryAnalyser = new \Vaimo\ComposerRepositoryBundle\Analysers\RepositoryAnalyser();
    }

    /**
     * @param \Composer\Repository\RepositoryInterface $repository
     * @param \Composer\IO\IOInterface $io
     * @param string $configDir
     * @return \Composer\Cache
     * @throws \Exception
     */
    public function create(RepositoryInterface $repository, IOInterface $io, $configDir)
    {
        $package = $this->repositoryAnalyser->getPackageForNamespace(
            $repository,
            __NAMESPACE__
        );

        return new \Composer\Cache(
            $io,
            implode(DIRECTORY_SEPARATOR, array(
                $configDir,
                'files',
                $package->getName(),
                'downloads'
            ))
        );
    }
}
