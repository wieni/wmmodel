<?php

namespace Drupal\Tests\wmmodel\Kernel;

use Drupal\wmmodel\Factory\ModelFactoryInterface;

class ModelFactoryTest extends ModelPluginTestBase
{
    /** @var ModelFactoryInterface */
    protected $modelFactory;

    protected function setUp()
    {
        parent::setUp();

        $this->modelFactory = $this->container->get('wmmodel.factory.model');
    }

    public function testGetClassName(): void
    {
        $entityType = $this->nodeType->getEntityType();
        $className = $this->modelFactory->getClassName($entityType, 'node_mock');
        $this->assertEqual($className, $entityType->getClass(), 'getClassName does not fallback to the entity type class.');

        $this->pluginManager->addDefinition();
        $className = $this->modelFactory->getClassName($entityType, 'node_mock');
        $this->assertEqual($className, $entityType->getClass(), 'getClassName does not return the bundle-specific class.');
    }
}
