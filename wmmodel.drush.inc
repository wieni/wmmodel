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
    $entity_types = \Drupal::entityTypeManager()->getDefinitions();
    if ($mapping = \Drupal::cache()->get('wmmodel.mapping')) {
        $mapping = $mapping->data ?: [];
    } else {
        $mapping = [];
    }

    // Get a list of all bundles for node and eck_entity
    $types = [];
    $types = array_merge($types, _wmmodel_get_types($entity_types, 'node'));
    $types = array_merge($types, _wmmodel_get_types($entity_types, 'eck_entity', true));

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

function _wmmodel_get_types($entityTypes, $entityType, $ignore_parentName = false)
{
    $entity_manager = \Drupal::entityTypeManager();

    $type = $entityTypes[$entityType . '_type'] ?? false;

    if (!$type) {
        return [];
    }

    // Get the existing bundles.
    $bundles = $entity_manager->createHandlerInstance(
        $type->getHandlerClass('storage'),
        $type
    )->loadMultiple();

    $types = [];
    foreach ($bundles as $bundle => $type) {
        // check for sub bundles (eg with eck)
        $types = array_merge($types, _wmmodel_get_types($entityTypes, $bundle));

        if (!$ignore_parentName) {
            $types[] = $entityType . '_' . $bundle;
        }
    }

    return $types;
}

