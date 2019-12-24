<?php

namespace Drupal\wmmodel\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * @Annotation
 */
class Model extends Plugin
{
    /** @var string */
    public $entity_type;
    /** @var string */
    public $bundle;

    public function getId()
    {
        if (isset($this->definition['entity_type'], $this->definition['bundle'])) {
            return implode('.', [
                $this->definition['entity_type'],
                $this->definition['bundle'],
            ]);
        }

        return parent::getId();
    }
}
