<?php

namespace Drupal\wmmodel\Controller;

use Drupal\Core\Controller\ControllerResolver as DrupalControllerResolver;
use Drupal\Core\Routing\RouteMatch;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\wmmodel\Entity\Interfaces\WmModelInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Request;


class ControllerResolver extends DrupalControllerResolver
{

    /**
     * @param Request $request
     * @param callable $controller
     * @param \ReflectionParameter[] $parameters
     * @return array
     */
    protected function doGetArguments(Request $request, $controller, array $parameters)
    {
        $attributes = $request->attributes->all();
        $raw_parameters = $request->attributes->has('_raw_variables') ? $request->attributes->get('_raw_variables') : [];
        $arguments = array();

        $WmModelEnabled = \Drupal::hasService('wmmodel.factory.model');

        foreach ($parameters as $param) {
            if (array_key_exists($param->name, $attributes)) {
                $arguments[] = $attributes[$param->name];
            }
            elseif (array_key_exists($param->name, $raw_parameters)) {
                $arguments[] = $attributes[$param->name];
            }
            elseif ($param->getClass() && $param->getClass()->isInstance($request)) {
                $arguments[] = $request;
            }
            elseif ($param->getClass() && $param->getClass()->name === ServerRequestInterface::class) {
                $arguments[] = $this->httpMessageFactory->createRequest($request);
            }
            elseif ($param->getClass() && ($param->getClass()->name == RouteMatchInterface::class || is_subclass_of($param->getClass()->name, RouteMatchInterface::class))) {
                $arguments[] = RouteMatch::createFromRequest($request);
            }
            elseif ($WmModelEnabled && $this->addModel($arguments, $attributes, $param)) {
                // noop
            }
            elseif ($param->isDefaultValueAvailable()) {
                $arguments[] = $param->getDefaultValue();
            }
            else {
                if (is_array($controller)) {
                    $repr = sprintf('%s::%s()', get_class($controller[0]), $controller[1]);
                }
                elseif (is_object($controller)) {
                    $repr = get_class($controller);
                }
                else {
                    $repr = $controller;
                }

                throw new \RuntimeException(sprintf('Controller "%s" requires that you provide a value for the "$%s" argument (because there is no default value or because there is a non optional argument after this one).', $repr, $param->name));
            }
        }
        return $arguments;
    }

    /**
     * Check whether an object is an instance of or a subclass of a class
     *
     * @param object $object
     * @param string $className
     * @return bool
     */
    protected function isSubclass($object, $className)
    {
        return $className == get_class($object) || is_subclass_of($object, $className);
    }

    /**
     * Check if an attribute is a WmModel
     *
     * @param mixed $object
     * @return bool
     */
    protected function isWmModel($object)
    {
        return is_object($object) && is_subclass_of($object, WmModelInterface::class);
    }

    private function addModel(&$arguments, &$attributes, \ReflectionParameter $param)
    {
        if (!$param->getClass()) {
            return false;
        }

        foreach ($attributes as $key => $attribute) {
            if ($this->isWmModel($attribute) && $this->isSubclass($attribute, $param->getClass()->name)) {
                $arguments[] = $attribute;
                // Remove from attributes so it can't give the same argument multiple times
                unset($attributes[$key]);
                return true;
            }
        }

        return false;
    }
}
