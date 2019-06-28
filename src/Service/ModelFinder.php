<?php

namespace Drupal\wmmodel\Service;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\wmmodel\Entity\Interfaces\WmModelInterface;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use ReflectionClass;
use RegexIterator;

class ModelFinder
{

    /**
     * Finds models inside a module
     *
     * @param $moduleName
     * @param string $namespace
     *      The namespace where the models live, defaults to 'Entity'
     *      Gets injected into \Drupal\$moduleName\$namespace\
     * @return array
     */
    public function findModels($moduleName, $namespace = 'Entity')
    {
        $models = [];
        $namespace = trim($namespace, '\\');
        $dir = drupal_get_path('module', $moduleName) . '/src/' . $namespace;
        $namespace = '\Drupal\\' . $moduleName . '\\' . $namespace . '\\';

        if (!file_exists($dir)) {
            return $models;
        }

        $dirItt = new RecursiveDirectoryIterator(
            $dir,
            FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS
        );
        $dirIttItt = new RecursiveIteratorIterator($dirItt);
        $matches = new RegexIterator($dirIttItt, '/^.*\.php$/i', RecursiveRegexIterator::GET_MATCH);

        foreach ($matches as $match) {
            // Skip if no match
            if (empty($match[0])) {
                continue;
            }

            $class = preg_replace('/\.php$/', '', str_replace($dir . '/', '', $match[0]));
            $class = $namespace . str_replace('/', '\\', $class);

            if (!class_exists($class)) {
                continue;
            }

            $ref = new ReflectionClass($class);

            // Skip if not isInstantiable or ContentEntityInterface
            if (
                !$ref->isInstantiable()
                || !$ref->implementsInterface(ContentEntityInterface::class)
                || !$ref->implementsInterface(WmModelInterface::class)
            ) {
                continue;
            }

            list($type, $bundle) = call_user_func([$class, 'getModelInfo']);

            $key = $type . '.' . $bundle;
            $models[$key] = $class;
        }

        return $models;
    }

}