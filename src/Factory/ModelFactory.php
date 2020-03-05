<?php

namespace Drupal\wmmodel\Factory;

use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\wmmodel\ModelPluginManager;

class ModelFactory implements ModelFactoryInterface
{
    /** @var EntityTypeManagerInterface */
    protected $entityTypeManager;
    /** @var ModelPluginManager */
    protected $pluginManager;

    public function __construct(
        EntityTypeManagerInterface $entityTypeManager,
        ModelPluginManager $pluginManager
    ) {
        $this->entityTypeManager = $entityTypeManager;
        $this->pluginManager = $pluginManager;
    }

    public function getClassName(EntityTypeInterface $entityType, string $bundle): string
    {
        try {
            $id = implode('.', [$entityType->id(), $bundle]);
            $definition = $this->pluginManager->getDefinition($id);
        } catch (PluginNotFoundException $e) {
            // By default, use the parent entity class
            return $entityType->getClass();
        }

        return $definition['class'];
    }

    public function getEntityTypeAndBundle(string $className): ?array
    {
        foreach ($this->pluginManager->getDefinitions() as $definition) {
            if ($definition['class'] === $className) {
                return [$definition['entity_type'], $definition['bundle']];
            }
        }

        foreach ($this->entityTypeManager->getDefinitions() as $definition) {
            if ($definition->getClass() === $className) {
                return [$definition->id(), null];
            }
        }

        return null;
    }

    public function rebuildMapping(): void
    {
        $this->pluginManager->clearCachedDefinitions();
    }
}
