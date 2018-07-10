<?php
/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */
namespace Vaimo\ComposerRepositoryBundle\Composer\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InfoCommand extends \Composer\Command\BaseCommand
{
    protected function configure()
    {
        $this->setName('bundle:info');

        $this->setDescription('Display information about bundle package owner and installation path');

        $this->addArgument(
            'name',
            \Symfony\Component\Console\Input\InputArgument::REQUIRED,
            'Targeted bundle package name'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $packageName = $input->getArgument('name');

        $pathInfo = new \Vaimo\ComposerRepositoryBundle\Bundle\PathInfo(
            $this->getComposer()->getRepositoryManager(),
            $this->getComposer()->getInstallationManager()
        );

        try {
            $result = $pathInfo->getPackagePaths($packageName);
        } catch (\Exception $e) {
            $result = array();
        }

        $output->writeln(
            $result ? json_encode($result, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) : '{}'
        );
    }
}
