<?php

namespace Drupal\wmmodel\Controller\ArgumentResolver;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use function Symfony\Component\DependencyInjection\Loader\Configurator\iterator;

class ModelValueResolver implements ArgumentValueResolverInterface
{
    public function supports(Request $request, ArgumentMetadata $argument)
    {
        $isArgumentSupported = is_a($argument->getType(), ContentEntityInterface::class, true)
            // We want to typehint $formState instead of $form_state
            // @see https://www.drupal.org/project/drupal/issues/3006502
            || is_a($argument->getType(), FormStateInterface::class, true);

        return $isArgumentSupported && !empty(iterator_to_array($this->resolve($request, $argument)));
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        // First look for an exact match based on type AND name
        if ($attribute = $request->attributes->get($argument->getName())) {
            if ($this->isMatch($attribute, $argument)) {
                yield $attribute;

                return;
            }
        }

        foreach ($request->attributes->getIterator() as $name => $attribute) {
            if ($this->isMatch($attribute, $argument)) {
                yield $attribute;

                return;
            }
        }

        if ($argument->hasDefaultValue()) {
            yield $argument->getDefaultValue();

            return;
        }
    }

    private function isMatch($attribute, ArgumentMetadata $argument): bool
    {
        return is_object($attribute) && is_a($attribute, $argument->getType());
    }
}
