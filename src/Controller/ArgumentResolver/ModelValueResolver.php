<?php

namespace Drupal\wmmodel\Controller\ArgumentResolver;

use Drupal\wmmodel\Entity\Interfaces\WmModelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class ModelValueResolver implements ArgumentValueResolverInterface
{
    public function supports(Request $request, ArgumentMetadata $argument)
    {
        return is_a($argument->getType(), WmModelInterface::class, true);
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        foreach ($request->attributes->getIterator() as $name => $attribute) {
            if (is_object($attribute) && is_a($attribute, $argument->getType())) {
                yield $request->attributes->get($name);
            }
        }
    }
}
