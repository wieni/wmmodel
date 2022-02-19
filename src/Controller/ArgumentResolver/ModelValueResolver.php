<?php

namespace Drupal\wmmodel\Controller\ArgumentResolver;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class ModelValueResolver implements ArgumentValueResolverInterface
{
    /** @var ConfigFactoryInterface */
    protected $configFactory;

    public function __construct(
        ConfigFactoryInterface $configFactory
    ) {
        $this->configFactory = $configFactory;
    }

    public function supports(Request $request, ArgumentMetadata $argument)
    {
        $config = $this->configFactory->get('wmmodel.settings');
        $isEntity = is_a($argument->getType(), ContentEntityInterface::class, true);
        $isFormState = is_a($argument->getType(), FormStateInterface::class, true);

        // We want to typehint $formState instead of $form_state
        // @see https://www.drupal.org/project/drupal/issues/3006502
        if (!empty($config->get('resolve_form_state_argument_type'))) {
            return $isEntity || $isFormState;
        }

        return $isEntity;
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        // Look for an exact match based on type and name
        if ($attribute = $request->attributes->get($argument->getName())) {
            if ($this->isTypeMatch($attribute, $argument)) {
                yield $attribute;

                return;
            }
        }

        // Look for a match based on type and snake cased name
        $snakeName = $this->camelCaseToSnakeCase($argument->getName());
        if ($attribute = $request->attributes->get($snakeName)) {
            if ($this->isTypeMatch($attribute, $argument)) {
                yield $attribute;

                return;
            }
        }

        // Look for a match based on type only
        foreach ($request->attributes->getIterator() as $attribute) {
            if ($this->isTypeMatch($attribute, $argument)) {
                yield $attribute;

                return;
            }
        }

        if ($argument->hasDefaultValue()) {
            yield $argument->getDefaultValue();
        }
    }

    protected function isTypeMatch($attribute, ArgumentMetadata $argument): bool
    {
        return is_object($attribute) && is_a($attribute, $argument->getType());
    }

    protected function camelCaseToSnakeCase(string $string): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
    }
}
