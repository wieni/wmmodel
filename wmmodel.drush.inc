<?php

use Drupal\wmmodel\Service\Drush8Io;

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
    \Drupal::service('wmmodel.cli')->listModels(new Drush8Io(), 'dt');
}
