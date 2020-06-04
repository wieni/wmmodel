<?php

namespace Drupal\Tests\wmmodel\Unit;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Tests\UnitTestCase;
use Drupal\wmmodel\Controller\ArgumentResolver\ModelValueResolver;
use Drupal\wmmodel\Entity\Interfaces\WmModelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadataFactory;

class ArgumentResolverTest extends UnitTestCase
{
    /** @var ArgumentResolverInterface */
    protected $argumentResolver;

    protected function setUp() {
        parent::setUp();

        $this->argumentResolver = new ArgumentResolver(
            new ArgumentMetadataFactory(),
            [new ModelValueResolver()]
        );
    }

    public function testResolveControllerArguments(): void
    {
        $mockEntity = $this->getMockBuilder(MockModel::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockFormState = $this->getMockBuilder(FormStateInterface::class)
            ->getMock();

        $request = Request::create('/');
        $request->attributes->set('foobar', $mockEntity);
        $request->attributes->set('form_state', $mockFormState);
        $controller = [new MockController(), 'show'];

        $arguments = $this->argumentResolver->getArguments($request, $controller);

        $this->assertEquals($mockEntity, $arguments[0]);
        $this->assertEquals($mockFormState, $arguments[1]);
    }
}

class MockModel extends ContentEntityBase implements WmModelInterface
{
}

class MockController
{
    public function show(WmModelInterface $entity, FormStateInterface $formState)
    {
    }
}
