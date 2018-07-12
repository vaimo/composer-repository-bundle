<?php
/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */
namespace Vaimo\ComposerRepositoryBundle\Analysers;

class PhpFileAnalyser
{
    public function collectPhpClasses($path)
    {
        $code = file_get_contents($path);
        $tokens = array_filter((array)@token_get_all($code));

        $namespace = $class = $classLevel = $level = null;

        $classes = array();

        do {
            $token = current($tokens);

            if (!$result = next($tokens)) {
                break;
            }

            switch (is_array($token) ? $token[0] : $token) {
                case T_NAMESPACE:
                    $namespace = ltrim($this->collectValue($tokens, array(T_STRING, T_NS_SEPARATOR)) . '\\', '\\');
                    break;
                case T_CLASS:
                case T_INTERFACE:
                    if ($name = $this->collectValue($tokens, array(T_STRING))) {
                        $classes[] = $namespace . $name;
                    }

                    break;
            }
        } while ($result);

        return $classes;
    }

    private function collectValue(array &$tokens, array $typeFilter)
    {
        $result = null;

        while ($token = current($tokens)) {
            list($token, $value) = is_array($token) ? $token : array($token, $token);

            if (in_array($token, (array)$typeFilter, true)) {
                $result .= $value;
            } elseif (!in_array($token, array(T_DOC_COMMENT, T_WHITESPACE, T_COMMENT), true)) {
                break;
            }

            next($tokens);
        }

        return $result;
    }
}
