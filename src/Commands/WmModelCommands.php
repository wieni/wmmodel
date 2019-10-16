<?php

namespace Drupal\wmmodel\Commands;

use Drupal\Core\State\StateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\wmmodel\Entity\EntityTypeBundleInfo;
use Drush\Commands\DrushCommands;

class WmModelCommands extends DrushCommands
{
    use StringTranslationTrait;

    /** @var EntityTypeBundleInfo */
    private $bundleInfo;
    /** @var StateInterface */
    private $state;

    public function __construct(
        EntityTypeBundleInfo $bundleInfo,
        StateInterface $state
    ) {
        $this->bundleInfo = $bundleInfo;
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
        foreach ($this->bundleInfo->getAllBundleInfo() as $entityType => $bundles) {
            foreach (array_keys($bundles) as $bundle) {
                $types[] = "$entityType.$bundle";
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
