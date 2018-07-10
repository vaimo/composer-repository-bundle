<?php
/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */
namespace Vaimo\ComposerRepositoryBundle\Composer;

class Plugin implements \Composer\Plugin\PluginInterface,
    \Composer\EventDispatcher\EventSubscriberInterface, \Composer\Plugin\Capable
{
    /**
     * @var \Vaimo\ComposerRepositoryBundle\Managers\BundlesManager
     */
    private $bundlesManager;

    public function activate(\Composer\Composer $composer, \Composer\IO\IOInterface $io)
    {
        $this->bundlesManager = new \Vaimo\ComposerRepositoryBundle\Managers\BundlesManager($composer, $io);
    }

    public static function getSubscribedEvents()
    {
        return array(
            \Composer\Script\ScriptEvents::PRE_UPDATE_CMD => 'bootstrapBundles',
            \Composer\Script\ScriptEvents::PRE_INSTALL_CMD => 'bootstrapBundles',
            \Composer\Installer\PackageEvents::PRE_PACKAGE_UPDATE => 'processPackage'
        );
    }

    /**
     * @throws \Exception
     */
    public function bootstrapBundles()
    {
        $this->bundlesManager->bootstrap();
    }

    public function processPackage()
    {
        $this->bundlesManager->processPackage();
    }

    public function getCapabilities()
    {
        return array(
            'Composer\Plugin\Capability\CommandProvider' =>
                '\Vaimo\ComposerRepositoryBundle\Composer\Plugin\CommandsProvider'
        );
    }
}
