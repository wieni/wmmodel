# Wieni Drupal Models

A convenient way to manage bundle-specific code in Drupal 8.

## Installation

```
composer require wieni\wmmodel
```

Patch your core installation of Drupal 8.

The patch will alter how Drupal creates an instance of your entity so Storages will return your models.

```
// composer.json
{
    ...
    
    "extra": {
        "patches": {
            "drupal/core": "todo: public link to the patch"
        }
    }
}
```

Clear the cache

```
drush cr
```

List all bundles and their model

```
drush model-list
```

## Creating Models

A model has to implement `Drupal\Core\Entity\ContentEntityInterface` and `Drupal\wmmodel\Entity\Interfaces\WmModelInterface`.

It's recommended to extend either `Drupal\node\Entity\Node` or `Drupal\eck\Entity\EckEntity` depending on what kind of model you are making.

Implement `Drupal\wmmodel\Entity\Interfaces\WmModelInterface`. This forces you to define a static method called `getModelInfo()` that will return an array with the `EntityType` and `BundleName` of your model.

The trait `Drupal\wmmodel\Entity\Traits\WmModel` can be used to assist you.

> Follow the `<module>/Entity/<storage>/<bundle>` namespace convention to make your life easier.

### Example model

```
<?php

namespace Drupal\mymodule\Entity\Node;

use Drupal\node\Entity\Node;
use Drupal\wmmodel\Entity\Interfaces\WmModelInterface;
use Drupal\wmmodel\Entity\Traits\WmModel;

class Article extends Node implements WmModelInterface
{
    use WmModel;
}
```

## Mapping

### List all bundles and check if a model exists

Use the drush command `drush model-list` or `drush wml` to list all bundles and their model.

### Registering models
Use the `hook_entity_model_mapping()` hook to map your models.

```
function mymodule_entity_model_mapping()
{
    // Add mappings
    $mapping['node_article'] = \Drupal\mymodule\Entity\Node\Article::class;
    $mapping['subcontent_product'] = \Drupal\mymodule\Entity\Subcontent\Product::class;

    return $mapping;
}
```

When you follow the `<module>/Entity/<storage>/<bundle>` namespace convention you can use a service that will automatically find your models.

```
function mymodule_entity_model_mapping()
{
    $modelFinder = \Drupal::service('wmmodel.service.finder');
    return $modelFinder->findModels('mymodule');
}
```

### Altering mapped models

The `hook_entity_model_mapping_alter` hook can be used to alter registered models by other modules.
 
```
function mymodule_entity_model_mapping_alter(&$mapping)
{
    // Map a bundle "article" with entity type "node" to a custom class
    $mapping['node_article'] = 'Drupal\mymodule\Entity\Node\Article';
}
```

## Cool stuff you should do

### PreSave / PostSave

I don't like having bundle-specific _presave _postsave _predelete stuff in my module files.
It makes more sense to have it at the Model.
 
```
 // Drupal\mymodule\Entity\Node\Article
 
 public function postSave(EntityStorageInterface $storage, $update = true)
 {
    parent::postSave($storage, $update);
    
    // Do whatever you have to do after a save
    // Send events, queue mails, whatever
 }
```

### Custom controllers

Subscribe to `RoutingEvents::ALTER` and alter the `entity.node.canonical` route to use individual controllers per bundle.

This is of course already possible without this module, but it's a fun combination!

```
<?php

namespace Drupal\mymodule\Routing;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Drupal\Core\Routing\RoutingEvents;
use Drupal\Core\Routing\RouteSubscriberBase;


class CoreRoutes extends RouteSubscriberBase
{
    public static function getSubscribedEvents()
    {
        // Default implementation (weight 0) doesn't suffice to
        // overwrite the defaults._controller of entity.taxonomy_term.canonical.
        $events[RoutingEvents::ALTER] = ['onAlterRoutes', -9999];
        return $events;
    }
    
    protected function alterRoutes(RouteCollection $collection)
    {
        $route = $collection->get('entity.node.canonical');
        $defaults = $route->getDefaults();
        $defaults['_controller'] =
            '\\Drupal\\mymodule\\Controller\\FrontController::show';
        
        $route->setDefaults($defaults);
    }
}
```

Then you create a frontcontroller that will delegate to a bundle-specific controller.

```
<?php

namespace Drupal\mymodule\Controller;

use Drupal\Core\Entity\EntityInterface;

class FrontController
{

    /**
     * A frontcontroller
     *
     * @param EntityInterface $node
     * @param string $mode
     * @return mixed
     */
    public function node(EntityInterface $entity, $mode = 'full')
    {
        $entityType = $this->camelize($entity->getEntityType()->id());
        $bundle = $this->camelize($entity->bundle());
        $controller = sprintf(
            'Drupal\\mymodule\\Controller\\%s\\%s',
            $entityType, $bundle
        );
        
        // Instantiate controller
        $controller = $controller::create(\Drupal::getContainer())

        // Call show() method on the controller
        return $controller->show($entity, $mode);
    }

    private function camelize($input, $separator = '_')
    {
        return str_replace($separator, '', ucwords($input, $separator));
    }
}
```

And finally your Bundle-specific controller.

```
<?php

namespace Drupal\mymodule\Controller\Node;

use Drupal\Core\Controller\ControllerBase;

class ArticleController extends ControllerBase
{

    public function show(Article $article)
    {
        // Whatever logic you need to perform in order
        // to render an Article
    }

}
```