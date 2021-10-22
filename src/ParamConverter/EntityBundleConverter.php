<?php

namespace Drupal\wmmodel\ParamConverter;

use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\ParamConverter\EntityConverter;
use Drupal\Core\ParamConverter\ParamNotConvertedException;
use Symfony\Component\Routing\Route;

class EntityBundleConverter extends EntityConverter
{
    public function applies($definition, $name, Route $route)
    {
        if (
            !empty($definition['type'])
            && ($parts = explode(':', $definition['type'], 3))
            && count($parts) === 3
        ) {
            [, $entityTypeId, $bundle] = $parts;

            try {
                $typeDefinition = $this->entityTypeManager->getDefinition($entityTypeId);
            } catch (PluginNotFoundException $e) {
                return false;
            }

            $count = $this->entityTypeManager
                ->getStorage($typeDefinition->getBundleEntityType())
                ->getQuery()
                ->accessCheck(false)
                ->condition($typeDefinition->getKey('bundle'), $bundle)
                ->count()
                ->execute();

            return (bool) $count;
        }

        return false;
    }

    protected function getEntityTypeFromDefaults($definition, $name, array $defaults)
    {
        if (
            !empty($definition['type'])
            && ($parts = explode(':', $definition['type'], 3))
            && count($parts) === 3
        ) {
            [, $entityTypeId, $bundle] = $parts;

            try {
                $typeDefinition = $this->entityTypeManager->getDefinition($entityTypeId);
            } catch (PluginNotFoundException $e) {
                return false;
            }

            $entityCount = (int) $this->entityTypeManager
                ->getStorage($entityTypeId)
                ->getQuery()
                ->accessCheck(false)
                ->condition($typeDefinition->getKey('bundle'), $bundle)
                ->condition($typeDefinition->getKey('id'), $defaults[$name])
                ->count()
                ->execute();

            if ($entityCount > 0) {
                return $entityTypeId;
            }
        }

        throw new ParamNotConvertedException(sprintf('The type definition "%s" is invalid. The expected format is "entity:<entity_type_id>:<bundle_id>".', $definition['type']));
    }
}
