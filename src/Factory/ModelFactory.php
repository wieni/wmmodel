<?php

namespace Drupal\wmmodel\Factory;

use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityTypeRepositoryInterface;
use Drupal\Core\Entity\Exception\AmbiguousBundleClassException;
use Drupal\Core\Entity\Exception\AmbiguousEntityClassException;
use Drupal\Core\Entity\Exception\NoCorrespondingEntityClassException;
use Drupal\wmmodel\ModelPluginManager;

class ModelFactory implements ModelFactoryInterface
{
    /** @var EntityTypeManagerInterface */
    protected $entityTypeManager;
    /** @var EntityTypeRepositoryInterface */
    protected $entityTypeRepository;
    /** @var ModelPluginManager */
    protected $pluginManager;

    public function __construct(
        EntityTypeManagerInterface $entityTypeManager,
        EntityTypeRepositoryInterface $entityTypeRepository,
        ModelPluginManager $pluginManager
    ) {
        $this->entityTypeManager = $entityTypeManager;
        $this->entityTypeRepository = $entityTypeRepository;
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
        try {
            $entityTypeId = $this->entityTypeRepository->getEntityTypeFromClass(static::class);
            $storage = $this->entityTypeManager->getStorage($entityTypeId);
            $bundle = $storage->getBundleFromClass(static::class);
        } catch (AmbiguousEntityClassException $e) {
            return null;
        } catch (NoCorrespondingEntityClassException $e) {
            return null;
        }

        return [$entityTypeId, $bundle];
    }

    public function rebuildMapping(): void
    {
        $this->pluginManager->clearCachedDefinitions();
    }
}
