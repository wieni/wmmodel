<?php

namespace Drupal\wmmodel;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceModifierInterface;
use Symfony\Component\DependencyInjection\Reference;

class WmmodelServiceProvider implements ServiceModifierInterface
{
    public function alter(ContainerBuilder $container)
    {
        try {
            $container->getDefinition('current_user')
                ->setClass('Drupal\\wmmodel\\Session\\AccountProxy');
        } catch (ServiceNotFoundException $e) {
        }
    }
}
