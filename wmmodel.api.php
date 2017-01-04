<?php

/**
 * @file
 * Hooks provided by the Wieni Model module.
 */

/**
 * Add content types to be converted to a Model
 *
 * @return array
 */
function hook_entity_model_mapping()
{
    $mapping = [];

    // Map a bundle "article" with entity type "node" to a custom class
    $mapping['node_article'] = 'Drupal\mymodule\Entity\Node\Article';

    return $mapping;
}

/**
 * Alter existing mappings
 *
 * @param array &$mapping
 *   Array containing the mapping between entities and classes
 */
function hook_entity_model_mapping_alter(&$mapping)
{
    // Map a bundle "article" with entity type "node" to a custom class
    $mapping['node_article'] = 'Drupal\mymodule\Entity\Node\Article';
}
