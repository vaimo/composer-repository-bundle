<?php
/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */
namespace Vaimo\ComposerRepositoryBundle;

use Composer\Composer;
use Composer\IO\IOInterface;
use Vaimo\ComposerRepositoryBundle\Managers;
use Vaimo\ComposerRepositoryBundle\Analysers;

class Plugin implements \Composer\Plugin\PluginInterface, \Composer\Plugin\Capable,
    \Composer\EventDispatcher\EventSubscriberInterface
{
    /**
     * @var \Composer\IO\IOInterface
     */
    private $io;

    /**
     * @var \Vaimo\ComposerRepositoryBundle\Managers\BundlesManager
     */
    private $bundlesManager;

    /**
     * @var \Vaimo\ComposerRepositoryBundle\Analysers\ComposerOperationAnalyser
     */
    private $operationAnalyser;

    /**
     * @var string[]
     */
    private $specialCaseCommands = array('require');

    public function activate(\Composer\Composer $composer, \Composer\IO\IOInterface $io)
    {
        $this->io = $io;

        $this->bundlesManager = new Managers\BundlesManager($composer, $io);
        $this->operationAnalyser = new Analysers\ComposerOperationAnalyser();

        try {
            $input = new \Symfony\Component\Console\Input\ArgvInput();
        } catch (\Exception $e) {
            // There are situations where composer is accessed from non-CLI entry points,
            // which will cause $argv not to be available, resulting a crash.
            return;
        }

        if (!in_array($input->getFirstArgument(), $this->specialCaseCommands)) {
            return;
        }

        $this->bootstrapBundles();
    }

    public static function getSubscribedEvents()
    {
        return array(
            \Composer\Script\ScriptEvents::PRE_INSTALL_CMD => 'bootstrapBundles',
            \Composer\Script\ScriptEvents::PRE_UPDATE_CMD => 'bootstrapBundles',
            \Composer\Installer\PackageEvents::PRE_PACKAGE_UNINSTALL => 'onPackageUninstall'
        );
    }

    public function bootstrapBundles()
    {
        if (!$this->bundlesManager) {
            return;
        }

        $this->bundlesManager->bootstrap();
    }

    public function getCapabilities()
    {
        return array(
            'Composer\Plugin\Capability\CommandProvider' =>
                '\Vaimo\ComposerRepositoryBundle\Composer\Plugin\CommandsProvider'
        );
    }

    public function onPackageUninstall(\Composer\Installer\PackageEvent $event)
    {
        if (!$this->operationAnalyser->isPluginUninstallOperation($event->getOperation())) {
            return;
        }

        $this->bundlesManager = null;
    }

    public function deactivate(Composer $composer, IOInterface $io)
    {
    }

    public function uninstall(Composer $composer, IOInterface $io)
    {
    }
}
