<?php
/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */
namespace Vaimo\ComposerRepositoryBundle;

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
     * @var string[]
     */
    private $specialCaseCommands = array('require');

    public function activate(\Composer\Composer $composer, \Composer\IO\IOInterface $io)
    {
        $this->io = $io;

        $this->bundlesManager = new \Vaimo\ComposerRepositoryBundle\Managers\BundlesManager($composer, $io);

        $input = new \Symfony\Component\Console\Input\ArgvInput();

        if (!in_array($input->getFirstArgument(), $this->specialCaseCommands)) {
            return;
        }

        $this->bootstrapBundles();
    }

    public static function getSubscribedEvents()
    {
        return array(
            \Composer\Script\ScriptEvents::PRE_INSTALL_CMD => 'bootstrapBundles',
            \Composer\Script\ScriptEvents::PRE_UPDATE_CMD => 'bootstrapBundles'
        );
    }

    public function bootstrapBundles()
    {
        $this->bundlesManager->bootstrap();
    }

    public function getCapabilities()
    {
        return array(
            'Composer\Plugin\Capability\CommandProvider' =>
                '\Vaimo\ComposerRepositoryBundle\Composer\Plugin\CommandsProvider'
        );
    }
}
