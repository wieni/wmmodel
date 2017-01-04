<?php

namespace Drupal\wmmodel\Entity\Traits;

use Drupal\Core\Entity\ContentEntityInterface;

trait FieldHelpers
{

    protected function removeReference(ContentEntityInterface $entity, $fieldName, $referenceName = 'target_id')
    {
        $id = $entity->id();
        $tagReferences = $this->get($fieldName)->getValue();

        // Loop over the values, but don't necessarily load them as entities
        foreach ($tagReferences as $delta => $reference) {
            if ($reference[$referenceName] == $id) {
                $this->get($fieldName)->removeItem($delta);
                return;
            }
        }
    }

    protected function removeFromList($value, $fieldName)
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        $values = array_diff(
            array_values($this->get($fieldName)->getValue()),
            $value
        );

        $this->set($fieldName, $values);
    }
}