<?php
/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */
namespace Vaimo\ComposerRepositoryBundle\FileSystem;

class FilesCollector
{
    public function collectFiles($path, $extension = false)
    {
        $recursiveIterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path)
        );

        $files = array();

        foreach ($recursiveIterator as $fileInfo) {
            if ($fileInfo->isDir()) {
                continue;
            }

            if ($extension && pathinfo($fileInfo->getPathname(), PATHINFO_EXTENSION) !== $extension) {
                continue;
            }

            $files[] = $fileInfo->getPathname();
        }

        return $files;
    }
}
