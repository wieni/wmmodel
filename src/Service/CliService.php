<?php

namespace Drupal\wmmodel\Service;

use Drupal\Core\State\StateInterface;
use Drupal\wmmodel\Entity\EntityTypeBundleInfo;

class CliService
{
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
     * List all models and their mappings
     *
     * @param \Symfony\Component\Console\Style\StyleInterface|\Drush8Io $io
     *   The io interface of the cli tool calling the method.
     *
     * @param callable $t
     *   The translation function akin to t().
     *
     * @return array
     */
    public function listModels($io, callable $t)
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
                $io->text($t('Model "@model" is not mapped.', ['@model' => $modelKey]));
            } else {
                $io->text($t('Model "@model" is mapped against "@mapping".', ['@model' => $modelKey, '@mapping' => $map]));
            }
        }
    }

}
