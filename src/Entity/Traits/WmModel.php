<?php

namespace Drupal\wmmodel\Entity\Traits;

use Drupal\Core\Entity\Exception\NoCorrespondingEntityClassException;

trait WmModel
{
    public static function loadMultiple(array $ids = null)
    {
        $entityTypeManager = \Drupal::entityTypeManager();
        $modelFactory = \Drupal::service('wmmodel.factory.model');

        if (!$definition = $modelFactory->getEntityTypeAndBundle(static::class)) {
            throw new NoCorrespondingEntityClassException(static::class);
        }

        [$entityTypeId, $bundle] = $definition;
        $entityType = $entityTypeManager->getDefinition($entityTypeId);
        $storage = $entityTypeManager->getStorage($entityTypeId);

        $query = $storage->getQuery()
            ->condition($entityType->getKey('bundle'), $bundle);

        if ($ids) {
            $query->condition($entityType->getKey('id'), $ids, 'IN');
        }

        $ids = $query->execute();

        if (empty($ids)) {
            return [];
        }

        return $storage->loadMultiple($ids);
    }

    public static function create(array $values = [])
    {
        $entityTypeManager = \Drupal::entityTypeManager();
        $modelFactory = \Drupal::service('wmmodel.factory.model');

        if (!$definition = $modelFactory->getEntityTypeAndBundle(static::class)) {
            throw new NoCorrespondingEntityClassException(static::class);
        }

        [$entityTypeId, $bundle] = $definition;
        $bundleKey = $entityTypeManager
            ->getDefinition($entityTypeId)
            ->getKey('bundle');
        $values[$bundleKey] = $bundle;

        return $entityTypeManager
            ->getStorage($entityTypeId)
            ->create($values);
    }
}
