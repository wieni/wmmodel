<?php

namespace Drupal\wmmodel\Factory;

use Doctrine\Common\Inflector\Inflector;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Entity\EntityTypeInterface;

class ModelFactory
{

    /** @var array */
    protected static $mapping = [];

    /** @var CacheBackendInterface */
    protected $cacheBackend;

    /**
     * ModelFactory constructor.
     * @param CacheBackendInterface $cacheBackend
     */
    public function __construct(CacheBackendInterface $cacheBackend)
    {
        $this->cacheBackend = $cacheBackend;
        $this->loadMapping();
    }

    /**
     * Get the model class of an entity
     *
     * @param EntityTypeInterface $entityType
     * @param string $bundle
     * @return string
     */
    public function getClassName(EntityTypeInterface $entityType, $bundle)
    {
        $modelName = $entityType->id() . '.' . $bundle;

        // By default, use the parent entity class
        $className = $entityType->getClass();

        // If the model is mapped to a specific class, use that one instead
        if ($this->modelIsMapped($modelName)) {
            $className = $this->getMappedClassName($modelName);
        }

        return $className;
    }

    /**
     * Get the class for a model
     *
     * @param $modelName
     * @return string
     */
    private function getMappedClassName($modelName)
    {
        return static::$mapping[$modelName] ?? '';
    }

    /**
     * @param $modelName
     * @return bool
     */
    private function modelIsMapped($modelName)
    {
        return isset(static::$mapping[$modelName]);
    }

    /**
     * Load the mapping from cache
     */
    private function loadMapping()
    {
        if (empty(static::$mapping) && $cache = $this->cacheBackend->get('mapping')) {
            static::$mapping = $cache->data;
        }
    }

}
