<?php

namespace Drupal\wmmodel\Factory;

use Doctrine\Common\Inflector\Inflector;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;

class ModelFactory
{

    /** @var array */
    protected static $mapping;

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
     * Creates an instance of the requested model
     *
     * @param array $values
     * @param EntityTypeInterface $entity_type
     * @param bool $bundle
     * @param array $translations
     * @return ContentEntityInterface
     */
    public function createModel(
        array $values,
        EntityTypeInterface $entity_type,
        $bundle = false,
        $translations = array()
    ) {
        $modelName = $entity_type->id() . '.' . Inflector::singularize($bundle);

        // By default, use the given class
        $className = $entity_type->getClass();

        // If the model is mapped to a specific class, use that one instead
        if ($this->modelIsMapped($modelName)) {
            $className = $this->getClassName($modelName);
        }

        return new $className($values, $entity_type->id(), $bundle, $translations);
    }

    /**
     * Get the class for a model
     *
     * @param $modelName
     * @return string
     */
    public function getClassName($modelName)
    {
        return static::$mapping[$modelName] ?? '';
    }

    /**
     * @param $modelName
     * @return bool
     */
    public function modelIsMapped($modelName)
    {
        return isset(static::$mapping[$modelName]);
    }

    /**
     * Load the mapping from cache
     */
    protected function loadMapping()
    {
        if (empty(static::$mapping)) {
            $mapping = $this->cacheBackend->get('mapping')->data;
            static::$mapping = $mapping;
        }
    }

}