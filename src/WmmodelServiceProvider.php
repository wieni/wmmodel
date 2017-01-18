<?php

namespace Drupal\wmmodel;

use Drupal\Core\DependencyInjection\ServiceModifierInterface;
use Drupal\Core\DependencyInjection\ContainerBuilder;

class WmmodelServiceProvider implements ServiceModifierInterface
{
    public function alter(ContainerBuilder $container)
    {
        $container->setParameter(
            'twig.config',
            $container->getParameter('twig.config') +
            [
                'base_template_class' => '\\Drupal\\wmmodel\\Twig\\Template',
            ]
        );
    }
}

