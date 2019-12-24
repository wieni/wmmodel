<?php

namespace Drupal\wmmodel\Entity\Traits;

use Drupal\Core\Entity\Exception\NoCorrespondingEntityClassException;

trait WmModel
{
    public static function create(array $values = [])
    {
        $entityTypeManager = \Drupal::entityTypeManager();
        $modelFactory = \Drupal::service('wmmodel.factory.model');

        if (!$definition = $modelFactory->getDefinition(static::class)) {
            throw new NoCorrespondingEntityClassException(static::class);
        }

        $entityType = $entityTypeManager->getDefinition($definition['entity_type']);
        $values[$entityType->getKey('bundle')] = $definition['bundle'];

        return $entityTypeManager
            ->getStorage($definition['entity_type'])
            ->create($values);
    }
}
