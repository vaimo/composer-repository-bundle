<?php
/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */
namespace Vaimo\ComposerRepositoryBundle\FileSystem;

class ChecksumCalculator
{
    /**
     * @var \Vaimo\ComposerRepositoryBundle\FileSystem\FilesCollector
     */
    private $filesCollector;

    public function __construct()
    {
        $this->filesCollector = new \Vaimo\ComposerRepositoryBundle\FileSystem\FilesCollector();
    }

    public function calculate($target, $includeContents = false)
    {
        if (is_file($target)) {
            return md5($target);
        }

        $filePaths = $this->filesCollector->collectFiles($target);

        $fileFootprints = array();

        sort($filePaths);

        foreach ($filePaths as $filePath) {
            $fileFootprints[$filePath] = $includeContents ? md5_file($filePath) : true;
        }

        return md5(
            serialize($fileFootprints)
        );
    }
}
