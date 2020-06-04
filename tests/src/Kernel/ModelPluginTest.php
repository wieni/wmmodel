<?php

namespace Drupal\Tests\wmmodel\Kernel;

use Drupal\node\Entity\Node;

class ModelPluginTest extends ModelPluginTestBase
{
    protected function setUp()
    {
        parent::setUp();

        $this->pluginManager->addDefinition();
    }

    public function testLoadBundleModel(): void
    {
        $node = Node::create(['type' => 'node_mock']);

        self::assertInstanceOf(NodeMock::class, $node, 'The entity model does not use the bundle-specific class');
    }
}
