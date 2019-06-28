<?php

namespace Drupal\wmmodel\Factory;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\State\StateInterface;

class ModelFactory
{
    /** @var array */
    protected static $mapping = [];

    /** @var StateInterface */
    protected $state;
    /** @var ModuleHandlerInterface */
    protected $moduleHandler;

    public function __construct(
        StateInterface $state,
        ModuleHandlerInterface $moduleHandler
    ) {
        $this->state = $state;
        $this->moduleHandler = $moduleHandler;
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
     * Load the mapping from state
     */
    public function rebuildMapping()
    {
        // Fetch mappings from other modules.
        $mapping = $this->moduleHandler->invokeAll('entity_model_mapping');
        // Allow modules to alter the assigned mappings.
        $this->moduleHandler->alter('entity_model_mapping', $mapping);
        // Cache the mapping
        $this->state->set('wmmodel', $mapping);
        // Cache the mapping using a static variable
        static::$mapping = $mapping;
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
     * Load the mapping from state
     */
    private function loadMapping()
    {
        if (empty(static::$mapping) && $mapping = $this->state->get('wmmodel', [])) {
            static::$mapping = $mapping;
        }
    }

}
