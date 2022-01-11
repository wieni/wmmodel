<?php

namespace Drupal\Tests\wmmodel\Kernel;

use Drupal\Component\Plugin\Discovery\StaticDiscovery;
use Drupal\Component\Plugin\Factory\ReflectionFactory;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;
use Drupal\node\NodeTypeInterface;
use Drupal\wmmodel\Annotation\Model;
use Drupal\wmmodel\ModelPluginManager;

abstract class ModelPluginTestBase extends KernelTestBase
{
    public static $modules = [
        'node',
        'system',
        'user',
        'wmmodel'
    ];

    /** @var MockModelPluginManager */
    protected $pluginManager;
    /** @var NodeTypeInterface */
    protected $nodeType;

    protected function setUp()
    {
        parent::setUp();

        $this->pluginManager = $this->container->get('plugin.manager.wmmodel.model');
        $this->nodeType = NodeType::create(['type' => 'node_mock']);
        $this->nodeType->save();
    }

    public function register(ContainerBuilder $container)
    {
        parent::register($container);

        $container->getDefinition('plugin.manager.wmmodel.model')
            ->setClass(MockModelPluginManager::class);
    }
}

class MockModelPluginManager extends ModelPluginManager
{
    public function __construct(
        \Traversable $namespaces,
        CacheBackendInterface $cacheBackend,
        ModuleHandlerInterface $moduleHandler
    ) {
        parent::__construct($namespaces, $cacheBackend, $moduleHandler);

        $this->discovery = new StaticDiscovery();
        $this->factory = new ReflectionFactory($this->discovery);
    }

    public function addDefinition(): void
    {
        $annotation = new Model([
            'entity_type' => 'node',
            'bundle' => 'node_mock',
            'class' => NodeMock::class,
        ]);

        $this->discovery->setDefinition($annotation->getId(), $annotation->get());
    }
}

class NodeMock extends Node
{
}
