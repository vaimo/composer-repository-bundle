<?php
/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */
namespace Vaimo\ComposerRepositoryBundle;

class Plugin implements \Composer\Plugin\PluginInterface,
    \Composer\EventDispatcher\EventSubscriberInterface, \Composer\Plugin\Capable
{
    /**
     * @var \Vaimo\ComposerRepositoryBundle\Managers\BundlesManager
     */
    private $bundlesManager;

    /**
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    private $commandInput;

    public function activate(\Composer\Composer $composer, \Composer\IO\IOInterface $io)
    {
        $this->bundlesManager = new \Vaimo\ComposerRepositoryBundle\Managers\BundlesManager($composer, $io);
    }

    public static function getSubscribedEvents()
    {
        return array(
            \Composer\Plugin\PluginEvents::COMMAND => 'onCommandEvent',
            \Composer\Script\ScriptEvents::PRE_INSTALL_CMD => 'installBundles',
            \Composer\Script\ScriptEvents::PRE_UPDATE_CMD => 'updateBundles'
        );
    }

    public function onCommandEvent(\Composer\Plugin\CommandEvent $event)
    {
        $this->commandInput = $event->getInput();
    }

    /**
     * @throws \Exception
     */
    public function installBundles()
    {
        $this->bundlesManager->bootstrap();
    }

    /**
     * @throws \Exception
     */
    public function updateBundles()
    {
        // @todo: re-install only when package config has changed (md5 calc for source + hash)
        // @todo: if whole package gets changed, force all to be re-installed
        $this->bundlesManager->bootstrap();

        $this->bundlesManager->processPackages(
            $this->commandInput->getArgument('packages')
        );
    }

    public function getCapabilities()
    {
        return array(
            'Composer\Plugin\Capability\CommandProvider' =>
                '\Vaimo\ComposerRepositoryBundle\Composer\Plugin\CommandsProvider'
        );
    }
}
