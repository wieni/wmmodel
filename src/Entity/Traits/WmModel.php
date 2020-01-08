<?php

namespace Drupal\wmmodel\Entity\Traits;

use Drupal\Core\Entity\Exception\NoCorrespondingEntityClassException;

trait WmModel
{
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
