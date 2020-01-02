wmmodel
======================

[![Latest Stable Version](https://poser.pugx.org/wieni/wmmodel/v/stable)](https://packagist.org/packages/wieni/wmmodel)
[![Total Downloads](https://poser.pugx.org/wieni/wmmodel/downloads)](https://packagist.org/packages/wieni/wmmodel)
[![License](https://poser.pugx.org/wieni/wmmodel/license)](https://packagist.org/packages/wieni/wmmodel)

> Adds support for bundle-specific models for Drupal 8 entities.

## Why?
- Improve the developer experience of the Entity API by providing the
  ability to add extra methods to entity classes and implement certain
  interfaces:
  - Add getters and setters to make it easier and cleaner to fetch field
    values in business logic and in Twig templates.
    ```php
    <?php

    // This
    $slug = $page->get('field_slug')->first()->getValue();
    // Is moved to a method and becomes this
    $slug = $page->getSlug();
    ```
  -  Use interfaces to abstract certain cross-type features:
     ```php
     <?php
     
     // This
     if ($entity->getEntityTypeId() === 'node' && $entity->bundle() === 'page') {
        return $entity->get('field_slug')->first()->getValue();
     }
     
     if ($entity->getEntityTypeId() === 'taxonomy_term' && $entity->bundle() === 'tag') {
        return $entity->get('field_tag_slug')->first()->getValue();
     }
     
     // Is moved to seperate classes and becomes this
     if ($entity instanceof SluggableEntityInterface) {
        return $entity->getSlug();
     }
     ```
- Drupal does not (yet) provide a way to subclass entities. For more
  information and updates, please refer to the core issue
  ([#2570593](https://www.drupal.org/node/2570593))

## Installation

This package requires PHP 7.1 and Drupal 8 or higher. It can be
installed using Composer:

```bash
 composer require wieni/wmmodel
```

### Patch
For this module to work, it is necessary to patch your Drupal
installation. If you manage your installation with Composer, you should
use the package to manage and automatically apply patches. If not,
please check the [documentation](https://www.drupal.org/patch/apply) for
instructions on how to manually apply patches.

```json
// composer.json
{
    "extra": {
        "composer-exit-on-patch-failure": true,
        "patches": {
            "drupal/core": {
                "The magic behind wmmodel": "https://raw.githubusercontent.com/wieni/wmmodel/0.3.3/src/Patch/core/wmmodel.patch"
            }
        }
    }
}
```

## How does it work?
### Creating models
Models are Drupal plugins with the `@Model` annotation, extending their
entity type class and implementing
`Drupal\wmmodel\Entity\Interfaces\WmModelInterface`. The annotation has
two required parameters, `entity_type` and `bundle`. Classes with this
annotation should be placed in the `Entity` namespace of your module. To
make sure the static `create` method works as expected, you should also
include the `Drupal\wmmodel\Entity\Traits\WmModel` trait in your class.

```php
<?php

namespace Drupal\mymodule\Entity\Node;

use Drupal\node\Entity\Node;
use Drupal\wmmodel\Entity\Interfaces\WmModelInterface;
use Drupal\wmmodel\Entity\Traits\WmModel;

/**
 * @Model(
 *     entity_type = "node",
 *     bundle = "page"
 * )
 */
class Page extends Node implements WmModelInterface
{
    use WmModel;
}
```

To make sure bundles are mapped to the right classes, you can use the
`wmmodel:list` Drush command.

```bash
> drush wmmodel:list
 Model "media.image" is not mapped.
 Model "node.page" is mapped against "Drupal\mymodule\Entity\Node\Page".
```

### Creating and loading entities
Creating and loading entities happens in the same way as before, by
using `Drupal\Core\Entity\EntityTypeManagerInterface`. Additionally, the
static `create` method can be called on model classes without having to
pass the bundle in the values array.

```php
use Drupal\mymodule\Entity\Node\Page;

$page = Page::create();
```

### Controller resolving
If a controller is handling a route with entity parameters, the models
can be automatically injected in the arguments of the controller method
by using the right type hint. This is especially useful in combination
with the [`wmcontroller`](https://github.com/wieni/wmcontroller) module.

```php
<?php

namespace Drupal\mymodule\Controller\Node;

use Drupal\mymodule\Entity\Node\Page;
use Drupal\wmcontroller\Controller\ControllerBase;

class PageController extends ControllerBase
{
    public function show(Page $page)
    {
        return $this->view(
            'node.page.detail',
            ['page' => $page]
        );
    }
}

```

### Injecting the user entity
This module provides an alternative implementation of
`Drupal\Core\Session\AccountProxyInterface` (returned by the
`current_user` service) that makes the `getAccount` method return the
actual User entity instead of an instance of
`Drupal\Core\Session\UserSession`.

## Changelog
All notable changes to this project will be documented in the
[CHANGELOG](CHANGELOG.md) file.

## Security
If you discover any security-related issues, please email
[security@wieni.be](mailto:security@wieni.be) instead of using the issue
tracker.

## License
Distributed under the MIT License. See the [LICENSE](LICENSE.md) file
for more information.
