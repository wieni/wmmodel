<?php

namespace Drupal\wmmodel\TwigExtension;


use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Template\TwigEnvironment;

class ModelDropExtension extends \Twig_Extension
{


    public function __construct()
    {

    }

    public function getFilters()
    {
        return [];
    }

    public function getFunctions()
    {
        return [
            'modeldrop' => new \Twig_SimpleFunction(
                'modeldrop',
                [$this, 'modeldrop'],
                [
                    'is_safe' => [
                        'html'
                    ],
                ]
            )
        ];
    }

    /**
     * This is the same name we used on the services.yml file
     *
     * @return string
     */
    public function getName()
    {
        return "wmmodel.model.drop";
    }

    public function modeldrop(EntityInterface $entity, $twig)
    {
        /** @var TwigEnvironment $twigService */
        $twigService = \Drupal::service('twig');
        $context = [];
        $context[$entity->bundle()] = $entity;
        return $twigService->render($twig, $context);
    }
}
