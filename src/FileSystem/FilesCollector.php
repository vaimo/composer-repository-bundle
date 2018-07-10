<?php
/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */
namespace Vaimo\ComposerRepositoryBundle\FileSystem;

class FilesCollector
{
    public function collectFilesWithExtension($path, $extension)
    {
        $recursiveIterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path)
        );

        $files = array();

        foreach ($recursiveIterator as $fileInfo) {
            if ($fileInfo->isDir()) {
                continue;
            }

            if (pathinfo($fileInfo->getPathname(), PATHINFO_EXTENSION) !== $extension) {
                continue;
            }

            $files[] = $fileInfo->getPathname();
        }

        return $files;
    }
}
