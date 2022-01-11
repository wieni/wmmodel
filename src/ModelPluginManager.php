<?php

namespace Drupal\wmmodel;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\wmmodel\Annotation\Model;

class ModelPluginManager extends DefaultPluginManager
{
    public function __construct(
        \Traversable $namespaces,
        CacheBackendInterface $cacheBackend,
        ModuleHandlerInterface $moduleHandler
    ) {
        parent::__construct(
            'Entity',
            $namespaces,
            $moduleHandler,
            ContentEntityInterface::class,
            Model::class
        );
        $this->alterInfo('wmmodel_model_info');
        $this->setCacheBackend($cacheBackend, 'wmmodel_model_info_plugins');
    }
}
