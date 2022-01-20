<?php

namespace Drupal\Tests\wmmodel\Unit;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Tests\UnitTestCase;
use Drupal\wmmodel\Controller\ArgumentResolver\ModelValueResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadataFactory;

class ArgumentResolverTest extends UnitTestCase
{
    /** @var ArgumentResolverInterface */
    protected $argumentResolver;

    protected function setUp()
    {
        parent::setUp();

        $this->argumentResolver = new ArgumentResolver(
            new ArgumentMetadataFactory(),
            [new ModelValueResolver()]
        );
    }

    public function testResolveControllerArguments(): void
    {
        $foo = $this->createMock(MockModel::class);
        $baz = $this->createMock(MockModel::class);
        $mockFormState = $this->createMock(FormStateInterface::class);

        $request = new Request();
        $request->attributes->set('foo_bar', $foo);
        $request->attributes->set('bazQux', $baz);
        $request->attributes->set('form_state', $mockFormState);

        $controllerClass = new class {
            public function show(MockModel $bazQux, MockModel $fooBar, FormStateInterface $formState): void
            {
            }
        };

        $controller = [$controllerClass, 'show'];

        $arguments = $this->argumentResolver->getArguments($request, $controller);

        static::assertSame($baz, $arguments[0]);
        static::assertSame($foo, $arguments[1]);
        static::assertSame($mockFormState, $arguments[2]);
    }
}

class MockModel extends ContentEntityBase
{
}

