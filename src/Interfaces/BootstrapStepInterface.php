<?php
/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */
namespace Vaimo\ComposerRepositoryBundle\Interfaces;

interface BootstrapStepInterface
{
    /**
     * @param array $bundles
     */
    public function execute(array $bundles);
}
