<?php

namespace Drupal\wmmodel\Factory;

use Drupal\Core\Entity\EntityTypeInterface;

interface ModelFactoryInterface
{
    /** Get the model class of an entity */
    public function getClassName(EntityTypeInterface $entityType, string $bundle): string;

    /** Rebuild the class mapping */
    public function rebuildMapping(): void;
}
