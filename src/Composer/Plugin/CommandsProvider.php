<?php
/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */
namespace Vaimo\ComposerRepositoryBundle\Composer\Plugin;

class CommandsProvider implements \Composer\Plugin\Capability\CommandProvider
{
    public function getCommands()
    {
        return array(
            new \Vaimo\ComposerRepositoryBundle\Commands\InfoCommand,
            new \Vaimo\ComposerRepositoryBundle\Commands\DeployCommand,
            new \Vaimo\ComposerRepositoryBundle\Commands\ListCommand
        );
    }
}
