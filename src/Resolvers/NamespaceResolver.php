<?php
/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */
namespace Vaimo\ComposerRepositoryBundle\Resolvers;

class NamespaceResolver
{
    /**
     * @var \Vaimo\ComposerRepositoryBundle\Analysers\PhpFileAnalyser
     */
    private $phpFileAnalyser;

    public function __construct()
    {
        $this->phpFileAnalyser = new \Vaimo\ComposerRepositoryBundle\Analysers\PhpFileAnalyser();
    }

    public function resolveSharedNamespaceForFiles(array $files)
    {
        $classes = array();

        foreach ($files as $file) {
            $classes = array_merge(
                $classes,
                array_filter($this->phpFileAnalyser->collectPhpClasses($file))
            );
        }

        $classParts = array_filter(explode('\\', reset($classes)));

        $namespace = '';

        foreach ($classParts as $item) {
            $lookup = $namespace . $item;

            $result = preg_grep('#^' . preg_quote($lookup, '#') . '#', $classes);

            if ($result !== $classes) {
                break;
            }

            $namespace = $lookup . '\\';
        }

        return $namespace;
    }
}
