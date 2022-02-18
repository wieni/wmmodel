<?php

namespace Drupal\wmmodel;

use Drupal\Core\Config\BootstrapConfigStorageFactory;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceModifierInterface;
use Drupal\wmmodel\Session\AccountProxy;
use Symfony\Component\DependencyInjection\Reference;

class WmmodelServiceProvider implements ServiceModifierInterface
{
    public function alter(ContainerBuilder $container)
    {
        $config = BootstrapConfigStorageFactory::get()->read('wmmodel.settings');

        if (!empty($config['override_account_proxy'])) {
            $container->getDefinition('current_user')
                ->setClass(AccountProxy::class);
        }

        $argumentResolver = $container->getDefinition('http_kernel.controller.argument_resolver');
        $argumentValueResolvers = $argumentResolver->getArgument(1);
        array_unshift($argumentValueResolvers, new Reference('wmmodel.argument_resolver'));
        $argumentResolver->setArgument(1, $argumentValueResolvers);
    }
}
