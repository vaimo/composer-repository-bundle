<?php
/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */
namespace Vaimo\ComposerRepositoryBundle\Console;

class Output
{
    /**
     * @var \Composer\IO\IOInterface
     */
    private $io;

    /**
     * @var bool
     */
    private $isVerbose;

    /**
     * @param \Composer\IO\IOInterface $io
     * @param $isVerbose
     */
    public function __construct(
        \Composer\IO\IOInterface $io,
        $isVerbose = false
    ) {
        $this->io = $io;
        $this->isVerbose = $isVerbose;
    }

    public function info()
    {
        if (!$this->isVerbose) {
            return;
        }

        $args = func_get_args();

        $this->io->write(
            '<info>' . count($args) > 1 ? call_user_func_array('sprintf', $args) : reset($args) . '</info>'
        );
    }

    public function raw()
    {
        if (!$this->isVerbose) {
            return;
        }

        $args = func_get_args();

        $this->io->write(
            count($args) > 1 ? call_user_func_array('sprintf', $args) : reset($args)
        );
    }

    public function nl()
    {
        if (!$this->isVerbose) {
            return;
        }

        $this->io->write('');
    }
}
