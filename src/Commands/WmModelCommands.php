<?php

namespace Drupal\wmmodel\Commands;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\State\StateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drush\Commands\DrushCommands;

class WmModelCommands extends DrushCommands
{
    use StringTranslationTrait;

    /** @var EntityTypeManagerInterface */
    protected $entityTypeManager;
    /** @var StateInterface */
    protected $state;

    public function __construct(
        EntityTypeManagerInterface $entityTypeManager,
        StateInterface $state
    ) {
        $this->entityTypeManager = $entityTypeManager;
        $this->state = $state;
    }

    /**
     * List all bundles and their mapping
     *
     * @command wmmodel:list
     * @aliases model-list,wml
     */
    public function listModels()
    {
        $mapping = $this->state->get('wmmodel', []);
        $types = [];

        foreach ($this->entityTypeManager->getDefinitions() as $type => $entityType) {
            if (!$bundleEntityType = $entityType->getBundleEntityType()) {
                continue;
            }

            $bundles = $this->entityTypeManager
                ->getStorage($bundleEntityType)
                ->getQuery()
                ->execute();

            foreach ($bundles as $bundle) {
                $types[] = "$type.{$bundle}";
            }
        }

        sort($types);

        foreach ($types as $modelKey) {
            $map = $mapping[$modelKey] ?? '';

            if (!$map) {
                $this->io()->text($this->t('Model "@model" is not mapped.', ['@model' => $modelKey]));
            } else {
                $this->io()->text($this->t('Model "@model" is mapped against "@mapping".', ['@model' => $modelKey, '@mapping' => $map]));
            }
        }
    }
}
