<?php

namespace Drupal\wmmodel\Entity\Traits;

trait WmModel
{
    /** @return static[] */
    public static function loadMultiple(array $ids = null)
    {
        $entityTypeRepository = \Drupal::service('entity_type.repository');
        $entityTypeManager = \Drupal::entityTypeManager();

        $entityTypeId = $entityTypeRepository->getEntityTypeFromClass(static::class);
        $storage = $entityTypeManager->getStorage($entityTypeId);
        $bundle = $storage->getBundleFromClass(static::class);
        $entityType = $entityTypeManager->getDefinition($entityTypeId);

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
}
