<?php

function wmmodel_drush_command()
{
    $commands['model-list'] = [
        'description' => 'List all bundles and their mapping',
        'aliases' => ['wml'],
    ];
    return $commands;
}

function drush_wmmodel_model_list()
{
    if ($mapping = \Drupal::cache('wmmodel')->get('mapping')) {
        $mapping = $mapping->data ?: [];
    } else {
        $mapping = [];
    }

    /** @var \Drupal\wmmodel\Entity\EntityTypeBundleInfo $bundleInfo */
    $bundleInfo = \Drupal::service('wmmodel.entity_type.bundle.info');

    $types = [];
    foreach ($bundleInfo->getAllBundleInfo() as $entityType => $bundles) {
        foreach (array_keys($bundles) as $bundle) {
            $bundle = \Doctrine\Common\Inflector\Inflector::singularize($bundle);
            $types[] = "$entityType.$bundle";
        }
    }

    sort($types);

    foreach ($types as $modelKey) {
        $map = $mapping[$modelKey] ?? '';

        if (!$map) {
            printf('Model "%s" is not mapped.' . PHP_EOL, $modelKey);
        } else {
            printf('Model "%s" is mapped against "%s"' . PHP_EOL, $modelKey, $map);
        }
    }
}
